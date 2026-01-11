@section('script')


<script src="{{ asset('theme/assets/js/modal-edit-user.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/3.2.6/js/froala_editor.pkgd.min.js"></script> --}}
{{-- <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert2 -->
<script>
    ClassicEditor
        .create( document.querySelector( '#deskripsi_permasalahan' ) )
        .catch( error => {
            console.error( error );
        } );

      

        function editPerencanaan(id) {
    console.log(id);
    
    // Remove any existing CKEditor instance from the element to prevent errors
    if (ClassicEditor.instances && ClassicEditor.instances['deskripsi_permasalahan_edit']) {
        ClassicEditor.instances['deskripsi_permasalahan_edit'].destroy();
    }

    $.ajax({
        url: '{{ route("pengawas.perencanaan.edit", ":id") }}'.replace(':id', id),
        type: 'GET',
        success: function(response) {
            // Populate the fields in the modal
            $('#editPerencanaan #id').val(response.data.id); 
            $('#editPerencanaan #bulan_edit').val(response.data.bulan); 
            $('#editPerencanaan #tahun_ajaran_edit').val(response.data.tahun_ajaran); 
            $('#editPerencanaan #nama_program_kerja_edit').val(response.data.nama_program_kerja); 
            $('#editPerencanaan #kategoriprogram_id_edit').val(response.data.kategoriprogram_id).trigger('change');
            $('#editPerencanaan #jenisprogram_id_edit').val(response.data.jenisprogram_id).trigger('change');
            $('#editPerencanaan #aspekprogram_id_edit').val(response.data.aspekprogram_id).trigger('change');
            $('#editPerencanaan #id_umpanbalik_category_edit').val(response.data.id_umpanbalik_category).trigger('change');
            
            var selectedValues = response.data.sekolah_id.split(',').map(Number); // Convert string to array of integers
            $('#editPerencanaan #sekolah_id_edit').val(selectedValues).trigger('change');

            // Create CKEditor instance after receiving response data
            ClassicEditor.create(document.querySelector('#deskripsi_permasalahan_edit'))
                .then(editor => {
                    editor.setData(response.data.deskripsi_permasalahan); // Set data from response
                })
                .catch(error => {
                    console.error(error);
                });

            // Show the modal
            $('#editPerencanaan').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}


      
  $(document).ready(function () {
   //   alert(3);
    var table = $('#dataTable').DataTable({
     
        processing: true,
        serverSide: true,
        ajax: "{{ route('pengawas.perencanaan.getdata') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'bulan_tahun', name: 'bulan_tahun'},
            {data: 'nama_program_kerja', name: 'nama_program_kerja'},
            {data: 'nama_kategori', name: 'nama_kategori'},
            {data: 'nama_jenis', name: 'nama_jenis'},
            {data: 'nama_aspek', name: 'nama_aspek'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
           
            {data: 'tanggal', name: 'tanggal'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    

  });

  function deletePerencanaan(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Anda tidak akan dapat mengembalikan data ini!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed, proceed with AJAX delete request
            $.ajax({
                url: '{{ route("pengawas.perencanaan.hapus", ":id") }}'.replace(':id', id),
                type: 'DELETE', // Ensure the request method is DELETE
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                success: function (response) {
                    // Handle success response (e.g., refresh the page)
                    location.reload();
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText);
                }
            });
        }
    });
}
</script>

@endsection