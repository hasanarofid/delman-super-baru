<?php

namespace App\Imports;

use App\User;
use App\Profile;
use App\SekolahM;
use App\GuruM;
use App\Models\SekolahbinaanT;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportPengawas implements OnEachRow, SkipsEmptyRows, WithChunkReading, WithEvents
{
    private $currentSupervisorData = null;
    private $currentSchools = [];
    private $kabupaten_id;
    private $rowCounter = 0;

    public function __construct()
    {
        $user_kab_id = Auth::user()->kabupaten_id ?? 1;
        $this->kabupaten_id = ($user_kab_id == 0) ? 1 : $user_kab_id;
        DB::connection()->disableQueryLog();
        Log::info("=== MULAI IMPORT PENGAWAS ===");
        Log::info("Kabupaten ID: " . $this->kabupaten_id);
    }

    public function onRow(Row $row)
    {
        try {
            $rowIndex = $row->getIndex();
            $cells = $row->toArray();

            // Skip row 1 (header with orange background)
            if ($rowIndex === 1) {
                Log::info("Skipping header row 1");
                return;
            }

            $colA = isset($cells[0]) ? trim($cells[0]) : '';
            $colB = isset($cells[1]) ? trim($cells[1]) : '';

            Log::info("Row {$rowIndex} - ColA: '{$colA}' | ColB: '{$colB}'");

            // Process Column A (Supervisor Info)
            if (!empty($colA)) {
                // Check if it's NIP
                if (stripos($colA, 'NIP') !== false || preg_match('/\d{15,}/', $colA)) {
                    if ($this->currentSupervisorData) {
                        $this->currentSupervisorData['nip'] = $this->cleanNip($colA);
                        Log::info("NIP detected: " . $this->currentSupervisorData['nip']);
                    }
                }
                // Check if it's Jabatan
                elseif (stripos($colA, 'Pengawas Sekolah') !== false) {
                    if ($this->currentSupervisorData) {
                        $this->currentSupervisorData['jenjang_jabatan'] = $colA;
                        Log::info("Jabatan detected: {$colA}");
                    }
                }
                // Check if it's Pangkat/Golongan (HARUS ada keyword Pembina/Penata atau format IV/d)
                elseif (stripos($colA, 'Pembina') !== false || 
                        stripos($colA, 'Penata') !== false || 
                        preg_match('/[IV]+\/[a-d]/i', $colA)) {
                    if ($this->currentSupervisorData) {
                        // Parse pangkat dan golongan
                        if (strpos($colA, ',') !== false) {
                            list($pangkat, $gol) = explode(',', $colA, 2);
                            $this->currentSupervisorData['pangkat'] = trim($pangkat);
                            $this->currentSupervisorData['gol_ruang'] = trim($gol);
                        } else {
                            $this->currentSupervisorData['pangkat'] = $colA;
                        }
                        Log::info("Pangkat detected: {$colA}");
                    }
                }
                // It's a new supervisor name
                else {
                    // Skip if it's header-like text
                    if (stripos($colA, 'Nama, NIP') !== false || 
                        stripos($colA, 'Satuan Pendidikan') !== false ||
                        stripos($colA, 'CABANG DINAS') !== false) {
                        Log::info("Skipping header-like text: {$colA}");
                        return;
                    }

                    // Save previous supervisor first
                    if ($this->currentSupervisorData && !empty($this->currentSupervisorData['name'])) {
                        Log::info("Saving previous supervisor before starting new one");
                        $this->processSupervisor($this->currentSupervisorData, $this->currentSchools);
                    }

                    // Start new supervisor
                    $this->currentSupervisorData = [
                        'name' => $colA,
                        'nip' => '',
                        'jenjang_jabatan' => '',
                        'pangkat' => '',
                        'gol_ruang' => '',
                    ];
                    $this->currentSchools = [];
                    Log::info("NEW SUPERVISOR STARTED: {$colA}");
                }
            }

            // Process Column B (School Names)
            if (!empty($colB)) {
                // Skip header
                if (stripos($colB, 'Nama Sekolah') !== false || 
                    stripos($colB, 'Satuan Pendidikan') !== false) {
                    Log::info("Skipping school header: {$colB}");
                    return;
                }

                $schoolName = $this->cleanSchoolName($colB);
                if ($schoolName) {
                    $this->currentSchools[] = $schoolName;
                    Log::info("School added: {$schoolName}");
                }
            }

        } catch (\Exception $e) {
            Log::error("Error on Row {$rowIndex}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                Log::info("=== AFTER SHEET EVENT TRIGGERED ===");
                if ($this->currentSupervisorData && !empty($this->currentSupervisorData['name'])) {
                    Log::info("Processing LAST supervisor: " . $this->currentSupervisorData['name']);
                    Log::info("Supervisor data: " . json_encode($this->currentSupervisorData));
                    Log::info("Schools count: " . count($this->currentSchools));
                    $this->processSupervisor($this->currentSupervisorData, $this->currentSchools);
                } else {
                    Log::warning("No supervisor data to process in AfterSheet");
                    if ($this->currentSupervisorData) {
                        Log::warning("Current data: " . json_encode($this->currentSupervisorData));
                    }
                }
                Log::info("=== SELESAI IMPORT ===");
            },
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function cleanNip($nipString)
    {
        $nip = str_ireplace(['NIP.', 'NIP', ':'], '', $nipString);
        $nip = str_replace([' ', '.', '-'], '', $nip);
        return trim($nip);
    }

    private function cleanSchoolName($schoolString)
    {
        // Remove number prefix like "1. ", "2. ", etc
        $name = preg_replace('/^\d+\.?\s+/', '', $schoolString);
        return trim($name);
    }

    private function processSupervisor($data, $schools)
    {
        if (empty($data['name'])) {
            Log::warning("Skipping: missing name", $data);
            return;
        }
        
        if (empty($data['nip'])) {
            Log::warning("Skipping: missing nip for " . $data['name'], $data);
            return;
        }

        try {
            DB::transaction(function () use ($data, $schools) {
                // Find or create user
                $user = User::where('nip', $data['nip'])->first();
                
                if (!$user) {
                    $user = new User();
                    $user->nip = $data['nip'];
                    $user->password = Hash::make($data['nip']);
                    $user->role = 'Pengawas';
                    $user->foto_profile = 'userdefault.jpg';
                    $user->kabupaten_id = $this->kabupaten_id;
                    $user->email = $data['nip'] . '@mail.com';
                    Log::info("Creating NEW user: " . $data['name']);
                } else {
                    Log::info("Updating EXISTING user: " . $data['name']);
                }

                $user->name = $data['name'];
                $user->jenjang_jabatan = $data['jenjang_jabatan'];
                $user->pangkat = $data['pangkat'];
                $user->gol_ruang = $data['gol_ruang'];
                $user->save();

                // Ensure profile exists
                $profile = Profile::firstOrCreate(['user_id' => $user->id]);

                // Delete old school assignments
                SekolahbinaanT::where('id_pengawas', $user->id)->delete();

                // Process schools
                foreach ($schools as $schoolName) {
                    $sekolah = SekolahM::where('nama_sekolah', $schoolName)->first();
                    
                    if (!$sekolah) {
                        // Create new school
                        $sekolah = new SekolahM();
                        $sekolah->nama_sekolah = $schoolName;
                        $sekolah->kabupaten_id = $this->kabupaten_id;
                        $sekolah->is_aktif = 1;
                        $sekolah->save();

                        // Create default headmaster
                        GuruM::create([
                            'sekolah_id' => $sekolah->id,
                            'nama' => 'Default Kepala Sekolah',
                            'no_telp' => '+62 821-1441-5474',
                            'jabatan' => 'Kepala Sekolah',
                            'kabupaten_id' => $this->kabupaten_id,
                            'is_aktif' => 1
                        ]);
                        
                        Log::info("Created new school with default headmaster: {$schoolName}");
                    }

                    // Link school to supervisor
                    SekolahbinaanT::create([
                        'id_pengawas' => $user->id,
                        'id_sekolah' => $sekolah->id
                    ]);
                }

                Log::info("✅ SUCCESS: Processed supervisor '{$data['name']}' with " . count($schools) . " schools");
            });
            
        } catch (\Exception $e) {
            Log::error("❌ FAILED to process supervisor '{$data['name']}': " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}