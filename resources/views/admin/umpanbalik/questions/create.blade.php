@extends('layouts.admin.home')

@section('title', 'Tambah Pertanyaan Umpan Balik')
@section('titelcard', 'Tambah Pertanyaan Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Tambah Pertanyaan Umpan Balik</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('umpanbalik.questions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="id_category" class="form-label">Kategori</label>
                    <select class="form-control @error('id_category') is-invalid @enderror" id="id_category" name="id_category" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('id_category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('id_category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pertanyaan" class="form-label">Pertanyaan</label>
                    <textarea class="form-control @error('pertanyaan') is-invalid @enderror" id="pertanyaan" name="pertanyaan" rows="3" required>{{ old('pertanyaan') }}</textarea>
                    @error('pertanyaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="type_input" class="form-label">Tipe Input</label>
                    <select class="form-control @error('type_input') is-invalid @enderror" id="type_input" name="type_input" required>
                        <option value="">Pilih Tipe Input</option>
                        <option value="text" {{ old('type_input') == 'text' ? 'selected' : '' }}>Teks Singkat</option>
                        <option value="textarea" {{ old('type_input') == 'textarea' ? 'selected' : '' }}>Teks Panjang</option>
                        <option value="radiobutton" {{ old('type_input') == 'radiobutton' ? 'selected' : '' }}>Pilihan Tunggal (Radio)</option>
                        <option value="checkbox" {{ old('type_input') == 'checkbox' ? 'selected' : '' }}>Pilihan Ganda (Checkbox)</option>
                        <option value="number" {{ old('type_input') == 'number' ? 'selected' : '' }}>Angka</option>
                        <option value="file" {{ old('type_input') == 'file' ? 'selected' : '' }}>File (Foto)</option>
                    </select>
                    @error('type_input')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="options-group" style="display: {{ old('type_input') == 'radiobutton' || old('type_input') == 'checkbox' ? 'block' : 'none' }};">
                    <label for="options" class="form-label">Opsi Jawaban (pisahkan dengan koma)</label>
                    <input type="text" class="form-control @error('options') is-invalid @enderror" id="options" name="options" value="{{ old('options') }}">
                    <small class="form-text text-muted">Contoh: Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang</small>
                    @error('options')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

               

                <div class="mb-3">
                    <label for="urutan" class="form-label">Urutan</label>
                    <input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan') }}" required>
                    @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input @error('status') is-invalid @enderror" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Aktif</label>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('umpanbalik.questions.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

@section('script')
<script>
    $(document).ready(function() {
        const typeInput = $('#type_input');
        const optionsGroup = $('#options-group');

        if (typeInput.length && optionsGroup.length) {
            typeInput.on('change', function() {
                const selectedValue = $(this).val();
                if (selectedValue === 'radiobutton' || selectedValue === 'checkbox') {
                    optionsGroup.show();
                } else {
                    optionsGroup.hide();
                }
            });

            // Trigger change on load if an old value exists
            const initialValue = typeInput.val();
            if (initialValue === 'radiobutton' || initialValue === 'checkbox') {
                optionsGroup.show();
            } else {
                optionsGroup.hide();
            }
        }
    });
</script>
@endsection
@endsection