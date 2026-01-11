@extends('layouts.admin.home')
@section('title', 'Add  Pengawas')
@section('titelcard', 'Add  Pengawas')
@section ('content')
 <div class="container-fluid py-2">
 

       <div class="row g-3">
         <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 p-3">
                     <div class="row">
                     <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Form Add Pengawas </h6>
                     </div>
                     
                     </div>
                  </div>
               <div class="card-body ">
@if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
    {{ Session::forget('success') }}
@endif
              
               @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                     <form class="row g-3" action="{{ route('masterpengawas.store') }}"
                        method="POST"
                        enctype="multipart/form-data">
                     @csrf
                     <div class="form-group">
                              <label for="name">Nama Pengawas</label>
                              <input type="text" class="form-control" name="name" id="name" placeholder="Nama Pengawas" required>
                     </div>

                     
                     <div class="form-group">
                        <label for="nip">NIP</label>
                        <input type="number" class="form-control" name="nip" id="nip" placeholder="NIP" required pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                     </div>
                     <div class="form-group">
                        <label for="name">Jenjang Jabatan </label>
                        <select name="jenjang_jabatan" id="jenjang_jabatan" class="form-control select2" required>
                         
                           <option value="Pengawas Sekolah Utama"> Pengawas Sekolah Utama </option>
                           <option value="Pengawas Sekolah Ahli Madya"> Pengawas Sekolah Ahli Madya </option>
                           <option value="Pengawas Sekolah Ahli Muda"> Pengawas Sekolah Ahli Muda </option>
                        </select>
                     </div>

                     <div class="form-group">
                        <label for="pangkat">Pangkat</label>
                        <select name="pangkat" id="pangkat" class="form-control">
                        
                        </select>
                     </div>

                     <div class="form-group">
                        <label for="gol_ruang">Gol. Ruang</label>
                        <select name="gol_ruang" id="gol_ruang" class="form-control">
                        
                        </select>

                     </div>
                     
                     <div class="form-group">
                              <label for="no_telp">No WA</label>
                              <input type="number" class="form-control" name="no_telp" id="no_telp" placeholder="No Telp/Wa" required> 
                     </div>



                         <div class="form-group">
                              <label for="alamat_lengkap">Alamat</label>
                              <textarea class="form-control" name="alamat_lengkap" id="alamat_lengkap" cols="10" rows="5" required></textarea>
                     </div>
                      <div class="form-group">
                              <label for="kota">Kota</label>
                              <input type="text" class="form-control" name="kota" id="kota" placeholder="Kota">
                     </div>
                     <div class="form-group">
                              <label for="kode_area">Kode Area</label>
                              <input type="number" class="form-control" name="kode_area" id="kode_area" placeholder="Kode Area">
                     </div>
                     <hr>
                     <p>Info Login</p>
                    <div class="form-group">
                              <label for="email">Email</label>
                              <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                     </div>

                       <div class="form-group">
                              <label for="password">Password</label>
                              <input type="password" class="form-control" name="password"  id="password" placeholder="Password" required>
                           </div>

                                                  <div class="form-group">
                              <label for="repeatpassword">Ulangi Password</label>
                              <input type="password" class="form-control" name="repeatpassword"  id="repeatpassword" placeholder="Ulangi Password" required>
                           </div>



                     <button type="submit" class="btn btn-primary me-sm-3 me-1">
                        <i class="fa fa-save"></i>   Save
                        </button>
                    
                  </form>
               </div>
            </div>
         </div>
      </div>
 </div>
@endsection
    @section('script')

         <script>
           jQuery(document).ready(function () {
    // Initialize Select2 for the 'select2' class elements
    jQuery('.select2').select2();

    // Initialize the 'pangkat' select2 element with AJAX
    jQuery('#pangkat').select2({
        ajax: {
            url: "{{ route('masterpengawas.getpangkat') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term, // search term
                    jenjang_jabatan: jQuery('#jenjang_jabatan').val() // Pass selected jenjang_jabatan
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    // Initialize the 'gol_ruang' select2 element with AJAX
    jQuery('#gol_ruang').select2({
        ajax: {
            url: "{{ route('masterpengawas.getRuang') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term, // search term
                    pangkat: jQuery('#pangkat').val() // Pass selected pangkat
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    // Trigger change event for 'jenjang_jabatan' to load initial 'pangkat' options
    jQuery('#jenjang_jabatan').on('change', function() {
        jQuery('#pangkat').val(null).trigger('change'); // Clear and trigger change on 'pangkat'
    });

    // Trigger change event for 'pangkat' to load initial 'gol_ruang' options
    jQuery('#gol_ruang').val(null).trigger('change'); // Clear and trigger change on 'gol_ruang'
});
   
         </script>
       @endsection


