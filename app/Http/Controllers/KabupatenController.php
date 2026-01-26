<?php

namespace App\Http\Controllers;

use App\Kabupaten;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class KabupatenController extends Controller
{
    public function index()
    {
        return view('kabupaten.index');
    }

    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            $data = Kabupaten::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $user = Auth::user();
                    if ($user && $user->role == 'Super Admin') {
                        $btn = '<a href="'.route('kabupaten.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <a href="'.route('kabupaten.hapus', $row->id).'" class="btn btn-danger btn-sm deletePost">Delete</a>';
                        return $btn;
                    }
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function add()
    {
        return view('kabupaten.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kabupaten' => 'required|string|max:255',
        ]);

        $kabupaten = new Kabupaten();
        $kabupaten->nama_kabupaten = $request->nama_kabupaten;
        $kabupaten->save();

        return redirect()->route('kabupaten.index')->with('success', 'Kabupaten berhasil ditambahkan');
    }

    public function edit($id)
    {
        $models = Kabupaten::findOrFail($id);
        return view('kabupaten.edit', compact('models'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:master_kabupaten,id',
            'nama_kabupaten' => 'required|string|max:255',
        ]);

        $kabupaten = Kabupaten::find($request->id);
        $kabupaten->nama_kabupaten = $request->nama_kabupaten;
        $kabupaten->save();

        return redirect()->route('kabupaten.index')->with('success', 'Kabupaten berhasil diupdate');
    }

    public function hapus($id)
    {
        $kabupaten = Kabupaten::findOrFail($id);
        $kabupaten->delete();

        return redirect()->back()->with('success', 'Kabupaten berhasil dihapus');
    }
}

