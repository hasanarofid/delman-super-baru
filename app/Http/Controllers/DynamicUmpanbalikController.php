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
    public function showForm($id_category, $generate_url)
    {
        $umpanbalikT = UmpanbalikT::with(['pengawasnama', 'user', 'rencanakerja'])->where('generate_url', $generate_url)->firstOrFail();
        
        // Check if already submitted
        if ($umpanbalikT->submitted_at !== null) {
            $model = $umpanbalikT;
            return view('umpanbalik.done', compact('model'));
        }

        // Group questions by aspect
        $questionsByAspect = UmpanbalikM::where('id_category', $umpanbalikT->id_category)
            ->where('status', true)
            ->orderBy('urutan')
            ->get()
            ->groupBy('aspek');

        return view('umpanbalik.dynamic_form', compact('umpanbalikT', 'questionsByAspect'));
    }

    public function saveForm(Request $request, $generate_url)
    {
        $umpanbalikT = UmpanbalikT::where('generate_url', $generate_url)->firstOrFail();

        // Validasi jawaban sesuai dengan tipe input
        $rules = [
            'tgl_pendampingan' => 'required|date',
        ];
        $questions = UmpanbalikM::where('id_category', $umpanbalikT->id_category)->where('status', true)->orderBy('urutan')->get();

        foreach ($questions as $question) {
            $rule = 'nullable'; // Default rule
            
            // Normalize type_input
            $type = $question->type_input;
            if ($type == 'radiobutton') $type = 'radio';

            if ($type == 'text' || $type == 'textarea') {
                $rule = 'required|string';
            } elseif ($type == 'number') {
                $rule = 'required|numeric';
            } elseif ($type == 'file') {
                $rule = 'nullable|image|max:2048'; // Max 2MB image
            } elseif ($type == 'radio') {
                $options = [];
                if ($question->options && is_array($question->options)) {
                    // Cek jika array numerik atau asosiatif
                    $keys = array_keys($question->options);
                    $isNumeric = true;
                    foreach($keys as $k) if(!is_numeric($k)) $isNumeric = false;
                    
                    $options = $isNumeric ? array_values($question->options) : $keys;
                } elseif ($question->jawaban) {
                    $options = array_map('trim', explode(';', $question->jawaban));
                }
                
                if (!empty($options)) {
                    $rule = 'required|in:' . implode(',', $options);
                } else {
                    $rule = 'required';
                }
            } elseif ($type == 'checkbox') {
                $options = [];
                if ($question->options && is_array($question->options)) {
                    $keys = array_keys($question->options);
                    $isNumeric = true;
                    foreach($keys as $k) if(!is_numeric($k)) $isNumeric = false;
                    
                    $options = $isNumeric ? array_values($question->options) : $keys;
                } elseif ($question->jawaban) {
                    $options = array_map('trim', explode(';', $question->jawaban));
                }

                $rule = 'nullable|array';
                if (!empty($options)) {
                    $rules['answer_' . $question->id . '.*'] = 'in:' . implode(',', $options);
                }
            }
            $rules['answer_' . $question->id] = $rule;
        }

        $validatedData = $request->validate($rules);

        foreach ($questions as $question) {
            $answer_value = $validatedData['answer_' . $question->id] ?? null;

            if (($question->type_input == 'checkbox') && is_array($answer_value)) {
                $answer_value = json_encode($answer_value); // Simpan array checkbox sebagai JSON string
            } elseif ($question->type_input == 'file' && $request->hasFile('answer_' . $question->id)) {
                $image = $request->file('answer_' . $question->id);
                $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('umpanbalik_dynamic', $imageName, 'shared');
                $answer_value = $imageName;
            }
            
            UmpanbalikAnswer::create([
                'id_umpanbalik_t' => $umpanbalikT->id,
                'id_question' => $question->id,
                'answer' => $answer_value,
            ]);
        }

        $umpanbalikT->update([
            'submitted_at' => now(),
            'tgl_pendampingan' => $validatedData['tgl_pendampingan']
        ]);

        return redirect()->route('umpanbalik.done')->with('success', 'Umpan balik Anda telah berhasil dikirim!');
    }

    public static function generateUmpanbalikUrl($id_user, $id_pelaporan, $id_pengawas, $id_category)
    {
        $generate_url = (string) Str::uuid();

        UmpanbalikT::create([
            'id_user' => $id_user,
            'id_pelaporan' => $id_pelaporan,
            'generate_url' => $generate_url,
            'id_pengawas' => $id_pengawas,
            'id_category' => $id_category,
            'tgl_rtl' => date('Y-m-d'), // Added missing tgl_rtl
        ]);

        return route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $generate_url]);
    }

    public function showSuperadminView($category_slug, $generate_url)
    {
        $umpanbalikT = UmpanbalikT::with(['category', 'user', 'pengawasnama', 'rencanakerja', 'answers.question'])
            ->where('generate_url', $generate_url)
            ->firstOrFail();

        $categoryName = $umpanbalikT->category ? $umpanbalikT->category->name : 'N/A';

        // Group questions by aspect for the multi-step view
        $questionsByAspect = UmpanbalikM::where('id_category', $umpanbalikT->id_category)
                                ->orderBy('urutan')
                                ->get()
                                ->groupBy('aspek');

        return view('admin.umpanbalik.dynamic_view', compact('umpanbalikT', 'categoryName', 'questionsByAspect'));
    }
}
