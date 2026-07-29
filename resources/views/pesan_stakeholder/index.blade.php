@extends('layouts.admin.home')
@section('title', 'Kelola Pesan Stakeholder')
@section('titelcard', 'Kelola Pesan Stakeholder untuk Pengawas')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="ti ti-speakerphone text-primary me-2"></i>Pesan / Arahan Stakeholder ke Pengawas
                            </h5>
                            <small class="text-muted">Pesan ini akan tampil sebagai Pop-up pengumuman saat Pengawas membuka sistem/dashboard.</small>
                        </div>
                        <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalTambahPesan">
                            <i class="ti ti-plus me-1"></i> Buat Pesan Baru
                        </button>
                    </div>
                    <div class="card-body mt-3">
                        @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-sm font-weight mb-1">NO</th>
                                        <th class="text-sm font-weight mb-1">JUDUL PESAN</th>
                                        <th class="text-sm font-weight mb-1">ISI PESAN / ARAHAN</th>
                                        <th class="text-sm font-weight mb-1">TARGET WILAYAH</th>
                                        <th width="15%" class="text-sm font-weight mb-1">STATUS</th>
                                        <th width="20%" class="text-sm font-weight mb-1">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pesans as $index => $pesan)
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle"><strong>{{ $pesan->judul }}</strong></td>
                                        <td class="align-middle">{{ Str::limit($pesan->isi_pesan, 150) }}</td>
                                        <td class="align-middle">
                                            @if($pesan->kabupaten)
                                                <span class="badge bg-label-info">{{ $pesan->kabupaten->nama_kabupaten }}</span>
                                            @else
                                                <span class="badge bg-label-secondary">Semua Wilayah</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($pesan->is_active)
                                                <span class="badge bg-label-success">Aktif (Tampil Pop-up)</span>
                                            @else
                                                <span class="badge bg-label-secondary">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('pesan_stakeholder.toggleStatus', $pesan->id) }}" class="btn btn-xs {{ $pesan->is_active ? 'btn-warning' : 'btn-success' }} me-1">
                                                {{ $pesan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </a>
                                            <button class="btn btn-xs btn-info me-1" data-bs-toggle="modal" data-bs-target="#modalEditPesan{{ $pesan->id }}">
                                                Edit
                                            </button>
                                            <a href="{{ route('pesan_stakeholder.destroy', $pesan->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')" class="btn btn-xs btn-danger">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Pesan -->
                                    <div class="modal fade" id="modalEditPesan{{ $pesan->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('pesan_stakeholder.update', $pesan->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Edit Pesan Stakeholder</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Judul Pesan</label>
                                                            <input type="text" name="judul" class="form-control" value="{{ $pesan->judul }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Target Wilayah / Kabupaten</label>
                                                            <select name="kabupaten_id" class="select2 form-select">
                                                                <option value="">-- Semua Wilayah --</option>
                                                                @foreach($kabupatens as $kab)
                                                                    <option value="{{ $kab->id }}" {{ $pesan->kabupaten_id == $kab->id ? 'selected' : '' }}>
                                                                        {{ $kab->nama_kabupaten }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Isi Pesan / Arahan Stakeholder</label>
                                                            <textarea name="isi_pesan" class="form-control" rows="4" required>{{ $pesan->isi_pesan }}</textarea>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activePesan{{ $pesan->id }}" {{ $pesan->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label ms-1" for="activePesan{{ $pesan->id }}">
                                                                Aktifkan pesan (Tampilkan Pop-up ke Pengawas)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada Pesan Stakeholder yang dibuat.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pesan -->
<div class="modal fade" id="modalTambahPesan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pesan_stakeholder.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Buat Pesan Stakeholder Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Pesan</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Pesan Stakeholder Bulan Ini" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Wilayah / Kabupaten</label>
                        <select name="kabupaten_id" class="select2 form-select">
                            <option value="">-- Semua Wilayah --</option>
                            @foreach($kabupatens as $kab)
                                <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Isi Pesan / Arahan Stakeholder</label>
                        <textarea name="isi_pesan" class="form-control" rows="4" placeholder="Tuliskan arahan/pesan untuk pengawas di sini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.select2) {
            $('.select2').select2({
                dropdownParent: $('#modalTambahPesan')
            });
        }
    });
</script>
@endsection
