<?php

namespace App\Http\Controllers;

use App\Models\UmpanbalikCategory;
use Illuminate\Http\Request;

class UmpanbalikCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = UmpanbalikCategory::where('status', true)->select('id', 'name')->get();
            return response()->json(['categories' => $categories]);
        }

        $categories = UmpanbalikCategory::all();
        return view('admin.umpanbalik.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.umpanbalik.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        UmpanbalikCategory::create($request->all());

        return redirect()->route('umpanbalik.categories.index')->with('success', 'Kategori umpan balik berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UmpanbalikCategory  $umpanbalikCategory
     * @return \Illuminate\Http\Response
     */
    public function show(UmpanbalikCategory $umpanbalikCategory)
    {
        return view('admin.umpanbalik.categories.show', compact('umpanbalikCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UmpanbalikCategory  $umpanbalikCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(UmpanbalikCategory $umpanbalikCategory)
    {
        return view('admin.umpanbalik.categories.edit', compact('umpanbalikCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UmpanbalikCategory  $umpanbalikCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UmpanbalikCategory $umpanbalikCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $umpanbalikCategory->update($request->all());

        return redirect()->route('umpanbalik.categories.index')->with('success', 'Kategori umpan balik berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UmpanbalikCategory  $umpanbalikCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(UmpanbalikCategory $umpanbalikCategory)
    {
        $umpanbalikCategory->delete();

        return redirect()->route('umpanbalik.categories.index')->with('success', 'Kategori umpan balik berhasil dihapus!');
    }
}
