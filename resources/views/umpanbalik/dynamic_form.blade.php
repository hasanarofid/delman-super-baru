@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">Aspek Pelaksanaan Pendampingan</div>
                <div class="card-body">
                    <p>Bagian ini untuk mengetahui pendapat saudara tentang pelaksanaan pendampingan</p>
                    
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('dynamic.umpanbalik.save', $umpanbalikT->generate_url) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @foreach ($questions as $question)
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ $loop->iteration }}. {{ $question->pertanyaan }}</label>
                                @if ($question->type_input == 'radio')
                                    @foreach ($question->options as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer_{{ $question->id }}" id="answer_{{ $question->id }}_{{ $value }}" value="{{ $value }}" {{ old('answer_' . $question->id) == $value ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="answer_{{ $question->id }}_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                @elseif ($question->type_input == 'checkbox')
                                    @foreach ($question->options as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="answer_{{ $question->id }}[]" id="answer_{{ $question->id }}_{{ $value }}" value="{{ $value }}" {{ in_array($value, old('answer_' . $question->id, [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="answer_{{ $question->id }}_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                @elseif ($question->type_input == 'textarea')
                                    <textarea class="form-control" name="answer_{{ $question->id }}" rows="3" placeholder="Masukkan jawaban Anda">{{ old('answer_' . $question->id) }}</textarea>
                                @elseif ($question->type_input == 'number')
                                    <input type="number" class="form-control" name="answer_{{ $question->id }}" value="{{ old('answer_' . $question->id) }}" placeholder="Masukkan angka">
                                @elseif ($question->type_input == 'file')
                                    <input type="file" class="form-control" name="answer_{{ $question->id }}">
                                    @if ($umpanbalikT->getAnswerForQuestion($question->id))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/umpanbalik_dynamic/' . $umpanbalikT->getAnswerForQuestion($question->id)) }}" alt="Foto Terunggah" style="max-width: 200px;">
                                        </div>
                                    @endif
                                @else {{-- Default to text input --}}
                                    <input type="text" class="form-control" name="answer_{{ $question->id }}" value="{{ old('answer_' . $question->id) }}" placeholder="Masukkan jawaban Anda">
                                @endif

                                @error('answer_' . $question->id)
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-success mt-4">Kirim Umpan Balik</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
