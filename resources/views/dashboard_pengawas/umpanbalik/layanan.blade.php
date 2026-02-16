@extends('layouts.pengawas.home')
@section('title', 'List Layanan yang dibutuhkan')
@section('titelcard', 'List Layanan yang dibutuhkan')
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
              <h5 class="m-0 me-2">Tabel Layanan yang dibutuhkan</h5>
            </div>

          
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
  
              
              <div class="app-card-body px-4 w-100">
                <div class="row mb-3">
                  <div class="col-md-4">
                      <a href="#" id="downloadPDF" class="btn btn-danger">Download PDF</a>
                  </div>
                </div>
              
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th>No</th>
                                <th>Sekolah</th> 
                                <th>Layanan yang dibutuhkan</th>
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
            url: "{{ route('pengawas.layanandibutuhkan.getdata') }}"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'layanan', name: 'layanan'},
        ],
    });

    $('#downloadPDF').click(function (event) {
            event.preventDefault(); 
            var searchQuery = $('#dataTable').DataTable().search();
            var url = "{{ route('pengawas.layanandibutuhkan.exportPDF') }}";
            url += `?search=${searchQuery}`;
            window.open(url, '_blank');
        });
  });


  
</script>

@endsection
