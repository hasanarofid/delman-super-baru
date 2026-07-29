<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AspekProgram;
use App\Models\PengaturanAspekStakeholder;
use App\Kabupaten;
use App\User;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Imports\ImportUser;
use App\Exports\ExportUser;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Auth;

class AspekprogramController extends Controller
{
    // index
    public function index()
    {
        return view('aspekprogram.index');
    }

    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            $post = AspekProgram::get();

            return Datatables::of($post)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<span class="badge bg-label-success m-1">Active</span>';
                    } else {
                        $status = '<span class="badge bg-label-danger m-1">InActive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $user = Auth::user();
                    if ($user && in_array($user->role, ['Super Admin', 'Stakeholder'])) {
                        $btn = '<a href="' . route('aspekprogram.edit', $row->id) . '" data-toggle="tooltip" class="edit btn btn-primary btn-sm editPost">Edit</a>';
                        $btn = $btn . ' <a href="' . route('aspekprogram.hapus', $row->id) . '" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal" data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';

                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('aspekprogram.index');
    }

    public function importfile(Request $request)
    {
        Excel::import(new ImportUser, $request->file('file')->store('files'));
        return redirect()->back()->with('success', 'pengawas Import successfully');
    }

    public function excelcontoh(Request $request)
    {
        $models = User::where('role', 'Pengawas')->limit(1)->get();
        $judul = 'Contoh Data pengawas';
        return Excel::download(new ExportUser($models), $judul . '.xlsx');
    }

    /** add data aspek program */
    public function add()
    {
        return view('aspekprogram.add');
    }

    public function import()
    {
        return view('pengawas.import');
    }

    /** save data aspek program */
    public function store(Request $request)
    {
        $model = new AspekProgram();
        $model->nama = $request->nama;
        $model->status = $request->status;
        $model->save();

        return redirect()->route('aspekprogram.index')->with('success', 'Aspek Program created successfully');
    }

    public function edit($id)
    {
        $models = AspekProgram::where('id', $id)->first();
        return view('aspekprogram.edit', compact('models'));
    }

    public function hapus($id)
    {
        $user = AspekProgram::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Aspek Program deleted successfully');
    }

    public function update(Request $request)
    {
        $model = AspekProgram::where('id', $request->id)->first();
        $model->nama = $request->nama;
        $model->status = $request->status;
        $model->save();

        return redirect()->route('aspekprogram.index')->with('success', 'Aspek Program updated successfully');
    }

    public function getkegiatan(Request $request)
    {
        $search = $request->term;
        $data = AspekProgram::select('kegiatan as text', 'id')
            ->whereNull('id_kegiatan')
            ->where('kegiatan', 'LIKE', "%$search%")
            ->get();

        return response()->json($data);
    }

    /** Pengaturan Buka/Tutup Aspek Pendidikan Periodik oleh Stakeholder */
    public function pengaturan(Request $request)
    {
        $user = Auth::user();

        // Ambil data Kabupaten
        $queryKabupaten = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'), 'kelompok_kabupaten')
            ->groupBy('nama_kabupaten', 'kelompok_kabupaten');

        if ($user->role == 'Stakeholder' && $user->kabupaten_id) {
            $userKab = Kabupaten::find($user->kabupaten_id);
            if ($userKab) {
                $queryKabupaten->where('kelompok_kabupaten', $userKab->kelompok_kabupaten);
            }
        }
        $kabupatens = $queryKabupaten->get();

        // Ambil master Aspek Program
        $aspekPrograms = AspekProgram::where('status', 1)->get();

        // Ambil daftar Pengawas jika filter kabupaten dipilih
        $selectedKabupatenId = $request->get('kabupaten_id');
        $selectedJenjang = $request->get('jenjang');
        $selectedBulan = $request->get('bulan', date('n'));
        $selectedTahun = $request->get('tahun', date('Y'));

        $pengawasQuery = User::where('role', 'Pengawas');
        if ($selectedKabupatenId) {
            $pengawasQuery->where('kabupaten_id', $selectedKabupatenId);
        } elseif ($user->role == 'Stakeholder' && $user->kabupaten_id) {
            $userKab = Kabupaten::find($user->kabupaten_id);
            if ($userKab) {
                $kabIds = Kabupaten::where('kelompok_kabupaten', $userKab->kelompok_kabupaten)->pluck('id');
                $pengawasQuery->whereIn('kabupaten_id', $kabIds);
            }
        }
        if ($selectedJenjang) {
            $pengawasQuery->where(function($q) use ($selectedJenjang) {
                $q->where('akses_jenjang', 'LIKE', '%"'.$selectedJenjang.'"%')
                  ->orWhere('akses_jenjang', 'LIKE', '%"All"%');
            });
        }
        $pengawases = $pengawasQuery->orderBy('name')->get();

        // Ambil data pengaturan yang sudah ada untuk filter ini
        $pengaturanExisting = PengaturanAspekStakeholder::where(function ($q) use ($selectedKabupatenId, $selectedJenjang, $selectedBulan, $selectedTahun) {
            if ($selectedKabupatenId) {
                $q->where('kabupaten_id', $selectedKabupatenId);
            }
            if ($selectedJenjang) {
                $q->where('jenjang', $selectedJenjang);
            }
            if ($selectedBulan) {
                $q->where('bulan', $selectedBulan);
            }
            if ($selectedTahun) {
                $q->where('tahun', $selectedTahun);
            }
        })->get()->keyBy(function ($item) {
            $pengawasKey = $item->pengawas_id ?: 'global';
            return $item->aspek_program_id . '_' . $pengawasKey;
        });

        $listJenjang = ['SMA', 'SMK', 'SLB', 'SD', 'SMP'];

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 2);

        return view('aspekprogram.pengaturan', compact(
            'kabupatens',
            'aspekPrograms',
            'pengawases',
            'listJenjang',
            'selectedKabupatenId',
            'selectedJenjang',
            'selectedBulan',
            'selectedTahun',
            'pengaturanExisting',
            'months',
            'years'
        ));
    }

    /** Simpan Pengaturan Buka/Tutup Aspek */
    public function storePengaturan(Request $request)
    {
        $user = Auth::user();
        $kabupaten_id = $request->input('kabupaten_id');
        $jenjang = $request->input('jenjang');
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $pengawas_id = $request->input('pengawas_id'); // null jika berlaku semua
        $aspek_statuses = $request->input('aspek_status', []); // Array [aspek_id => 1 or 0]

        if (empty($aspek_statuses)) {
            return redirect()->back()->with('error', 'Tidak ada data aspek yang diproses.');
        }

        foreach ($aspek_statuses as $aspek_id => $is_active) {
            PengaturanAspekStakeholder::updateOrCreate(
                [
                    'kabupaten_id' => $kabupaten_id ?: null,
                    'pengawas_id' => $pengawas_id ?: null,
                    'jenjang' => $jenjang ?: null,
                    'aspek_program_id' => $aspek_id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'stakeholder_id' => $user->id,
                    'is_active' => $is_active ? 1 : 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan aspek berhasil disimpan.');
    }
}
