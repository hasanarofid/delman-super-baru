@extends('layouts.pengawas.home')
@section('title', 'List Saran Perbaikan')
@section('titelcard', 'List Saran Perbaikan')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      @if(Session::has('success'))
      <div class="alert alert-success">
          {{ Session::get('success') }}
      </div>
      {{ Session::forget('success') }}
  @endif
    <div class="col-12 col-lg-12 ">
      <!-- About User -->
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Tabel Saran Perbaikan</h5>
            </div>

          
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
  
              
              <div class="app-card-body px-4 w-100">
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th>No</th>
                                <th>Sekolah</th> 
                                <th>Saran Perbaikan</th>
                            </tr>
                          </thead>
                      </table>
                      <br>
                      
                  </div>
                  <br>
              </div>
          </div>
      </div>
      <!--/ About User -->

      <!--/ Profile Overview -->
  </div>
</div>


    

    <div class="content-backdrop fade"></div>
  </div>
@endsection


@section('script')


<script>
  $(document).ready(function () {
   

$('#dataTable').DataTable({
 
    processing: true,
    serverSide: false,
    ajax: {
            url: "{{ route('pengawas.saranperbaikan.getdata') }}"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'saran_perbaikan', name: 'saran_perbaikan'},
        ],
    });

  });


  
</script>

@endsection
