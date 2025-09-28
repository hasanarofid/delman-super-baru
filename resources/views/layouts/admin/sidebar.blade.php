<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="height: 190px !important;">
        <a href="{{ route('admin.index') }}" class="app-brand-link">
            <img src="{{ asset('delmansupernew.png') }}" height="200px" width="200px" alt="Image placeholder" class="">
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1" style="margin-top: 10px">
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.index') }}" class="menu-link">
                <i class="menu-icon tf-icons fas fa-home"></i>
                <div data-i18n="Profile">Dashboard</div>
            </a>
        </li>
        @if (Auth::user()->role == 'Super Admin' || Auth::user()->role == 'Stakeholder')

          <!-- master data -->
          <li class="menu-item {{ ( request()->is('superadmin/mastertupoksi*') || request()->is('superadmin/jenisprogram*')  || request()->is('superadmin/aspekprogram*')   ) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
              <div data-i18n="Master Data">Master Data</div>
            </a>

            <ul class="menu-sub">
             
                <li class="menu-item {{ (request()->is('superadmin/mastertupoksi*')) ? 'active' : '' }}">
                    <a href="{{ route('mastertupoksi.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons fas fa-list-alt"></i>
                        <div data-i18n="Profile">Kategori Program</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->is('superadmin/jenisprogram*')) ? 'active' : '' }}">
                    <a href="{{ route('jenisprogram.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons fas fa-table"></i>
                        <div data-i18n="Profile">Jenis Program</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->is('superadmin/aspekprogram*')) ? 'active' : '' }}">
                    <a href="{{ route('aspekprogram.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons fas fa-list"></i>
                        <div data-i18n="Profile">Aspek Raport Pendidikan</div>
                    </a>
                </li>
                
            </ul>
          </li>
           <!-- end master data -->

            <!-- master delmansuper -->
          <li class="menu-item {{ ( request()->is('superadmin/masterpengawas*') || request()->is('superadmin/sekolah*')  || request()->is('superadmin/guru*')   ) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons fa-solid fas fa-person"></i>
              <div data-i18n="Master Data">Master DelmanSuper</div>
            </a>

            <ul class="menu-sub">
             
                <li class="menu-item {{ (request()->is('superadmin/masterpengawas*')) ? 'active' : '' }}">
                    <a href="{{ route('masterpengawas.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-person"></i>
                        <div data-i18n="Profile">Pengawas</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->is('superadmin/sekolah*')) ? 'active' : '' }}">
                    <a href="{{ route('sekolah.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons fas fa-school"></i>
                        <div data-i18n="Profile">Sekolah</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->is('superadmin/guru*')) ? 'active' : '' }}">
                    <a href="{{ route('guru.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-users"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-users-line"></i>
                        <div data-i18n="Profile"> Kepala Sekolah</div>
                    </a>
                </li>
                
            </ul>
          </li>

          <!-- master delmansuper -->

        

        <li class="menu-item {{ (request()->is('superadmin/rencanatugas*')) ? 'active' : '' }}">
            <a href="{{ route('rencanatugas.index') }}" class="menu-link">
                {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                <i class="menu-icon tf-icons fa-solid fas fa-list-ol"></i>
                <div data-i18n="Profile">List Rencana Kerja</div>
            </a>
        </li>

        <li class="menu-item {{ ( request()->is('superadmin/listumpanbalik*') 
        || request()->is('superadmin/dokumentasipendampingan*') 
         || request()->is('superadmin/saranperbaikan*') 
          || request()->is('superadmin/layanandibutuhkan*')   ) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
              <div data-i18n="Master Data">Umpan Balik</div>
            </a>

            <ul class="menu-sub">
             
               
                <li class="menu-item {{ (request()->is('superadmin/listumpanbalik*')) ? 'active' : '' }}">
                    <a href="{{ route('listumpanbalik.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                        <div data-i18n="Profile">List Umpan Balik</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->is('superadmin/dokumentasipendampingan*')) ? 'active' : '' }}">
                    <a href="{{ route('dokumentasipendampingan.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                        <div data-i18n="Profile">Dokumentasi Pendampingan</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('superadmin/saranperbaikan*')) ? 'active' : '' }}">
                    <a href="{{ route('saranperbaikan.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                        <div data-i18n="Profile">Saran Perbaikan</div>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('superadmin/layanandibutuhkan*')) ? 'active' : '' }}">
                    <a href="{{ route('layanandibutuhkan.index') }}" class="menu-link">
                        {{-- <i class="menu-icon tf-icons ti ti-user"></i> --}}
                        <i class="menu-icon tf-icons fa-solid fas fa-thumbs-up"></i>
                        <div data-i18n="Profile">Layanan yang dibutuhkan</div>
                    </a>
                </li>
                
            </ul>
          </li>
          

        

        {{-- <li class="menu-item {{ (request()->is('superadmin/stakeholder*')) ? 'active' : '' }}">
            <a href="{{ route('stakeholder.index') }}" class="menu-link">
                <i class="menu-icon tf-icons fa-solid fa-user-tie"></i>
                <div data-i18n="Profile">Stakeholder</div>
            </a>
        </li> --}}

        <li class="menu-item {{ (request()->is('superadmin/wablasthistory')) ? 'active' : '' }}">
            <a href="{{ route('wablasthistory.index') }}" class="menu-link">
                {{-- <i class="menu-icon tf-icons ti ti-users"></i> --}}
                <i class="menu-icon tf-icons fa-solid fa-envelope"></i>
                <div data-i18n="Profile">Histori Wa Blast</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->role == 'Admin')
        <li class="menu-item {{ (request()->is('admin/masterpengawas*')) ? 'active' : '' }}">
            <a href="{{ route('masterpengawas.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="Profile">Pengawas</div>
            </a>
        </li>
        <li class="menu-item {{ (request()->is('admin/sekolah*')) ? 'active' : '' }}">
            <a href="{{ route('sekolah.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div data-i18n="Profile">Sekolah</div>
            </a>
        </li>
        <li class="menu-item {{ (request()->is('admin/guru*')) ? 'active' : '' }}">
            <a href="{{ route('guru.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Profile">Guru / Kepala Sekolah</div>
            </a>
        </li>

        <li class="menu-item {{ (request()->is('admin/stakeholder*')) ? 'active' : '' }}">
            <a href="{{ route('stakeholder.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Profile">Stakeholder</div>
            </a>
        </li>
        
        @endif

        <li class="menu-item">
            <a class="menu-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                <i class="menu-icon fa fa-sign-out"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
            <form id="logout-form2" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</aside>
