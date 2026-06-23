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
              <h3 class="text-center">Umpan Balik Pelaksanaan Pengawasan / Supervisi Pengawas Sekolah Provinsi Banten [{{ $categoryName }}]</h3>

              <div class="container">
                <form id="multiStepForm">
                    <!-- Form Step 1: Info Umum -->
                    <div id="form0" class="formStep">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label>Nama Pengawas</label>
                                    <input type="text" value="{{ $umpanbalikT->pengawasnama->name ?? 'N/A' }}" disabled class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Tanggal Kedatangan Pengawas</label>
                                    <input type="date" value="{{ $umpanbalikT->tgl_pendampingan instanceof \Carbon\Carbon ? $umpanbalikT->tgl_pendampingan->format('Y-m-d') : $umpanbalikT->tgl_pendampingan }}" disabled class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Kepala Sekolah / Guru</label>
                                    <input type="text" value="{{ $umpanbalikT->user->nama ?? 'N/A' }}" disabled class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Rencana Kerja</label>
                                    <input type="text" value="{{ $umpanbalikT->rencanakerja->nama_program_kerja ?? 'N/A' }}" disabled class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Tanggal Disubmit</label>
                                    <input type="text" value="{{ $umpanbalikT->submitted_at instanceof \Carbon\Carbon ? $umpanbalikT->submitted_at->format('d M Y H:i:s') : ($umpanbalikT->submitted_at ?? 'Belum Disubmit') }}" disabled class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>URL Umpan Balik</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ route('dynamic.umpanbalik.form', ['id_category' => $umpanbalikT->id_category, 'generate_url' => $umpanbalikT->generate_url]) }}" disabled>
                                        <a href="{{ route('dynamic.umpanbalik.form', ['id_category' => $umpanbalikT->id_category, 'generate_url' => $umpanbalikT->generate_url]) }}" target="_blank" class="btn btn-outline-primary">Buka Link</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Steps for Aspects -->
                    @php $stepIndex = 1; @endphp
                    @foreach($questionsByAspect as $aspect => $aspectQuestions)
                        <div id="form{{ $stepIndex }}" class="formStep" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="card-title text-white">{{ $aspect }}</h5>
                                </div>
                                <div class="card-body">
                                    <br>
                                    <p class="card-text">Detail jawaban untuk aspek {{ $aspect }}</p>
                                </div>
                            </div>
                            <br>
                            @foreach($aspectQuestions as $question)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>{{ $question->urutan }}. {{ $question->pertanyaan }}</label>
                                            @php
                                                $answer = $umpanbalikT->answers->where('id_question', $question->id)->first();
                                                $val = $answer ? $answer->answer : '-';
                                            @endphp

                                            @if($question->type_input === 'radiobutton' || $question->type_input === 'radio')
                                                @php
                                                    $options = [];
                                                    if ($question->options && is_array($question->options)) {
                                                        $options = $question->options;
                                                    } elseif ($question->jawaban) {
                                                        $options = array_map('trim', explode(';', $question->jawaban));
                                                        $options = array_combine($options, $options);
                                                    }
                                                @endphp
                                                @foreach($options as $optValue => $optLabel)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" disabled
                                                            {{ $val == $optValue ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $optLabel }}</label>
                                                    </div>
                                                @endforeach
                                            @elseif($question->type_input === 'checkbox')
                                                @php
                                                    $options = [];
                                                    if ($question->options && is_array($question->options)) {
                                                        $options = $question->options;
                                                    } elseif ($question->jawaban) {
                                                        $options = array_map('trim', explode(';', $question->jawaban));
                                                        $options = array_combine($options, $options);
                                                    }
                                                    $decodedAnswers = json_decode($val, true) ?? [];
                                                @endphp
                                                @foreach($options as $optValue => $optLabel)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" disabled
                                                            {{ in_array($optValue, $decodedAnswers) ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $optLabel }}</label>
                                                    </div>
                                                @endforeach
                                            @elseif($question->type_input === 'file')
                                                <div class="mt-2">
                                                    @if($val && $val !== '-')
                                                        <a href="{{ route('umpanbalik.dynamic.file', $val) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">View File</a>
                                                        <br>
                                                        <img src="{{ route('umpanbalik.dynamic.file', $val) }}" style="max-width: 500px;" class="img-fluid border rounded shadow-sm">
                                                    @else
                                                        <p class="text-muted">Tidak ada file diunggah.</p>
                                                    @endif
                                                </div>
                                            @elseif($question->type_input === 'textarea')
                                                <textarea class="form-control" disabled rows="3">{{ $val }}</textarea>
                                            @else
                                                <input type="text" class="form-control" value="{{ $val }}" disabled>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @php $stepIndex++; @endphp
                    @endforeach

                    <!-- Navigation Buttons -->
                    <div class="row mt-3">
                        <div class="col text-left">
                            <button type="button" class="btn btn-primary" id="prevBtn">Previous</button>
                        </div>
                        <div class="col text-end">
                            <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                            <a href="{{ route('listumpanbalik.index') }}" class="btn btn-secondary ms-2">Close</a>
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
