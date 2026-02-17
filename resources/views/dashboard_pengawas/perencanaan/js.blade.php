@section('script')


    <script src="{{ asset('theme/assets/js/modal-edit-user.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    {{--
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/3.2.6/js/froala_editor.pkgd.min.js"></script> --}}
    {{--
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert2 -->
    <script>
        ClassicEditor
            .create(document.querySelector('#deskripsi_permasalahan'))
            .catch(error => {
                console.error(error);
            });

        function initSelect2() {
            $('.select2-custom').each(function () {
                var $this = $(this);
                var isMultiple = $this.prop('multiple');
                var placeholder = $this.data('placeholder') || 'Select value';

                $this.select2({
                    placeholder: placeholder,
                    allowClear: true,
                    dropdownParent: $this.closest('.modal'),
                    minimumResultsForSearch: isMultiple ? 1 : 10 // Hide search for short single lists
                });
            });
        }

        $(document).ready(function () {
            initSelect2();

            // Store original options for Jenis Program
            const originalJenisOptions = $('#jenisprogram_id').html();
            const originalJenisOptionsEdit = $('#jenisprogram_id_edit').html();

            // Dynamic logic for Add Form
            $('#kategoriprogram_id').on('change', function() {
                handleDynamicLogic($(this).val(), '', originalJenisOptions);
            });

            $('#id_umpanbalik_category').on('change', function() {
                handleFeedbackCategoryLogic($(this).val(), '');
            });

            // Dynamic logic for Edit Form
            $('#kategoriprogram_id_edit').on('change', function() {
                handleDynamicLogic($(this).val(), '_edit', originalJenisOptionsEdit);
            });

            $('#id_umpanbalik_category_edit').on('change', function() {
                handleFeedbackCategoryLogic($(this).val(), '_edit');
            });

            function handleFeedbackCategoryLogic(feedbackId, suffix) {
                let sekolahSelect = $('#sekolah_id' + suffix);
                if (feedbackId != '0' && feedbackId != '') {
                    sekolahSelect.val([]).prop('disabled', true).trigger('change');
                } else {
                    sekolahSelect.prop('disabled', false).trigger('change');
                }
            }

            function handleDynamicLogic(kategoriId, suffix, originalOptions) {
                let jenisSelect = $('#jenisprogram_id' + suffix);
                let umpanbalikSelect = $('#id_umpanbalik_category' + suffix);
                let sekolahSelect = $('#sekolah_id' + suffix);

                // Restore original options first
                jenisSelect.html(originalOptions);

                if (kategoriId == '9' || kategoriId == '10') { // RHK 1 & 2
                    // Filter: Only Pendampingan (1), Monev (2), Bimtek (3)
                    jenisSelect.find('option').each(function() {
                        let val = $(this).val();
                        if (val != '' && val != '1' && val != '2' && val != '3') {
                            $(this).remove();
                        }
                    });
                    umpanbalikSelect.val('0').trigger('change'); // Default
                } else if (kategoriId == '11') { // RHK 3
                    // Filter: Only Rakor (7), Narasumber (8), Kegiatan Lainnya (9)
                    jenisSelect.find('option').each(function() {
                        let val = $(this).val();
                        if (val != '' && val != '7' && val != '8' && val != '9') {
                            $(this).remove();
                        }
                    });
                    umpanbalikSelect.val('2').trigger('change'); // Pengembangan Kompetensi Pengawas
                }
                
                // Re-run feedback logic to ensure school target state is correct
                handleFeedbackCategoryLogic(umpanbalikSelect.val(), suffix);
                
                // Refresh Select2
                jenisSelect.select2({
                    placeholder: jenisSelect.data('placeholder'),
                    allowClear: true,
                    dropdownParent: jenisSelect.closest('.modal')
                });

                // Clear selection if current one is gone
                if (!jenisSelect.find('option:selected').length) {
                    jenisSelect.val('').trigger('change');
                }
            }
        });



        function editPerencanaan(id) {
            console.log(id);

            // Remove any existing CKEditor instance from the element to prevent errors
            if (ClassicEditor.instances && ClassicEditor.instances['deskripsi_permasalahan_edit']) {
                ClassicEditor.instances['deskripsi_permasalahan_edit'].destroy();
            }

            $.ajax({
                url: '{{ route("pengawas.perencanaan.edit", ":id") }}'.replace(':id', id),
                type: 'GET',
                success: function (response) {
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

                    // Re-initialize or refresh select2 after data is loaded
                    initSelect2();
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }



        $(document).ready(function () {
            // Prevent double submission for Create Form
            $('#formAddPerencanaan').on('submit', function () {
                var btn = $('#btnSubmitAdd');
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                return true; // continue with form submission
            });

            // Prevent double submission for Edit Form
            $('#formEditPerencanaan').on('submit', function () {
                var btn = $('#btnSubmitEdit');
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                return true; // continue with form submission
            });

            //   alert(3);
            var table = $('#dataTable').DataTable({

                processing: true,
                serverSide: true,
                ajax: "{{ route('pengawas.perencanaan.getdata') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'bulan_tahun', name: 'bulan_tahun' },
                    { data: 'nama_program_kerja', name: 'nama_program_kerja' },

                    { data: 'nama_jenis', name: 'nama_jenis' },
                    { data: 'nama_aspek', name: 'nama_aspek' },
                    { data: 'nama_sekolah', name: 'nama_sekolah' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'status_wa', name: 'status_wa' },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
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