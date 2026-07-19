import re

with open('resources/views/adminNew/dashboard_monev_bosp.blade.php', 'r') as f:
    content = f.read()

# Replace col-md-6 with col-md-12 in both tables
content = content.replace('<!-- Table Lebih -->\n            <div class="col-md-6 mb-4">', '<!-- Table Lebih -->\n            <div class="col-md-12 mb-4">')
content = content.replace('<!-- Table Kurang -->\n            <div class="col-md-6 mb-4">', '<!-- Table Kurang -->\n            <div class="col-md-12 mb-4">')

# Add <th>Aksi</th>
content = content.replace('<th>Selisih</th>\n                                    </tr>', '<th>Selisih</th>\n                                        <th>Aksi</th>\n                                    </tr>')

# Replace TR generation to add the detail button for Lebih
tr_lebih = """<td class="text-warning">+{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                        </tr>"""
tr_lebih_new = """<td class="text-warning">+{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>"""
content = content.replace(tr_lebih, tr_lebih_new)

# Replace TR generation to add the detail button for Kurang
tr_kurang = """<td class="text-danger">{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                        </tr>"""
tr_kurang_new = """<td class="text-danger">{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>"""
content = content.replace(tr_kurang, tr_kurang_new)

# Add Sesuai Table after Kurang Table
kurang_end = """                            </table>
                        </div>
                    </div>
                </div>
            </div>"""

sesuai_table = """                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Sesuai -->
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-success">Daftar Sekolah Data Sesuai (Aktual = BOS)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sekolah</th>
                                        <th>Kabupaten</th>
                                        <th>Pengawas</th>
                                        <th>Selisih</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($monevList as $data)
                                        @if($data->total_siswa_riil == $data->siswa_dinas_bos)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $data->sekolah->nama_sekolah ?? '-' }}</td>
                                            <td>{{ $data->sekolah->kabupaten->nama_kabupaten ?? '-' }}</td>
                                            <td>{{ $data->pengawas->name ?? '-' }}</td>
                                            <td class="text-success">0</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>"""
content = content.replace(kurang_end, sesuai_table, 1)

# Add Modal 
end_section_div = """    </div>
</div>
@endsection"""

modal_html = """    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalTitle">Detail Laporan Monev BOSP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
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
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 mb-2">
                <strong>Catatan Observasi:</strong>
                <p id="det-catatan" class="mt-1 mb-0 border p-2 bg-light rounded" style="min-height: 60px;"></p>
            </div>
            <div class="col-md-12 mt-2">
                <strong>File SPTJM:</strong>
                <div id="det-file-container" class="mt-1">
                    <a href="#" id="det-file" target="_blank" class="btn btn-sm btn-primary"><i class="ti ti-download me-1"></i> Download SPTJM</a>
                    <span id="det-no-file" class="text-muted" style="display:none;">Tidak ada file</span>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection"""
content = content.replace(end_section_div, modal_html)

js_block = """        // Data for Status IJOP (Pie Chart)"""
js_new = """        // Handle View Detail click
        $(document).on('click', '.btn-view-detail', function() {
            const data = $(this).data('info');
            
            $('#det-sekolah').text(data.sekolah);
            $('#det-kabupaten').text(data.kabupaten);
            $('#det-pengawas').text(data.pengawas);
            
            const monthNames = {
                '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
                '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
                '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
            };
            const monthName = monthNames[data.bulan] || data.bulan;
            $('#det-periode').text(monthName + ' ' + data.tahun);
            
            $('#det-ijop').text(data.status_ijop);
            $('#det-riil').text(data.total_siswa_riil);
            $('#det-bos').text(data.siswa_dinas_bos);
            
            // Format currency
            const formatter = new Intl.NumberFormat('id-ID');
            $('#det-realisasi').text(formatter.format(data.realisasi_bosp));
            
            $('#det-k10').text(data.siswa_kelas_10);
            $('#det-k11').text(data.siswa_kelas_11);
            $('#det-k12').text(data.siswa_kelas_12);
            
            $('#det-catatan').text(data.catatan || '-');
            
            if (data.file) {
                $('#det-file').attr('href', data.file).show();
                $('#det-no-file').hide();
            } else {
                $('#det-file').hide();
                $('#det-no-file').show();
            }
            
            $('#detailModal').modal('show');
        });

        // Data for Status IJOP (Pie Chart)"""

content = content.replace(js_block, js_new)

with open('resources/views/adminNew/dashboard_monev_bosp.blade.php', 'w') as f:
    f.write(content)

