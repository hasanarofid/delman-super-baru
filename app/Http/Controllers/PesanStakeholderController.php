<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesanStakeholder;
use App\Kabupaten;
use Auth;
use DB;

class PesanStakeholderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $queryKabupaten = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'), 'kelompok_kabupaten')
            ->groupBy('nama_kabupaten', 'kelompok_kabupaten');

        if ($user->role == 'Stakeholder' && $user->kabupaten_id) {
            $userKab = Kabupaten::find($user->kabupaten_id);
            if ($userKab) {
                $queryKabupaten->where('kelompok_kabupaten', $userKab->kelompok_kabupaten);
            }
        }
        $kabupatens = $queryKabupaten->get();

        $pesanQuery = PesanStakeholder::with('stakeholder', 'kabupaten')->latest();

        if ($user->role == 'Stakeholder') {
            $pesanQuery->where('stakeholder_id', $user->id);
        }

        $pesans = $pesanQuery->get();

        return view('pesan_stakeholder.index', compact('kabupatens', 'pesans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'isi_pesan' => 'required',
        ]);

        PesanStakeholder::create([
            'stakeholder_id' => $user->id,
            'kabupaten_id' => $request->kabupaten_id ?: null,
            'judul' => $request->judul ?: 'Pesan Stakeholder',
            'isi_pesan' => $request->isi_pesan,
            'is_active' => 1,
        ]);

        return redirect()->route('pesan_stakeholder.index')->with('success', 'Pesan Stakeholder berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $pesan = PesanStakeholder::findOrFail($id);

        $request->validate([
            'isi_pesan' => 'required',
        ]);

        $pesan->update([
            'kabupaten_id' => $request->kabupaten_id ?: null,
            'judul' => $request->judul ?: 'Pesan Stakeholder',
            'isi_pesan' => $request->isi_pesan,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('pesan_stakeholder.index')->with('success', 'Pesan Stakeholder berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pesan = PesanStakeholder::findOrFail($id);
        $pesan->delete();

        return redirect()->route('pesan_stakeholder.index')->with('success', 'Pesan Stakeholder berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $pesan = PesanStakeholder::findOrFail($id);
        $pesan->is_active = !$pesan->is_active;
        $pesan->save();

        return redirect()->route('pesan_stakeholder.index')->with('success', 'Status Pesan Stakeholder berhasil diubah');
    }
}
