<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('theme/assets/') }}"
  data-template="horizontal-menu-template-no-customizer">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Umpan Balik view | Delman Super</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('delmansupernew.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/pages/cards-advance.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('theme/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <style>
      .hide {
          display: none !important;
      }
      label {
          font-weight: bold;
      }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->

        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="container-xxl">

            <a href="#" class="app-brand-link ">
              <img src="{{ asset('delmansupernew.png') }}" style="margin-top:-20px"   height="70px" width="70px" alt="Image placeholder" class="">
              <span class="app-brand-text demo menu-text fw-bold">Delman Super | Umpan Balik view</span>
            </a>

          </div>
        </nav>

        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
              <div class="container-xxl d-flex h-100">

              </div>
            </aside>
            <!-- / Menu -->

            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h3 class="text-center">Umpan Balik Pelaksanaan Pengawasan / Supervisi Pengawas Sekolah Provinsi Banten</h3>

              <div class="container">
                <form id="multiStepForm" action="{{ route('kirimumpanbalik') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_umpanbalik" value="{{ $model->id }}">

                    <!-- Form Step 1 -->
                    <div id="form1" class="formStep">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name1">Nama Pengawas</label>
                                    <input type="text" value="{{ $pengawas->name }}" disabled class="form-control" id="name1" placeholder="pengawas">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="email1">Tanggal Kedatangan Pengawas</label>
                                    <input type="date" disabled class="form-control" value="{{ $tangapan->tanggal_kedatangan ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Step 2 (Aspek Pelaksanaan Pengawasan) -->
                    <div id="form2" class="formStep" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 class="card-title text-white">Aspek Pelaksanaan Pengawasan</h5>
                            </div>
                            <div class="card-body">
                                <br>
                                <p class="card-text">Bagian ini untuk mengetahui pendapat saudara tentang pelaksanaan pengawasan</p>
                            </div>
                        </div>
                        <br>
                        @foreach($umpanBalikM as $item)
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>{{ $item->pertanyaan }}</label>
                                        @if($item->type_input === 'radiobutton')
                                            @php
                                                $options = [];
                                                if ($item->options && is_array($item->options)) {
                                                    $options = $item->options;
                                                } elseif ($item->jawaban) {
                                                    $options = explode(';', $item->jawaban);
                                                }
                                            @endphp
                                            @foreach($options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $option }}"
                                                        {{ ($tangapan && $tangapan->{'jawaban_' . $item->urutan} == $option) ? 'checked' : '' }} {{ $tangapan ? 'disabled' : '' }}>
                                                    <label class="form-check-label">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif($item->type_input === 'textarea')
                                            <textarea class="form-control" name="{{ 'jawaban_' . $item->urutan }}" {{ $tangapan ? 'disabled' : '' }}>{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}</textarea>
                                        @else {{-- Default to text input --}}
                                            <input type="text" class="form-control" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}" {{ $tangapan ? 'disabled' : '' }}>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endforeach
                    </div>

                    <!-- Form Step 3 (Aspek Kompetensi Supervisor) -->
                    <div id="form3" class="formStep" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 class="card-title text-white">Aspek Kompetensi Supervisor</h5>
                            </div>
                            <div class="card-body">
                                <br>
                                <p class="card-text">Pada bagian kami ingin mengetahui pendapat saudara perihal aspek kepribadian supervisor</p>
                            </div>
                        </div>
                        <br>
                        @foreach($umpanBalikM2 as $item)
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>{{ $item->pertanyaan }}</label>
                                        @if($item->type_input === 'radiobutton')
                                            @php
                                                $options = [];
                                                if ($item->options && is_array($item->options)) {
                                                    $options = $item->options;
                                                } elseif ($item->jawaban) {
                                                    $options = explode(';', $item->jawaban);
                                                }
                                            @endphp
                                            @foreach($options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $option }}"
                                                        {{ ($tangapan && $tangapan->{'jawaban_' . $item->urutan} == $option) ? 'checked' : '' }} {{ $tangapan ? 'disabled' : '' }}>
                                                    <label class="form-check-label">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif($item->type_input === 'textarea')
                                            <textarea class="form-control" name="{{ 'jawaban_' . $item->urutan }}" {{ $tangapan ? 'disabled' : '' }}>{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}</textarea>
                                        @else {{-- Default to text input --}}
                                            <input type="text" class="form-control" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}" {{ $tangapan ? 'disabled' : '' }}>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endforeach
                    </div>

                    <!-- Form Step 4 (Aspek Lainnya) -->
                    <div id="form4" class="formStep" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 class="card-title text-white">Aspek Lainnya</h5>
                            </div>
                            <div class="card-body">
                                <br>
                                <p class="card-text">Pada bagian ini kami ingin mengetahui saran masukan dan kebutuhan layanan pengawas sekolah di masa mendatang</p>
                            </div>
                        </div>
                        <br>
                        @foreach($umpanBalikM3 as $item)
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>{{ $item->pertanyaan }}</label>
                                        @if($item->type_input === 'radiobutton')
                                            @php
                                                $options = [];
                                                if ($item->options && is_array($item->options)) {
                                                    $options = $item->options;
                                                } elseif ($item->jawaban) {
                                                    $options = explode(';', $item->jawaban);
                                                }
                                            @endphp
                                            @foreach($options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $option }}"
                                                        {{ ($tangapan && $tangapan->{'jawaban_' . $item->urutan} == $option) ? 'checked' : '' }} {{ $tangapan ? 'disabled' : '' }}>
                                                    <label class="form-check-label">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif($item->type_input === 'textarea')
                                            <textarea class="form-control" name="{{ 'jawaban_' . $item->urutan }}" {{ $tangapan ? 'disabled' : '' }}>{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}</textarea>
                                        @else {{-- Default to text input --}}
                                            <input type="text" class="form-control" name="{{ 'jawaban_' . $item->urutan }}" value="{{ $tangapan->{'jawaban_' . $item->urutan} ?? '' }}" {{ $tangapan ? 'disabled' : '' }}>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endforeach

                        <!-- Photo -->
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="foto">Uploaded Foto</label>
                                    <br>
                                    @if($tangapan && $tangapan->foto)
                                    <img id="imagePreview" class="img-fluid" style="max-width: 500px;"
                                    src="{{ route('umpanbalikfoto', $tangapan->foto) }}" alt="Uploaded Foto" />
                                    @else
                                    <p>Tidak ada foto yang diunggah.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="row mt-3">
                        <div class="col text-left">
                            <button type="button" class="btn btn-primary" id="prevBtn">Previous</button>
                        </div>
                        <div class="col text-end">
                            <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                        </div>
                    </div>
                </form>
            </div>


            </div>
            <!--/ Content -->

            <!-- Footer -->
             <footer class="content-footer footer bg-footer-theme">
                <div class="container-xxl">
                    <div
                    class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                    <div>
                        ©
                        <script>
                        document.write(new Date().getFullYear());
                        </script>
                        , made with ❤️ by <a href="{{ route('pengawas.index') }}" target="_blank" class="fw-semibold">Delman Super</a>
                    </div>

                    </div>
                </div>
                </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!--/ Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('theme/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('theme/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->

    <!-- Page JS -->

    <script>
     $(document).ready(function() {
      var currentStep = 0;
    var totalSteps = $('.formStep').length;

    // Initial button state
    updateButtons();

    $('#nextBtn').click(function() {
        if (currentStep < totalSteps - 1) {
            $('.formStep').eq(currentStep).hide();
            currentStep++;
            $('.formStep').eq(currentStep).show();
            updateButtons();
        }
    });

    $('#prevBtn').click(function() {
        if (currentStep > 0) {
            $('.formStep').eq(currentStep).hide();
            currentStep--;
            $('.formStep').eq(currentStep).show();
            updateButtons();
        }
    });

    function updateButtons() {
    if (currentStep === 0) {
        $('#prevBtn').addClass('hide'); 
    } else {
        $('#prevBtn').removeClass('hide'); 
    }

    if (currentStep === totalSteps - 1) {
        $('#nextBtn').addClass('hide'); 
    } else {
        $('#nextBtn').removeClass('hide'); 
    }
}
      });
    </script>
  </body>
</html>
