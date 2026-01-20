<?php

namespace App\Imports;

use App\User;
use App\Profile;
use App\SekolahM;
use App\Models\SekolahbinaanT;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportPengawas implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new SingleSheetImport(),
            1 => new SingleSheetImport(),
        ];
    }
}

class SingleSheetImport implements OnEachRow, SkipsEmptyRows, WithChunkReading, WithEvents
{
    private $currentSupervisorData = null;
    private $currentSchools = [];
    private $kabupaten_id;

    public function __construct()
    {
        $user_kab_id = Auth::user()->kabupaten_id ?? 1;
        $this->kabupaten_id = ($user_kab_id == 0) ? 1 : $user_kab_id;
        
        // Disable query log to save memory
        DB::connection()->disableQueryLog();
        Log::info("Starting Import Sheet with Kabupaten ID: " . $this->kabupaten_id);
    }

    public function onRow(Row $row)
    {
        try {
            $rowIndex = $row->getIndex();
            $cells = $row->toArray();

            // Skip absolute header row
            if ($rowIndex === 1) {
                return;
            }

            $colA = isset($cells[0]) ? trim($cells[0]) : '';
            $colB = isset($cells[1]) ? trim($cells[1]) : '';

            if (!empty($colA)) {
                // Identification logic
                if (stripos($colA, 'NIP.') !== false || preg_match('/\d{8}/', $colA)) {
                    if ($this->currentSupervisorData) {
                        $this->currentSupervisorData['nip'] = $this->cleanNip($colA);
                    }
                } elseif (stripos($colA, 'Pengawas') !== false) {
                    if ($this->currentSupervisorData) {
                        $this->currentSupervisorData['jenjang_jabatan'] = $colA;
                    }
                } elseif (stripos($colA, 'Pembina') !== false || stripos($colA, 'Penata') !== false || strpos($colA, '/') !== false) {
                    if ($this->currentSupervisorData) {
                        if (strpos($colA, ',') !== false) {
                            list($pangkat, $gol) = explode(',', $colA, 2);
                            $this->currentSupervisorData['pangkat'] = trim($pangkat);
                            $this->currentSupervisorData['gol_ruang'] = trim($gol);
                        } else {
                            $this->currentSupervisorData['pangkat'] = $colA;
                        }
                    }
                } elseif (stripos($colA, 'Nama') === false && stripos($colA, 'Satuan') === false && stripos($colA, 'No') === false) {
                    // This is a new supervisor name
                    if ($this->currentSupervisorData) {
                        $this->processSupervisor($this->currentSupervisorData, $this->currentSchools);
                    }

                    $this->currentSupervisorData = [
                        'name' => $colA,
                        'nip' => '',
                        'jenjang_jabatan' => '',
                        'pangkat' => '',
                        'gol_ruang' => '',
                    ];
                    $this->currentSchools = [];
                }
            }

            if (!empty($colB) && stripos($colB, 'Sekolah') === false) {
                $schoolName = $this->cleanSchoolName($colB);
                if ($schoolName) {
                    $this->currentSchools[] = $schoolName;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error on Row " . $row->getIndex() . ": " . $e->getMessage());
            throw $e;
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Save the very last supervisor in the sheet
                if ($this->currentSupervisorData) {
                    $this->processSupervisor($this->currentSupervisorData, $this->currentSchools);
                }
                Log::info("Finished Import Sheet");
            },
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Increase chunk size to speed up processing
    }

    private function cleanNip($nipString)
    {
        $nip = str_ireplace('NIP.', '', $nipString);
        $nip = str_replace([' ', '.'], '', $nip);
        return trim($nip);
    }

    private function cleanSchoolName($schoolString)
    {
        return trim(preg_replace('/^\d+\.?\s+/', '', $schoolString));
    }

    private function processSupervisor($data, $schools)
    {
        if (empty($data['name']) || empty($data['nip'])) {
            Log::warning("Skipping supervisor: missing name or nip", $data);
            return;
        }

        try {
            DB::transaction(function () use ($data, $schools) {
                $user = User::where('nip', $data['nip'])->first();
                if (!$user) {
                    $user = new User();
                    $user->nip = $data['nip'];
                    $user->password = Hash::make($data['nip']);
                    $user->role = 'Pengawas';
                    $user->foto_profile = 'userdefault.jpg';
                    $user->kabupaten_id = $this->kabupaten_id;
                    $user->email = $data['nip'] . '@mail.com';
                }

                $user->name = $data['name'];
                $user->jenjang_jabatan = $data['jenjang_jabatan'];
                $user->pangkat = $data['pangkat'];
                $user->gol_ruang = $data['gol_ruang'];
                $user->save();

                $profile = Profile::where('user_id', $user->id)->first();
                if (!$profile) {
                    $profile = new Profile();
                    $profile->user_id = $user->id;
                    $profile->save();
                }

                SekolahbinaanT::where('id_pengawas', $user->id)->delete();

                foreach ($schools as $schoolName) {
                    $sekolah = SekolahM::where('nama_sekolah', $schoolName)->first();
                    if (!$sekolah) {
                        $sekolah = new SekolahM();
                        $sekolah->nama_sekolah = $schoolName;
                        $sekolah->kabupaten_id = $this->kabupaten_id;
                        $sekolah->is_aktif = 1;
                        $sekolah->save();
                    } else {
                        $sekolah->kabupaten_id = $this->kabupaten_id;
                        $sekolah->save();
                    }

                    $sb = new SekolahbinaanT();
                    $sb->id_pengawas = $user->id;
                    $sb->id_sekolah = $sekolah->id;
                    $sb->save();
                }
            });
            Log::info("Processed supervisor: " . $data['name'] . " with " . count($schools) . " schools");
        } catch (\Exception $e) {
            Log::error("Failed to process supervisor " . $data['name'] . ": " . $e->getMessage());
            throw $e;
        }
    }
}
