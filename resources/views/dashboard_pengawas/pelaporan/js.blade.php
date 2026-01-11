     1|@section('script')
     2|
     3|{{-- <script src="{{ asset('theme/assets/js/modal-edit-user.js') }}"></script> --}}
     4|<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
     5|<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert2 -->
     6|<script>
     7|  $(document).ready(function () {
     8|    // $(".select2").select2();
     9|            ClassicEditor
    10|            .create( document.querySelector('#deskripsi_permasalahan'))
    11|            .catch( error => {
    12|                console.error( error );
    13|            } );
    14|
    15|            ClassicEditor
    16|            .create( document.querySelector('#target_capaian') )
    17|            .catch( error => {
    18|                console.error( error );
    19|            });
    20|
    21|            ClassicEditor
    22|            .create( document.querySelector('#deskripsi_permasalahan_edit'))
    23|            .catch( error => {
    24|                console.error( error );
    25|            } );
    26|
    27|            ClassicEditor
    28|            .create( document.querySelector('#target_capaian_edit') )
    29|            .catch( error => {
    30|                console.error( error );
    31|            });
    32|
    33|            ClassicEditor
    34|            .create( document.querySelector('#catatan_evaluasi') )
    35|            .catch( error => {
    36|                console.error( error );
    37|            });
    38|
    39|            ClassicEditor
    40|            .create( document.querySelector('#saran_rekomendasi') )
    41|            .catch( error => {
    42|                console.error( error );
    43|            });
    44|
    45|   //   alert(3);
    46|    var table = $('#dataTable').DataTable({
    47|     
    48|        processing: true,
    49|        serverSide: true,
    50|        ajax: "{{ route('pengawas.pelaporan.getdata') }}",
    51|        columns: [
    52|          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    53|            {data: 'sasaran', name: 'sasaran'},
    54|            {data: 'judul', name: 'judul'},
    55|            {data: 'nama_kategori', name: 'nama_kategori'},
    56|            {data: 'tgl_pendampingan', name: 'tgl_pendampingan'},
    57|            {data: 'action', name: 'action', orderable: false, searchable: false},
    58|        ]
    59|    });
    60|  });
    61|
    62|  // function lihatPerencanaan(id) {      
    63|  //   $.ajax({
    64|  //       url: '{{ route("pengawas.pelaporan.edit", ":id") }}'.replace(':id', id),
    65|  //       type: 'GET',
    66|  //       success: function(response) {
    67|  //           // Tampilkan data dalam modal
    68|  //           $('#editPerencanaan #id').val(response.id); 
    69|  //           $('#editPerencanaan #tahun_ajaran_edit').val(response.tahun_ajaran); 
    70|  //           $('#editPerencanaan #nama_program_kerja_edit').val(response.nama_program_kerja); 
    71|  //           $('#editPerencanaan #judul_edit').val(response.judul); 
    72|  //           $('#editPerencanaan #kategoriprogram_id').val(response.kategoriprogram_id); 
    73|  //           $('#editPerencanaan #kategoriprogram_id_edit').val(response.kategoriprogram_id).trigger('change');
    74|  //           $('#editPerencanaan #tenggat_waktu_edit').val(response.tenggat_waktu).trigger('change');
    75|  //           var selectedValues = response.sekolah_id.split(',').map(Number); // Ubah string menjadi array integer
    76|  //           $('#editPerencanaan #sekolah_id_edit').val(selectedValues).trigger('change');
    77|           
    78|         
    79|  //           $('#editPerencanaan #deskripsi_permasalahan_edit').val(response.deskripsi_permasalahan);
    80|  //           $('#editPerencanaan #target_capaian_edit').val(response.target_capaian);
    81|
    82|
    83|           
    84|  //           $('#editPerencanaan').modal('show');
    85|  //       },
    86|  //       error: function(xhr, status, error) {
    87|  //           console.error(xhr.responseText);
    88|  //       }
    89|  //   });
    90|  // }
    91|
    92|  function setKategory(id){
    93|    // alert(id);
    94|    $("#id_kategory").val(id);
    95|  }
    96|
   97|
   98|
   99|  function pilihSasaran(obj){
   100|    var sasaran = $(obj).val();
   101|    var program = $("#sub_kategori").val();
   102|    $.ajax({
   103|        url: "{{ route('pengawas.pelaporan.getProgramKerjaSasaran') }}", // Define your route
   104|        type: 'GET',
   105|        data: { 
   106|          sasaran: sasaran,
   107|          program: program
   108|         },
   109|        success: function(response) {
   110|        // Clear and populate the dropdown with options
   111|        $('#object_sasaran').empty();
   112|        $('#object_sasaran').append($('<option>').text('Select').attr('value', ''));
   113|
   114|        // Iterate over each category and append it to the dropdown
   115|        $.each(response.objek, function(category, categoryName) {
   116|            $('#object_sasaran').append($('<option>').text(categoryName).attr('value', category));
   117|        });
   118|
   119|        // Reinitialize Select2
   120|        // $('#sub_kategori').select2();
   121|
   122|
   123|        },
   124|        error: function(xhr, textStatus, errorThrown) {
   125|            console.log('Error:', errorThrown);
   126|        }
   127|    });
   128|   
   129|  }
   130|
   131|  function pilihKategory(obj) {
   132|    var kategoriId = $(obj).val();
   133|    
   134|    // Make AJAX request
   135|    $.ajax({
   136|        url: "{{ route('pengawas.pelaporan.getSubKategori') }}", // Define your route
   137|        type: 'GET',
   138|        data: { kategori_id: kategoriId },
   139|        success: function(response) {
   140|        // Clear and populate the dropdown with options
   141|        $('#sub_kategori').empty();
   142|        $('#sub_kategori').append($('<option>').text('Select').attr('value', ''));
   143|
   144|        // Iterate over each category and append it to the dropdown
   145|        $.each(response.subcategories, function(category, categoryName) {
   146|            $('#sub_kategori').append($('<option>').text(categoryName).attr('value', category));
   147|        });
   148|
   149|        // Reinitialize Select2
   150|        // $('#sub_kategori').select2();
   151|
   152|
   153|        },
   154|        error: function(xhr, textStatus, errorThrown) {
   155|            console.log('Error:', errorThrown);
   156|        }
   157|    });
   158|}   
   159|
   160|function pilihSubKategory(obj) {
   161|    var kategoriIdsub = $(obj).val();
   162|    $.ajax({
   163|        url: "{{ route('pengawas.pelaporan.getProgramKerja') }}", // Define your route
   164|        type: 'GET',
   165|        data: { id: kategoriIdsub },
   166|        success: function(response) {
   167|          
   168|            $('#editUser #deskripsi_permasalahan').val(response.rencana.deskripsi_permasalahan);
   169|            $('#editUser #target_capaian').val(response.rencana.target_capaian);
   170|
   171|
   172|
   173|
   174|        },
   175|        error: function(xhr, textStatus, errorThrown) {
   176|            console.log('Error:', errorThrown);
   177|        }
   178|    });
   179|}
   180|
   181|function deletePelaporan(id) {
   182|    Swal.fire({
   183|        title: 'Apakah Anda yakin?',
   184|        text: 'Anda tidak akan dapat mengembalikan data ini!',
   185|        icon: 'warning',
   186|        showCancelButton: true,
   187|        confirmButtonColor: '#d33',
   188|        cancelButtonColor: '#3085d6',
   189|        confirmButtonText: 'Ya, hapus!',
   190|        cancelButtonText: 'Batal'
   191|    }).then((result) => {
   192|        if (result.isConfirmed) {
   193|            $.ajax({
   194|                url: '{{ route("pengawas.pelaporan.hapus", ":id") }}'.replace(':id', id),
   195|                type: 'GET', // Assuming it's a GET route based on web.php, but DELETE is more appropriate
   196|                headers: {
   197|                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   198|                 },
   199|                success: function (response) {
   200|                    location.reload();
   201|                },
   202|                error: function (xhr, status, error) {
   203|                    console.error(xhr.responseText);
   204|                }
   205|            });
   206|        }
   207|    });
   208|}
   209|</script>
   210|
   211|@endsection
