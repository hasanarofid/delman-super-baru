<!DOCTYPE html>
<html
lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-navbar-fixed layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('theme/assets/') }}"
  data-template="vertical-menu-template-no-customizer"
>
  <head>
    @php
    $profile = App\ProfileMarket::find(1);
@endphp
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>@yield('title') | {{ $profile->title }}</title>
      <meta name="description" content="Pengawas" />

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

    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/select2/select2.css') }}" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/pages/page-profile.css') }}" />
    <!-- Custom CSS for Pengawas -->
    <link rel="stylesheet" href="{{ asset('css/custom-admin.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('theme/assets/vendor/js/helpers.js') }}" ></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('theme/assets/js/config.js') }}" ></script>

    @yield('css')
  </head>

  <body>
     <!-- Layout wrapper -->
     <div class="layout-wrapper layout-content-navbar">
     
        <div class="layout-container">
          <!-- Menu -->
         
          <aside  id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          
            <div class="app-brand demo" style="height: 190px !important;" >
              <a href="{{ route('pengawas.index') }}" class="app-brand-link">
                <img src="{{ asset('delmansupernew.png') }}" height="200px" width="200px" alt="DelmanSuper Logo" class="brand-logo">
              </a>
              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
              </a>
            </div>
  
            {{-- <div class="menu-inner-shadow"></div> --}}
            
            <!-- Brand Info -->
            <div class="brand-info text-center mb-3">
                <h6 class="text-white mb-1" style="font-weight: 600; font-size: 0.9rem;">Sistem Pengawas</h6>
                <small class="text-white-50">DelmanSuper Platform</small>
            </div>

  
            <ul class="menu-inner py-1" style="margin-top: 10px">
              <li class="menu-item {{ ( request()->is('pengawas') || request()->is('editprofile') ) ? 'active' : '' }}">
                <a href="{{ route('pengawas.index') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-user"></i>
                  <div data-i18n="Profile">Profile</div>
                </a>
              </li>

              <li class="menu-item {{ ( request()->is('dashboard') ) ? 'active' : '' }}">
                <a href="{{ route('pengawas.dashboard') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-smart-home"></i>
                  <div data-i18n="Profile">Dashboards</div>
                </a>
              </li>
              <!-- Dashboards -->
              <li class="menu-item {{ (request()->is('sekolahbinaan')) ? 'active' : '' }}">
                <a href="{{ route('pengawas.sekolahbinaan') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
                  <div data-i18n="Data Sekolah Binaan">Data Sekolah Binaan</div>
                </a>
              </li>
              {{-- <!-- Layouts -->
              <li class="menu-item {{ ( request()->is('sekolahbinaan') || request()->is('datapengawas')   ) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
                  <div data-i18n="Master Data">Master Data</div>
                </a>

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
                  <div data-i18n="Master Data">Master Data</div>
                </a>
  
                <ul class="menu-sub">
                  <li class="menu-item {{ (request()->is('sekolahbinaan')) ? 'active' : '' }}">
                    <a href="{{ route('pengawas.sekolahbinaan') }}" class="menu-link">
                      <div data-i18n="Data Sekolah Binaan">Data Sekolah Binaan</div>
                    </a>
                  </li>
                  <li class="menu-item {{ (request()->is('datapengawas')) ? 'active' : '' }}">
                    <a href="{{ route('pengawas.datapengawas') }}" class="menu-link">
                      <div data-i18n="Data Pengawas">Data Pengawas</div>
                    </a>
                  </li>
                </ul>
              </li> --}}
  
              <!-- Apps & Pages -->
              <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Sistem Modip</span>
              </li>
              <li class="menu-item {{ (request()->is('perencanaan')) ? 'active' : '' }}">
                <a href="{{ route('pengawas.perencanaan') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-layout-kanban"></i>
                  <div data-i18n="Perencanaan">Input Rencana Kerja</div>
                </a>
              </li>
              {{-- <li class="menu-item {{ (request()->is('pelaporan')) ? 'active' : '' }}">
                <a href="{{ route('pengawas.pelaporan') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-messages"></i>
                  <div data-i18n="Pelaporan">Pelaporan</div>
                </a>
              </li> --}}
              {{-- <li class="menu-item {{ (request()->is('umpanbalik')) ? 'active' : '' }}">
                <a href="{{ route('pengawas.umpanbalik') }}" class="menu-link">
                  <i class="menu-icon tf-icons ti ti-calendar"></i>
                  <div data-i18n="Umpan Balik">Umpan Balik</div>
                </a>
              </li> --}}

              <li class="menu-item {{ ( request()->is('pengawas/listumpanbalik*') 
                || request()->is('pengawas/dokumentasipendampingan*') 
                 || request()->is('pengawas/saranperbaikan*') 
                  || request()->is('pengawas/layanandibutuhkan*')   ) ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                      <div data-i18n="Master Data">Umpan Balik</div>
                    </a>
        
                    <ul class="menu-sub">
                     
                       
                        <li class="menu-item {{ (request()->is('pengawas/listumpanbalik*')) ? 'active' : '' }}">
                            <a href="{{ route('pengawas.listumpanbalik.index') }}" class="menu-link">
                                {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                                <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                                <div data-i18n="Profile">List Umpan Balik</div>
                            </a>
                        </li>
                        <li class="menu-item {{ (request()->is('pengawas/dokumentasipendampingan*')) ? 'active' : '' }}">
                            <a href="{{ route('pengawas.dokumentasipendampingan.index') }}" class="menu-link">
                                {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                                <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                                <div data-i18n="Profile">Dokumentasi Pendampingan</div>
                            </a>
                        </li>
        
                        <li class="menu-item {{ (request()->is('pengawas/layanandibutuhkan*')) ? 'active' : '' }}">
                            <a href="{{ route('pengawas.layanandibutuhkan.index') }}" class="menu-link">
                                {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                                <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                                <div data-i18n="Profile">Layanan yang dibutuhkan</div>
                            </a>
                        </li>
                        
                    </ul>
                  </li>
              
              <li class="menu-item">
                <a class="menu-link" href="{{ route('pengawas.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                <i class="menu-icon fa fa-sign-out"></i>
                  <div data-i18n="Logout">Logout</div>
             </a>  
             <form id="logout-form2" action="{{ route('pengawas.logout') }}" method="POST" style="display: none;">
              @csrf
           </form>
               
              </li>

  
            </ul>
          </aside>
          <!-- / Menu -->
         
          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
            <nav
              class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
              id="layout-navbar">
              <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                  <i class="ti ti-menu-2 ti-sm"></i>
                </a>
              </div>
  
              <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <!-- Dashboard Pengawas Identifier -->
                <div class="d-flex align-items-center me-3">
                  <div class="badge bg-success me-2">
                    <i class="ti ti-user-check me-1"></i>
                    PENGAWAS
                  </div>
                  <h4 style="margin-top: 15px; margin-bottom: 0;">@yield('titelcard')</h4>
                </div>

                <ul class="navbar-nav flex-row align-items-center ms-auto">
                 
                  @php
                      if(Auth::user()->foto_profile == 'userdefault.jpg'){
                            $foto = asset('userdefault.jpg');
                        }else{
                            $foto =  route('fotopengawas',Auth::user()->foto_profile );
                        }
                  @endphp
  
  
                  <!-- User -->
                  <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                      <div class="avatar avatar-online">
                        <img src="{{ $foto }}" alt class="rounded-circle" />
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item" href="{{ route('pengawas.index') }}">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar avatar-online">
                                <img src="{{ $foto }}" alt class="rounded-circle" />
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                              <small class="text-muted">{{ Auth::user()->role }}</small>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                      </li>

                      <li>
                        <a class="dropdown-item" href="{{ route('pengawas.logout') }}"
                          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                          <i class="ti ti-logout me-2 ti-sm"></i> Log Out
                       </a>  
                       <form id="logout-form" action="{{ route('pengawas.logout') }}" method="POST" style="display: none;">
                        @csrf
                     </form>

                      </li>

                      
                     
                    </ul>
                  </li>
                  <!--/ User -->
                </ul>
              </div>
  
              <!-- Search Small Screens -->
              <div class="navbar-search-wrapper search-input-wrapper d-none">
                <input
                  type="text"
                  class="form-control search-input container-xxl border-0"
                  placeholder="Search..."
                  aria-label="Search..." />
                <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
              </div>
            </nav>
  
            <!-- / Navbar -->
  
            <!-- Content wrapper -->
            @yield('content')
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
            , made with ❤️ by <a href="{{ route('pengawas.index') }}" target="_blank" class="fw-semibold">Sistem Modip</a>
          </div>

        </div>
      </div>
    </footer>
    <!-- / Footer -->
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>
  
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
  
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
      </div>
      <!-- / Layout wrapper -->

    
  
  


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('theme/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('theme/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->
    <script src="{{ asset('theme/assets/vendor/libs/select2/select2.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('theme/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('theme/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('theme/assets/js/tables-datatables-basic.js') }}"></script>
    @yield('script')

  </body>
</html>
