<?php

namespace App\Http\Controllers;

use App\Models\UmpanbalikT;
use App\Models\UmpanbalikM;
use App\Models\UmpanbalikAnswer;
use App\Models\UmpanbalikCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DynamicUmpanbalikController extends Controller
{
    public function showForm($generate_url)
    {
        $umpanbalikT = UmpanbalikT::where('generate_url', $generate_url)->firstOrFail();
        $category_id = $umpanbalikT->id_category; // Anda perlu menambahkan id_category ke umpanbalik_t
        $questions = UmpanbalikM::where('id_category', $category_id)->where('status', true)->orderBy('urutan')->get();

        return view('umpanbalik.dynamic_form', compact('umpanbalikT', 'questions'));
    }

    public function saveForm(Request $request, $generate_url)
    {
        $umpanbalikT = UmpanbalikT::where('generate_url', $generate_url)->firstOrFail();

        // Validasi jawaban sesuai dengan tipe input
        $rules = [];
        $questions = UmpanbalikM::where('id_category', $umpanbalikT->id_category)->where('status', true)->orderBy('urutan')->get();

        foreach ($questions as $question) {
            $rule = 'nullable'; // Default rule
            if ($question->type_input == 'text' || $question->type_input == 'textarea') {
                $rule = 'required|string';
            } elseif ($question->type_input == 'number') {
                $rule = 'required|numeric';
            } elseif ($question->type_input == 'file') {
                $rule = 'nullable|image|max:2048'; // Max 2MB image
            } elseif ($question->type_input == 'radio') {
                // Untuk radio, pastikan jawaban ada di opsi yang tersedia
                $options_keys = array_keys($question->options); // Ambil key dari array opsi
                $rule = 'required|in:' . implode(',', $options_keys);
            } elseif ($question->type_input == 'checkbox') {
                // Untuk checkbox, jawaban bisa array, dan setiap elemen harus ada di opsi
                $options_keys = array_keys($question->options); // Ambil key dari array opsi
                $rule = 'nullable|array';
                $rules['answer_' . $question->id . '.*'] = 'in:' . implode(',', $options_keys);
            }
            $rules['answer_' . $question->id] = $rule;
        }

        $validatedData = $request->validate($rules);

        foreach ($questions as $question) {
            $answer_value = $validatedData['answer_' . $question->id] ?? null;

            if ($question->type_input == 'checkbox' && is_array($answer_value)) {
                $answer_value = json_encode($answer_value); // Simpan array checkbox sebagai JSON string
            } elseif ($question->type_input == 'file' && $request->hasFile('answer_' . $question->id)) {
                $image = $request->file('answer_' . $question->id);
                $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('umpanbalik_dynamic', $imageName, 'public');
                $answer_value = $imageName;
            }
            
            UmpanbalikAnswer::create([
                'id_umpanbalik_t' => $umpanbalikT->id,
                'id_question' => $question->id,
                'answer' => $answer_value,
            ]);
        }

        $umpanbalikT->update(['submitted_at' => now()]);

        return redirect()->route('umpanbalik.done')->with('success', 'Umpan balik Anda telah berhasil dikirim!');
    }

    // Method untuk menggenerate URL umpan balik dinamis (akan dipanggil dari controller pengawas)
    public static function generateUmpanbalikUrl($id_user, $id_pelaporan, $id_pengawas, $id_category)
    {
        $generate_url = (string) Str::uuid();

        UmpanbalikT::create([
            'id_user' => $id_user,
            'id_pelaporan' => $id_pelaporan,
            'generate_url' => $generate_url,
            'id_pengawas' => $id_pengawas,
            'id_category' => $id_category, // Pastikan kolom ini ada di umpanbalik_t
        ]);

        return route('dynamic.umpanbalik.form', ['generate_url' => $generate_url]);
    }

    public function showSuperadminView($category_slug, $generate_url)
    {
        $umpanbalikT = UmpanbalikT::with(['category', 'user', 'pengawasnama', 'rencanakerja', 'answers.question'])
            ->where('generate_url', $generate_url)
            ->firstOrFail();

        // You might want to compare $category_slug with $umpanbalikT->category->name
        // For now, we'll just pass it through.
        $categoryName = $umpanbalikT->category ? $umpanbalikT->category->name : 'N/A';

        // Get all questions related to this category for the original form structure
        $questions = UmpanbalikM::where('id_category', $umpanbalikT->id_category)
                                ->orderBy('urutan')
                                ->get();

        return view('admin.umpanbalik.dynamic_view', compact('umpanbalikT', 'categoryName', 'questions'));
    }
}
