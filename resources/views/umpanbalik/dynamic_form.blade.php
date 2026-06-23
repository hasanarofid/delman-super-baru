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

    <title>Umpan Balik | Delman Super</title>

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

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/pages/cards-advance.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('theme/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <style>
        /* CSS untuk membuat logo menjadi terpusat di dalam header */
.navbar-brand {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

@media (max-width: 1199.98px) {
    .navbar-brand {
        position: static;
        transform: none;
    }
}
.hide {
    display: none !important;
}
.required {
    color: red;
    font-weight: bold;
}

.validate-card.invalid {
    border: 2px solid red;
    background-color: #ffe5e5;
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
              <span class="app-brand-text demo menu-text fw-bold">Delman Super | Umpan Balik</span>
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
              <h3 class="text-center">Umpan Balik Pelaksanaan Pengawasan / Supervisi Pengawas Sekolah </h3>

                <div class="container ">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                        <form id="multiStepForm" action="{{ route('dynamic.umpanbalik.save', $umpanbalikT->generate_url) }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          
                          {{-- Step 0: Initial Info (Same as Default) --}}
                          <div id="step0" class="formStep">
                            <div class="card">
                              <div class="card-body">
                                <div class="form-group">
                                    <label for="name1">Nama Pengawas</label>
                                    <input type="text" value="{{ $umpanbalikT->pengawasnama->name ?? 'Pengawas' }}" disabled class="form-control" id="name1">
                                </div>
                              </div>
                            </div>
                              <br>
                              <div class="card">
                              <div class="card-body">
                              <div class="form-group">
                                  <label for="tgl_pendampingan">Tanggal Kedatangan Pengawas <span class="required">*</span></label>
                                  <input type="date" required name="tgl_pendampingan" id="tgl_pendampingan" class="form-control" >
                              </div>
                              </div>
                            </div>
                          </div>

                          {{-- Dynamic Steps based on Aspects --}}
                          @php $stepIndex = 1; @endphp
                          @foreach($questionsByAspect as $aspectName => $questions)
                          <div id="step{{ $stepIndex }}" class="formStep" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-primary ">
                                    <h5 class="card-title text-white">{{ $aspectName ?: 'Aspek Pelaksanaan Pengawasan' }}</h5>
                                </div>
                                <div class="card-body">
                                  <br>
                                    <p class="card-text">Bagian ini untuk mengetahui pendapat saudara tentang aspek ini</p>
                                </div>
                            </div>
                            <br>
                              @foreach($questions as $question)
                              <div class="card validate-card">
                                <div class="card-body">
                                  <div class="form-group">
                                      <label class="fw-bold">{{ $loop->iteration }}. {{ $question->pertanyaan }} <span class="required">*</span></label>
                                      
                                      @php
                                          $options = [];
                                          if ($question->options && is_array($question->options)) {
                                              $options = $question->options;
                                          } elseif ($question->jawaban) {
                                              $options = array_map('trim', explode(';', $question->jawaban));
                                          }
                                      @endphp

                                      @if ($question->type_input == 'radio' || $question->type_input == 'radiobutton')
                                          @foreach ($options as $value => $label)
                                              @php 
                                                  // Handle both associative and numeric arrays
                                                  $val = is_numeric($value) ? $label : $value;
                                                  $lbl = $label;
                                              @endphp
                                              <div class="form-check">
                                                  <input class="form-check-input" type="radio" name="answer_{{ $question->id }}" id="answer_{{ $question->id }}_{{ $val }}" value="{{ $val }}" required>
                                                  <label class="form-check-label" for="answer_{{ $question->id }}_{{ $val }}">
                                                      {{ $lbl }}
                                                  </label>
                                              </div>
                                          @endforeach
                                      @elseif ($question->type_input == 'checkbox')
                                          @foreach ($options as $value => $label)
                                              @php 
                                                  $val = is_numeric($value) ? $label : $value;
                                                  $lbl = $label;
                                              @endphp
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" name="answer_{{ $question->id }}[]" id="answer_{{ $question->id }}_{{ $val }}" value="{{ $val }}">
                                                  <label class="form-check-label" for="answer_{{ $question->id }}_{{ $val }}">
                                                      {{ $lbl }}
                                                  </label>
                                              </div>
                                          @endforeach
                                      @elseif ($question->type_input == 'textarea')
                                          <textarea class="form-control" name="answer_{{ $question->id }}" rows="3" placeholder="Masukkan jawaban Anda" required></textarea>
                                      @elseif ($question->type_input == 'number')
                                          <input type="number" class="form-control" name="answer_{{ $question->id }}" placeholder="Masukkan angka" required>
                                      @elseif ($question->type_input == 'file')
                                          <input type="file" class="form-control" name="answer_{{ $question->id }}" required accept="image/*">
                                      @else {{-- Default to text input --}}
                                          <input type="text" class="form-control" name="answer_{{ $question->id }}" placeholder="Masukkan jawaban Anda" required>
                                      @endif
                                  </div>
                                </div>
                              </div>
                              <br>
                              @endforeach
                          </div>
                          @php $stepIndex++; @endphp
                          @endforeach

                          <div class="row mt-3">
                            <div class="col text-left">
                                <button type="button" class="btn btn-primary" id="prevBtn" >Previous</button>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                                <button type="submit" class="btn btn-success" id="submitBtn" >Submit</button>
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
    <script src="{{ asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('theme/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script>
$(document).ready(function() {
    var currentStep = 0;
    var totalSteps = $('.formStep').length;

    updateButtons();

    $('#nextBtn').click(function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps - 1) {
                $('.formStep').eq(currentStep).hide();
                currentStep++;
                $('.formStep').eq(currentStep).show();
                updateButtons();
            }
        } else {
            alert("Harap isi semua kolom yang bertanda (*)!");
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
            $('#submitBtn').removeClass('hide');
        } else {
            $('#nextBtn').removeClass('hide');
            $('#submitBtn').addClass('hide');
        }
    }

    function validateStep(step) {
        let isValid = true;
        const currentStepEl = $('.formStep').eq(step);
        
        const inputs = currentStepEl.find('input[required], textarea[required], select[required]');
        inputs.each(function() {
            if ($(this).attr('type') === 'radio') {
                const name = $(this).attr('name');
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    isValid = false;
                    $(this).closest('.validate-card').addClass('invalid');
                }
            } else if (!$(this).val()) {
                $(this).addClass('is-invalid');
                $(this).closest('.validate-card').addClass('invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                $(this).closest('.validate-card').removeClass('invalid');
            }
        });

        return isValid;
    }

    $(document).on('input change', '.is-invalid, .invalid, input, textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).closest('.validate-card').removeClass('invalid');
    });

    $('#multiStepForm').submit(function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
            alert('Harap isi semua kolom yang wajib diisi!');
            return false;
        }
        $(this).find('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...');
    });
});
    </script>
  </body>
</html>
