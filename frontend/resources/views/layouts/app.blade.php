<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>{{ isset($title) ? $title . ' - InLab Inventory Dashboard' : 'InLab Inventory Dashboard' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/assets/images/favicon_io/site.webmanifest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script type="importmap">
      {
        "imports": {
          "bootstrap": "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js",
          "apexcharts": "https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.esm.js"
        }
      }
    </script>
  </head>
  <body>
    <!-- TOPBAR -->
    <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
      <!-- MOBILE ONLY TOGGLE -->
      <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
        <i class="ti ti-menu-2"></i>
      </button>
      <div>
        <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
          <!-- Bell icon -->
          <li>
            <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">
              <svg class="icon icon-tabler icons-tabler-outline icon-tabler-bell" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path>
                <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
              </svg>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2">2
                <span class="visually-hidden">unread messages</span>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
              <ul class="list-unstyled p-0 m-0">
                <li class="p-3 border-bottom">
                  <div class="d-flex gap-3">
                    <img class="avatar avatar-sm rounded-circle" src="/assets/images/avatar/avatar-1.jpg" alt="">
                    <div class="flex-grow-1 small">
                      <p class="mb-0">New order received</p>
                      <p class="mb-1">Order #12345 has been placed</p>
                      <div class="text-secondary">5 minutes ago</div>
                    </div>
                  </div>
                </li>
                <li class="p-3 border-bottom">
                  <div class="d-flex gap-3">
                    <img class="avatar avatar-sm rounded-circle" src="/assets/images/avatar/avatar-4.jpg" alt="">
                    <div class="flex-grow-1 small">
                      <p class="mb-0">New user registered</p>
                      <p class="mb-1">User @john_doe has signed up</p>
                      <div class="text-secondary">30 minutes ago</div>
                    </div>
                  </div>
                </li>
                <li class="p-3 border-bottom">
                  <div class="d-flex gap-3">
                    <img class="avatar avatar-sm rounded-circle" src="/assets/images/avatar/avatar-2.jpg" alt="">
                    <div class="flex-grow-1 small">
                      <p class="mb-0">Payment confirmed</p>
                      <p class="mb-1">Payment of $299 has been received</p>
                      <div class="text-secondary">1 hour ago</div>
                    </div>
                  </div>
                </li>
                <li class="px-4.py-3 text-center">
                  <a class="text-primary" href="#">View all notifications</a>
                </li>
              </ul>
            </div>
          </li>
          <!-- Dropdown -->
          <li class="ms-3 dropdown">
            <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img class="avatar avatar-sm rounded-circle" src="/assets/images/avatar/avatar-1.jpg" alt="">
            </a>
            <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 200px;">
              <div>
                <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-3 py-3">
                  <img class="avatar avatar-md rounded-circle" src="/assets/images/avatar/avatar-1.jpg" alt="">
                  <div>
                    <h4 class="mb-0 small">{{ Session::has('user') ? Session::get('user')['nama'] : 'Guest' }}</h4>
                    <p class="mb-0 small text-capitalize">{{ Session::has('user') ? str_replace('_', ' ', Session::get('user')['role']) : 'Visitor' }}</p>
                  </div>
                </div>
                <div class="p-3 d-flex flex-column gap-1 small lh-lg">
                  <a href="/">Dashboard</a>
                  @if (Session::has('user'))
                    <a class="text-danger fw-semibold" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
                  @else
                    <a href="{{ route('login') }}">Sign In</a>
                  @endif
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <!-- LOGOUT FORM -->
    @if (Session::has('user'))
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    @endif

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar">
      <!-- Floating Sidebar Toggle for Desktop -->
      <button id="toggleBtn" class="d-none d-lg-flex btn toggle-sidebar-btn">
        <i class="ti ti-chevron-left"></i>
      </button>

      <div class="logo-area">
        <a class="d-inline-flex align-items-center text-decoration-none" href="/">
          <img src="/assets/images/logo-icon.svg" alt="" width="26" height="26">
          <span class="logo-text ms-2">
            <span class="fw-bold text-primary" style="font-size: 20px; font-family: 'Poppins', sans-serif;">In</span><span class="fw-bold text-white" style="font-size: 20px; font-family: 'Poppins', sans-serif;">Lab</span>
            <span class="d-block" style="font-size: 9px; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; margin-top: -3px; font-family: 'Poppins', sans-serif; color: #94a3b8;">Inventory Lab</span>
          </span>
        </a>
      </div>
      <ul class="nav flex-column">
        <li class="px-4 py-2 nav-header"><small class="nav-text">Main</small></li>
        <li>
          <a class="nav-link {{ ($activePath ?? '') === '/' ? 'active' : '' }}" href="/">
            <i class="ti ti-home"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

        @if (Session::has('user'))
          @php
            $role = Session::get('user')['role'];
          @endphp

          @if ($role === 'admin')
            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Administrator</small></li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/admin/users' ? 'active' : '' }}" href="/admin/users">
                <i class="ti ti-users"></i>
                <span class="nav-text">Data Pengguna</span>
              </a>
            </li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/admin/ruangan' ? 'active' : '' }}" href="/admin/ruangan">
                <i class="ti ti-map-pin"></i>
                <span class="nav-text">Data Ruangan</span>
              </a>
            </li>
          @elseif ($role === 'kepala_lab')
            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Kepala Lab</small></li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/kepala-lab/pengadaan' ? 'active' : '' }}" href="/kepala-lab/pengadaan">
                <i class="ti ti-file-plus"></i>
                <span class="nav-text">Draf Pengadaan</span>
              </a>
            </li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/kepala-lab/history' ? 'active' : '' }}" href="/kepala-lab/history">
                <i class="ti ti-history"></i>
                <span class="nav-text">History Draf</span>
              </a>
            </li>
          @elseif ($role === 'ketua_prodi')
            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Ketua Prodi</small></li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/ketua-prodi/review' ? 'active' : '' }}" href="/ketua-prodi/review">
                <i class="ti ti-shield-check"></i>
                <span class="nav-text">Review Draf</span>
              </a>
            </li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/ketua-prodi/history' ? 'active' : '' }}" href="/ketua-prodi/history">
                <i class="ti ti-history"></i>
                <span class="nav-text">History Finalisasi</span>
              </a>
            </li>
          @elseif ($role === 'staf_admin')
            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Staf Administrasi</small></li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/staf-admin/drafts' ? 'active' : '' }}" href="/staf-admin/drafts">
                <i class="ti ti-clipboard-check"></i>
                <span class="nav-text">Draf Disetujui</span>
              </a>
            </li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/staf-admin/inventaris' ? 'active' : '' }}" href="/staf-admin/inventaris">
                <i class="ti ti-edit"></i>
                <span class="nav-text">Update Inventaris</span>
              </a>
            </li>
          @elseif ($role === 'staf_lab')
            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Staf Laboratorium</small></li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/staf-lab/bhp' ? 'active' : '' }}" href="/staf-lab/bhp">
                <i class="ti ti-package"></i>
                <span class="nav-text">Stok BHP</span>
              </a>
            </li>
            <li>
              <a class="nav-link {{ ($activePath ?? '') === '/staf-lab/maintenance' ? 'active' : '' }}" href="/staf-lab/maintenance">
                <i class="ti ti-tool"></i>
                <span class="nav-text">Log Maintenance</span>
              </a>
            </li>
          @endif
        @endif

        <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">General</small></li>
        <li>
          <a class="nav-link {{ ($activePath ?? '') === '/inventory' ? 'active' : '' }}" href="/inventory">
            <i class="ti ti-box-seam"></i>
            <span class="nav-text">Inventory</span>
          </a>
        </li>
        <li>
          <a class="nav-link {{ ($activePath ?? '') === '/create-product' ? 'active' : '' }}" href="/create-product">
            <i class="ti ti-plus"></i>
            <span class="nav-text">Add Product</span>
          </a>
        </li>
        <li>
          <a class="nav-link {{ ($activePath ?? '') === '/reports' ? 'active' : '' }}" href="/reports">
            <i class="ti ti-receipt"></i>
            <span class="nav-text">Reports</span>
          </a>
        </li>
        <li>
          <a class="nav-link {{ ($activePath ?? '') === '/docs' ? 'active' : '' }}" href="/docs">
            <i class="ti ti-file-text"></i>
            <span class="nav-text">Docs</span>
          </a>
        </li>

        <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Account</small></li>
        @if (Session::has('user'))
          <li>
            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="ti ti-logout"></i>
              <span class="nav-text">Sign Out</span>
            </a>
          </li>
        @else
          <li>
            <a class="nav-link {{ ($activePath ?? '') === '/signin' ? 'active' : '' }}" href="{{ route('login') }}">
              <i class="ti ti-login"></i>
              <span class="nav-text">Log in</span>
            </a>
          </li>
        @endif
      </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main id="content" class="content py-10">
      <div class="container-fluid">
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-circle-check fs-4 me-2"></i>
              <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-alert-triangle fs-4 me-2"></i>
              <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
      </div>
      @yield('content')
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <footer class="text-center py-2 mt-6 text-secondary">
              <p class="mb-0">Copyright © 2026 InLab Inventory Dashboard. Developed by 
                <a class="text-primary" href="https://codescandy.com/" target="_blank">CodesCandy</a>
                • Distributed by 
                <a class="text-primary" href="https://themewagon.com/" target="_blank">ThemeWagon</a>
              </p>
            </footer>
          </div>
        </div>
      </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="/assets/js/main.js" type="module"></script>
  </body>
</html>
