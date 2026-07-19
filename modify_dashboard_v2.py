import re

with open('resources/views/adminNew/dashboard_monev_bosp.blade.php', 'r') as f:
    content = f.read()

# 1. Change Table Titles
content = content.replace(
    '<h6 class="mb-0 text-warning">Daftar Sekolah Data Lebih (Aktual > BOS)</h6>',
    '<h6 class="mb-0 text-warning">Data Aktual Siswa Berlebih</h6>'
)
content = content.replace(
    '<h6 class="mb-0 text-danger">Daftar Sekolah Data Kurang (Aktual < BOS)</h6>',
    '<h6 class="mb-0 text-danger">Data Aktual Siswa Kurang</h6>'
)
content = content.replace(
    '<h6 class="mb-0 text-success">Daftar Sekolah Data Sesuai (Aktual = BOS)</h6>',
    '<h6 class="mb-0 text-success">Data Siswa sesuai</h6>'
)

# 2. Add Download PDF button
header_target = """        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Dashboard /</span> Monev BOSP SMK</h4>
            </div>
        </div>"""

header_new = """        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Dashboard /</span> Monev BOSP SMK</h4>
            </div>
            <div class="col-md-6 text-end">
                <button onclick="window.print()" class="btn btn-danger"><i class="ti ti-file-pdf me-1"></i> Download PDF</button>
            </div>
        </div>"""
if header_target in content:
    content = content.replace(header_target, header_new)
else:
    # Fallback if structure is slightly different
    content = content.replace(
        '<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Monev BOSP SMK</h4>',
        '<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Dashboard /</span> Monev BOSP SMK</h4><button onclick="window.print()" class="btn btn-danger"><i class="ti ti-file-pdf me-1"></i> Download PDF</button></div>'
    )


# 3. Change Modal Structure
modal_old = """      <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Sekolah:</strong> <span id="det-sekolah"></span><br>
                <strong>Kabupaten:</strong> <span id="det-kabupaten"></span><br>
                <strong>Pengawas:</strong> <span id="det-pengawas"></span><br>
                <strong>Periode:</strong> <span id="det-periode"></span>
            </div>
            <div class="col-md-6">
                <strong>Status IJOP:</strong> <span id="det-ijop"></span><br>
                <strong>Total Siswa Riil:</strong> <span id="det-riil"></span><br>
                <strong>Siswa Dinas BOS:</strong> <span id="det-bos"></span><br>
                <strong>Realisasi BOSP:</strong> Rp <span id="det-realisasi"></span>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Siswa Kelas 10:</strong> <span id="det-k10"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 11:</strong> <span id="det-k11"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 12:</strong> <span id="det-k12"></span>
            </div>
        </div>"""

modal_new = """      <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Sekolah:</strong> <span id="det-sekolah"></span><br>
                <strong>Kabupaten:</strong> <span id="det-kabupaten"></span><br>
                <strong>Pengawas:</strong> <span id="det-pengawas"></span><br>
                <strong>Periode:</strong> <span id="det-periode"></span>
            </div>
            <div class="col-md-6">
                <strong>Status IJOP:</strong> <span id="det-ijop"></span><br>
                <div class="mt-2 p-2 rounded" id="det-status-container">
                    <strong>Status Data Siswa:</strong> <span id="det-status-siswa" class="fw-bold"></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mb-2">
            <div class="col-md-4">
                <strong>Siswa Kelas 10:</strong> <span id="det-k10"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 11:</strong> <span id="det-k11"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 12:</strong> <span id="det-k12"></span>
            </div>
        </div>
        <div class="row mb-3 bg-light p-2 rounded align-items-center">
            <div class="col-md-6">
                <strong>Total Siswa Riil:</strong> <span id="det-riil" class="fs-5 fw-bold text-primary"></span>
            </div>
            <div class="col-md-6">
                <strong>Siswa Dinas BOS:</strong> <span id="det-bos" class="fs-5 fw-bold text-primary"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 bg-success text-white rounded">
                    <strong>Realisasi BOSP:</strong> <span class="fs-5 fw-bolder">Rp <span id="det-realisasi"></span></span>
                </div>
            </div>
        </div>"""

content = content.replace(modal_old, modal_new)


# 4. Modify Javascript to calculate status and format
js_old = """            $('#det-riil').text(data.total_siswa_riil);
            $('#det-bos').text(data.siswa_dinas_bos);
            
            // Format currency
            const formatter = new Intl.NumberFormat('id-ID');
            $('#det-realisasi').text(formatter.format(data.realisasi_bosp));"""

js_new = """            $('#det-riil').text(data.total_siswa_riil);
            $('#det-bos').text(data.siswa_dinas_bos);
            
            // Set Status Data Siswa
            let statusText = '';
            let statusClass = '';
            if (parseInt(data.total_siswa_riil) > parseInt(data.siswa_dinas_bos)) {
                statusText = 'Data Aktual Siswa Berlebih';
                statusClass = 'bg-warning bg-opacity-10 text-warning border border-warning';
            } else if (parseInt(data.total_siswa_riil) < parseInt(data.siswa_dinas_bos)) {
                statusText = 'Data Aktual Siswa Kurang';
                statusClass = 'bg-danger bg-opacity-10 text-danger border border-danger';
            } else {
                statusText = 'Data Siswa Sesuai';
                statusClass = 'bg-success bg-opacity-10 text-success border border-success';
            }
            $('#det-status-siswa').text(statusText);
            $('#det-status-container').removeClass().addClass('mt-2 p-2 rounded ' + statusClass);
            
            // Format currency
            const formatter = new Intl.NumberFormat('id-ID');
            $('#det-realisasi').text(formatter.format(data.realisasi_bosp));"""

content = content.replace(js_old, js_new)

# Add media print CSS to hide buttons during printing
style_tag = """@section('style')
<style>
    @media print {
        .btn, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('content')"""

if "@section('content')" in content and "@media print" not in content:
    content = content.replace("@section('content')", style_tag)

with open('resources/views/adminNew/dashboard_monev_bosp.blade.php', 'w') as f:
    f.write(content)

