@extends('layouts.admin.home')
@section('title', 'List Rencana Kerja')
@section('titelcard', 'List Rencana Kerja')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Tabel Rencana Kerja</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    {{-- <a class="btn btn-primary waves-effect waves-light" href="{{ route('admin.add') }}">
                                        <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Rencana Kerja
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="filter-pengawas">Filter by Pengawas:</label>
                                <select
                                id="filter-pengawas"
                                name="pengawas"
                                class="select2 form-select"
                                required
                            >
                                <option value="all">All</option> <!-- Option to show all records -->
                                @foreach ($listPengawas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name.' - '.$item->nip }}</option>
                                @endforeach
                            </select>
                            
                            </div>

                            <div class="col-md-4">
                                <label for="filter-pengawas">Filter Bulan:</label>
                                <select
                                id="filter-bln"
                                name="bln"
                                class="select2 form-select"
                                required
                            >
                                <option value="all">All</option> <!-- Option to show all records -->
                                @foreach($months as $month)
                                    <option value="{{ $month['name'] }}">
                                        {{ $month['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            
                            </div>
                            <div class="col-md-4">
                                <label for="filter-tahun">Filter Tahun:</label>
                                <select
                                    id="filter-tahun"
                                    name="tahun"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pengawas</th>
                                        <th>Bulan - Tahun</th>
                                        <th>Nama Program Kerja</th>
                                        <th>Kategori</th>
                                        <th>Jenis Program</th>
                                        <th>Aspek Raport Pendidikan</th>
                                        <th>Sekolah Sasaran</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Modal -->
<div class="modal fade" id="categorySelectionModal" tabindex="-1" aria-labelledby="categorySelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categorySelectionModalLabel">Pilih Kategori Umpan Balik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendWaWithCategoryForm">
                    @csrf
                    <input type="hidden" id="rencanaKerjaId" name="rencana_kerj-id">
                    <div class="mb-3">
                        <label for="umpanbalikCategorySelect" class="form-label">Kategori:</label>
                        <select class="form-select" id="umpanbalikCategorySelect" name="id_category" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim WA dengan Kategori</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert2 -->
@section('script')
<script>

// function kirimWaBlast(id) {
//     let button = $('#sendWaButton-' + id);  // Reference to the specific button

//     // Disable button and add a loading state
//     button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

//     $.ajax({
//         url: '{{ route("rencanatugas.kirimwa", ":id") }}'.replace(':id', id),
//         type: 'GET',
//         success: function(response) {
//             // Show success message with SweetAlert and re-enable button
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Success',
//                 text: 'WA message sent successfully!',
//                 confirmButtonText: 'OK'
//             }).then(() => {
//                 // Reload DataTable after successful WA blast
//                 $('#data-table').DataTable().ajax.reload();
//             });
//             button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
//         },
//         error: function(xhr, status, error) {
//             console.error(xhr.responseText);
//             // Show error message with SweetAlert and re-enable button
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error',
//                 text: 'Failed to send WA message. Please try again.',
//                 confirmButtonText: 'OK'
//             });
//             button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
//         }
//     });
// }

function kirimWaBlast(id) {
    $('#rencanaKerjaId').val(id);
    $('#categorySelectionModal').modal('show');
    loadUmpanbalikCategories();
}

function loadUmpanbalikCategories() {
    $.ajax({
        url: '{{ route("umpanbalik.categories.index") }}',
        type: 'GET',
        success: function(response) {
            let select = $('#umpanbalikCategorySelect');
            select.empty();
            select.append('<option value="">Pilih Kategori</option>');
            select.append('<option value="0">Umpan Balik Default (Statis)</option>');
            // Assuming response.categories is an array of objects {id: ..., name: ...}
            response.categories.forEach(function(category) {
                select.append('<option value="' + category.id + '">' + category.name + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error("Failed to load categories: " + xhr.responseText);
        }
    });
}

$('#sendWaWithCategoryForm').submit(function(e) {
    e.preventDefault();
    let rencanaKerjaId = $('#rencanaKerjaId').val();
    let selectedCategoryId = $('#umpanbalikCategorySelect').val();
    let button = $('#sendWaButton-' + rencanaKerjaId); // Get the original button

    if (!selectedCategoryId) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih kategori umpan balik.',
            confirmButtonText: 'OK'
        });
        return;
    }

    $('#categorySelectionModal').modal('hide');

    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

    $.ajax({
        url: '{{ route("rencanatugas.kirimwa.withcategory", [":id", ":id_category"]) }}'.replace(':id', rencanaKerjaId).replace(':id_category', selectedCategoryId),
        type: 'GET',
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Pesan Masuk Antrean',
                text: 'Mengikuti ketentuan terbaru dari WhatsApp, notifikasi Anda telah masuk antrean dan akan terkirim secara bertahap dalam kurun waktu 30 menit hingga 1 jam.',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#data-table').DataTable().ajax.reload();
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to send WA message with dynamic feedback. Please try again.',
                confirmButtonText: 'OK'
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        }
    });
});


    $(document).ready(function () {
        $('#filter-pengawas').select2();
        $('#filter-bln').select2();
        $('#filter-tahun').select2();
        var isExporting = false;
        $('#filter-pengawas').change(function () {
    $('#data-table').DataTable().ajax.reload(); // Reload the table when filter changes
});


$('#filter-bln').change(function () {
    $('#data-table').DataTable().ajax.reload(); // Reload the table when filter changes
});


$('#filter-tahun').change(function () {
    $('#data-table').DataTable().ajax.reload(); // Reload the table when filter changes
});

$('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('rencanatugas.getdata') }}",
                data: function(d) {
                         d.pengawas = $('#filter-pengawas').val();
                         d.bln = $('#filter-bln').val();
                         d.tahun = $('#filter-tahun').val();
                 }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'pengawas', name: 'pengawas'},
                {data: 'bulan_tahun', name: 'bulan_tahun'},
                {data: 'nama_program_kerja', name: 'nama_program_kerja'},
                {data: 'nama_kategori', name: 'nama_kategori'},
                {data: 'nama_jenis', name: 'nama_jenis'},
                {data: 'nama_aspek', name: 'nama_aspek'},
                {data: 'nama_sekolah', name: 'nama_sekolah'}, // For display
                {data: 'tanggal', name: 'tanggal'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            dom: 'Bfrtip', // Enables the buttons at the top of the DataTable
            buttons: [
                {
                    extend: 'pdfHtml5',
                    title: 'List Rencana Kerja',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8], 
                         format: {
                            body: function (data, row, column, node) {
                                if (column === 7) { // Adjust index based on your columns
                                    // Collect all data-sekolah2 attributes
                                    let sekolahData = [];
                                    $(node).find('span').each(function() {
                                        // Push each data-sekolah2 into the array
                                        sekolahData.push($(this).data('sekolah2'));
                                    });
                                    // Join them into a single string
                                    return sekolahData.join(', ') || data; // Fallback to data if not found
                                }
                                return data; // Return data as is for other columns
                            }
                        }
                    },
                    customize: function (doc) {
                        doc.styles.tableHeader.alignment = 'left';
                    }
                }
            ]
        });

        

    });
    </script>
            
    
@endsection