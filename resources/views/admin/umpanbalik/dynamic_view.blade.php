@extends('layouts.admin.home')

@section('title', 'Detail Umpan Balik Dinamis')
@section('titelcard', 'Detail Umpan Balik Dinamis')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Umpan Balik Dinamis</h5>
            <a href="{{ route('listumpanbalik.index') }}" class="btn btn-secondary">Kembali ke Daftar Umpan Balik</a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Kategori Umpan Balik:</strong> {{ $categoryName }}
            </div>
            <div class="mb-3">
                <strong>Pengawas:</strong> {{ $umpanbalikT->pengawasnama->name ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Kepala Sekolah:</strong> {{ $umpanbalikT->user->nama ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Rencana Kerja:</strong> {{ $umpanbalikT->rencanakerja->nama_program_kerja ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>URL yang Digenerate:</strong> <a href="{{ route('dynamic.umpanbalik.form', $umpanbalikT->generate_url) }}" target="_blank">{{ $umpanbalikT->generate_url }}</a>
            </div>
            <div class="mb-3">
                <strong>Tanggal Disubmit:</strong> {{ $umpanbalikT->submitted_at ? $umpanbalikT->submitted_at->format('d M Y H:i:s') : 'Belum Disubmit' }}
            </div>

            <h5 class="mt-4">Jawaban Pertanyaan:</h5>
            @if ($umpanbalikT->answers->isEmpty())
                <p>Belum ada jawaban yang disubmit untuk umpan balik ini.</p>
            @else
                @foreach ($questions as $question)
                    <div class="mb-3">
                        <strong>{{ $question->urutan }}. {{ $question->pertanyaan }}</strong><br>
                        @php
                            $answer = $umpanbalikT->answers->where('id_question', $question->id)->first();
                        @endphp
                        @if ($answer)
                            @if ($question->type_input == 'checkbox')
                                @php
                                    $decodedAnswers = json_decode($answer->answer, true);
                                @endphp
                                @if (is_array($decodedAnswers))
                                    <ul>
                                        @foreach ($decodedAnswers as $val)
                                            <li>{{ $val }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>{{ $answer->answer }}</p>
                                @endif
                            @elseif ($question->type_input == 'file')
                                @if ($answer->answer)
                                    <p>File: <a href="{{ asset('storage/umpanbalik_dynamic/' . $answer->answer) }}" target="_blank">{{ $answer->answer }}</a></p>
                                    <img src="{{ asset('storage/umpanbalik_dynamic/' . $answer->answer) }}" alt="Uploaded Photo" style="max-width: 300px;" class="img-fluid mt-2">
                                @else
                                    <p>Tidak ada file diunggah.</p>
                                @endif
                            @else
                                <p>{{ $answer->answer ?? '-' }}</p>
                            @endif
                        @else
                            <p>Tidak ada jawaban.</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

