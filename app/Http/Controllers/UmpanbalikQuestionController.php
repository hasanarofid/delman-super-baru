<?php

namespace App\Http\Controllers;

use App\Models\UmpanbalikM;
use App\Models\UmpanbalikCategory;
use Illuminate\Http\Request;

class UmpanbalikQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = UmpanbalikM::with('category')->get();
        return view('admin.umpanbalik.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = UmpanbalikCategory::all();
        return view('admin.umpanbalik.questions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_category' => 'required|exists:umpanbalik_categories,id',
            'pertanyaan' => 'required|string',
            'type_input' => 'required|string',
            'options' => 'nullable|string',
            'status' => 'boolean',
            'urutan' => 'required|integer',
        ]);

        if (in_array($validatedData['type_input'], ['radiobutton', 'checkbox'])) {
            $validatedData['options'] = array_map('trim', explode(',', $validatedData['options']));
        } else {
            $validatedData['options'] = null;
        }

        UmpanbalikM::create($validatedData);

        return redirect()->route('umpanbalik.questions.index')->with('success', 'Pertanyaan umpan balik berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UmpanbalikM  $umpanbalikM
     * @return \Illuminate\Http\Response
     */
    public function show(UmpanbalikM $umpanbalikQuestion)
    {
        return view('admin.umpanbalik.questions.show', compact('umpanbalikQuestion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UmpanbalikM  $umpanbalikM
     * @return \Illuminate\Http\Response
     */
    public function edit(UmpanbalikM $umpanbalikQuestion)
    {
        $categories = UmpanbalikCategory::all();
        return view('admin.umpanbalik.questions.edit', compact('umpanbalikQuestion', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UmpanbalikM  $umpanbalikM
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UmpanbalikM $umpanbalikQuestion)
    {
        $validatedData = $request->validate([
            'id_category' => 'required|exists:umpanbalik_categories,id',
            'pertanyaan' => 'required|string',
            'type_input' => 'required|string',
            'options' => 'nullable|string',
            'status' => 'boolean',
            'urutan' => 'required|integer',
        ]);

        if (in_array($validatedData['type_input'], ['radiobutton', 'checkbox'])) {
            $validatedData['options'] = array_map('trim', explode(',', $validatedData['options']));
        } else {
            $validatedData['options'] = null;
        }

        $umpanbalikQuestion->update($validatedData);

        return redirect()->route('umpanbalik.questions.index')->with('success', 'Pertanyaan umpan balik berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UmpanbalikM  $umpanbalikM
     * @return \Illuminate\Http\Response
     */
    public function destroy(UmpanbalikM $umpanbalikQuestion)
    {
        $umpanbalikQuestion->delete();

        return redirect()->route('umpanbalik.questions.index')->with('success', 'Pertanyaan umpan balik berhasil dihapus!');
    }
}
