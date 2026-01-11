@extends('layouts.admin.home')
@section('title', 'Set Sekolah Binaan')
@section('titelcard', 'Set Sekolah Binaan')

@section ('content')
<style>
    .select2-selection__choice{
        background-color: #7367f0 !important;
        color: #fff !important;
    }
</style>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
          <div class="col-12">
              <div class="card mb-4">
                  <div class="card-header pb-0 p-3">
                      <div class="row">
                          <div class="col-6 d-flex align-items-center">
                              <h6 class="mb-0">Set Sekolah Binaan  (Total : {{ $total_binaan }} ) </h6>
                          </div>
                     
                      </div>
                  </div>
                  <div class="card-body">
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
                                                <form  action="{{ route('masterpengawas.store_sekolah') }}"
                        method="POST"
                        enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="id_pengawas" id="id_pengawas" value="{{ $models->id }}">
              
                     
                     <div class="form-group mb-3">
                        <label for="sekolah_id">Pilih Sekolah Binaan</label>
                        <div class="mb-2 mt-2">
                            <button type="button" class="btn btn-xs btn-outline-primary" id="select-all">Select All</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="deselect-all">Deselect All</button>
                        </div>
                        <select name="sekolah_id[]" id="sekolah_id" class="form-select select2" multiple>
                            @foreach ($sekolah as $item)
                                <option value="{{ $item->id }}" {{ $binaan->contains('id_sekolah', $item->id) ? 'selected' : '' }}>
                                    {{ $item->nama_sekolah }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan pilihan jika ingin menghapus semua sekolah binaan.</small>
                     </div>
                    


                     <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('masterpengawas.index') }}" class="btn btn-label-secondary">Kembali</a>
                     </div>
                    
                  </form>
               </div>
            </div>
         </div>
      </div>
 </div>
</div>
@endsection
       @section('script')
       <script>
           jQuery(document).ready(function () {
               var $select = jQuery('#sekolah_id').select2({
                   placeholder: '.: Pilih Sekolah Binaan :.',
                   allowClear: true
               });

               jQuery('#select-all').on('click', function() {
                   var allOptions = [];
                   jQuery('#sekolah_id option').each(function() {
                       if (jQuery(this).val()) {
                           allOptions.push(jQuery(this).val());
                       }
                   });
                   $select.val(allOptions).trigger('change');
               });

               jQuery('#deselect-all').on('click', function() {
                   $select.val(null).trigger('change');
               });
           });
       </script>
       @endsection


