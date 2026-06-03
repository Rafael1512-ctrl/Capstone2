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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
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
    <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3 d-flex align-items-center justify-content-between">
        <!-- MOBILE ONLY TOGGLE -->
        <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
            <i class="ti ti-menu-2"></i>
        </button>
        <div class="ms-auto">
            <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
                <!-- Bell icon -->
                <li>
                    <a class="position-relative btn-icon btn-light btn rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="dropdown"
                        aria-expanded="false" href="#" role="button" style="width: 38px; height: 38px; padding: 0 !important; color: #1e293b !important; background-color: #f1f5f9 !important; border: none !important;">
                        <i class="ti ti-bell fs-5" style="font-size: 20px !important;"></i>
                        <span id="notificationBadge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-1 ms-n1 d-none" style="font-size: 9px; padding: 3px 6px;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" style="min-width: 320px; border-radius: 12px;">
                        <div class="px-3 py-2 border-bottom fw-bold text-dark small bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">Notifikasi</div>
                        <ul id="notificationList" class="list-unstyled p-0 m-0" style="max-height: 320px; overflow-y: auto;">
                            <li class="p-4 text-center text-muted small"><i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Loading...</li>
                        </ul>
                    </div>
                </li>
                <!-- Dropdown -->
                <li class="ms-3 dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="avatar avatar-sm rounded-circle" src="/assets/images/avatar/avatar-1.jpg"
                            alt="">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 200px;">
                        <div>
                            <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-3 py-3">
                                <img class="avatar avatar-md rounded-circle" src="/assets/images/avatar/avatar-1.jpg"
                                    alt="">
                                <div>
                                    <h4 class="mb-0 small">
                                        {{ Session::has('user') ? Session::get('user')['nama'] : 'Guest' }}</h4>
                                    <p class="mb-0 small text-capitalize">
                                        {{ Session::has('user') ? str_replace('_', ' ', Session::get('user')['role']) : 'Visitor' }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-3 d-flex flex-column gap-2 small lh-lg">
                                <a href="/" class="text-dark d-flex align-items-center gap-2"><i class="ti ti-home"></i> Dashboard</a>
                                <a href="/profile" class="text-dark d-flex align-items-center gap-2"><i class="ti ti-user"></i> Profil Saya</a>
                                @if (Session::has('user'))
                                    <a class="text-danger fw-semibold d-flex align-items-center gap-2" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="ti ti-logout"></i> Sign Out
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="d-flex align-items-center gap-2"><i class="ti ti-login"></i> Sign In</a>
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
                    <span class="fw-bold text-primary"
                        style="font-size: 20px; font-family: 'Poppins', sans-serif;">In</span><span
                        class="fw-bold text-white"
                        style="font-size: 20px; font-family: 'Poppins', sans-serif;">Lab</span>
                    <span class="d-block"
                        style="font-size: 9px; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; margin-top: -3px; font-family: 'Poppins', sans-serif; color: #94a3b8;">Inventory
                        Lab</span>
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
                        <a class="nav-link {{ ($activePath ?? '') === '/admin/users' ? 'active' : '' }}"
                            href="/admin/users">
                            <i class="ti ti-users"></i>
                            <span class="nav-text">Data Pengguna</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/admin/ruangan' ? 'active' : '' }}"
                            href="/admin/ruangan">
                            <i class="ti ti-map-pin"></i>
                            <span class="nav-text">Data Ruangan</span>
                        </a>
                    </li>
                @elseif ($role === 'kepala_lab')
                    <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Kepala Lab</small></li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/kepala-lab/pengadaan' ? 'active' : '' }}"
                            href="/kepala-lab/pengadaan">
                            <i class="ti ti-file-plus"></i>
                            <span class="nav-text">Draf Pengadaan</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/kepala-lab/history' ? 'active' : '' }}"
                            href="/kepala-lab/history">
                            <i class="ti ti-history"></i>
                            <span class="nav-text">History Draf</span>
                        </a>
                    </li>
                @elseif ($role === 'ketua_prodi')
                    <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Ketua Prodi</small></li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/ketua-prodi/review' ? 'active' : '' }}"
                            href="/ketua-prodi/review">
                            <i class="ti ti-shield-check"></i>
                            <span class="nav-text">Review Draf</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/ketua-prodi/history' ? 'active' : '' }}"
                            href="/ketua-prodi/history">
                            <i class="ti ti-history"></i>
                            <span class="nav-text">History Finalisasi</span>
                        </a>
                    </li>
                @elseif ($role === 'staf_admin')
                    <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Staf Administrasi</small></li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/staf-admin/drafts' ? 'active' : '' }}"
                            href="/staf-admin/drafts">
                            <i class="ti ti-clipboard-check"></i>
                            <span class="nav-text">Draf Disetujui</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/staf-admin/inventaris' ? 'active' : '' }}"
                            href="/staf-admin/inventaris">
                            <i class="ti ti-edit"></i>
                            <span class="nav-text">Update Inventaris</span>
                        </a>
                    </li>
                @elseif ($role === 'staf_lab')
                    <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Staf Laboratorium</small></li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/staf-lab/bhp' ? 'active' : '' }}"
                            href="/staf-lab/bhp">
                            <i class="ti ti-package"></i>
                            <span class="nav-text">Stok BHP</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/staf-lab/bhp/mutasi' ? 'active' : '' }}"
                            href="/staf-lab/bhp/mutasi">
                            <i class="ti ti-history"></i>
                            <span class="nav-text">Mutasi BHP</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ ($activePath ?? '') === '/staf-lab/maintenance' ? 'active' : '' }}"
                            href="/staf-lab/maintenance">
                            <i class="ti ti-tool"></i>
                            <span class="nav-text">Log Maintenance</span>
                        </a>
                    </li>
                @endif
            @endif

            <li class="px-4 pt-4 pb-2 nav-header"><small class="nav-text">Account</small></li>
            @if (Session::has('user'))
                <li>
                    <a class="nav-link" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti ti-logout"></i>
                        <span class="nav-text">Sign Out</span>
                    </a>
                </li>
            @else
                <li>
                    <a class="nav-link {{ ($activePath ?? '') === '/signin' ? 'active' : '' }}"
                        href="{{ route('login') }}">
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
                    <footer class="text-center py-3 mt-6 text-secondary">
                        <p class="mb-0 small">Copyright © 2026 InLab — Sistem Informasi Inventaris & Pengadaan Laboratorium.</p>
                    </footer>
                </div>
            </div>
        </div>
    </main>

    <!-- CUSTOM CONFIRMATION MODAL -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-light border-bottom px-4 py-3 d-flex align-items-center gap-2">
                    <i class="ti ti-alert-circle fs-4 text-warning"></i>
                    <h5 class="modal-title fw-bold text-dark fs-5" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p id="confirmMessage" class="mb-0 text-dark"></p>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="confirmAndSubmit()">
                        <i class="ti ti-check me-1"></i> Setuju
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CUSTOM CONFIRM FUNCTION -->
    <script>
        let pendingForm = null;
        let validationFunction = null;
        
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'), {
            backdrop: 'static',
            keyboard: false
        });

        function showConfirmDialog(message, form, validator = null) {
            document.getElementById('confirmMessage').textContent = message;
            pendingForm = form;
            validationFunction = validator;
            confirmModal.show();
            return false;
        }

        function confirmAndSubmit() {
            // Run validation if provided
            if (validationFunction && typeof validationFunction === 'function') {
                if (!validationFunction()) {
                    confirmModal.hide();
                    return false;
                }
            }
            
            confirmModal.hide();
            if (pendingForm) {
                // Show loading indicator
                const submitBtn = pendingForm.querySelector('button[type="submit"]');
                const originalHTML = submitBtn?.innerHTML;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Menyimpan...';
                }
                
                // Bypass the submit event listener
                pendingForm.removeEventListener('submit', handleFormSubmit);
                pendingForm.submit();
            }
            pendingForm = null;
            validationFunction = null;
        }

        // Store submit handler in variable for later removal
        function handleFormSubmit(e) {
            const form = e.target;
            const confirmMsg = form.getAttribute('data-confirm');
            const validatorName = form.getAttribute('data-validator');
            let validator = null;
            
            if (validatorName && typeof window[validatorName] === 'function') {
                validator = window[validatorName];
            }
            
            if (confirmMsg) {
                e.preventDefault();
                showConfirmDialog(confirmMsg, form, validator);
                return false;
            }
        }

        // Override form submission for confirmation
        document.addEventListener('submit', handleFormSubmit, true);
    </script>

    <!-- FETCH NOTIFICATIONS AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadNotifications() {
                fetch('/api-notifications')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notificationBadge');
                        const list = document.getElementById('notificationList');
                        
                        if (!badge || !list) return;

                        if (data && data.notifications && data.notifications.length > 0) {
                            const count = data.notifications.length;
                            badge.textContent = count;
                            badge.classList.remove('d-none');

                            let html = '';
                            data.notifications.forEach(n => {
                                let icon = 'ti-info-circle';
                                if (n.type === 'success') {
                                    icon = 'ti-circle-check';
                                } else if (n.type === 'danger') {
                                    icon = 'ti-alert-circle';
                                } else if (n.type === 'warning') {
                                    icon = 'ti-alert-triangle';
                                }
                                const iconColor = 'text-dark bg-light';

                                const dateStr = n.time ? new Date(n.time).toLocaleDateString('id-ID', {
                                    day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit'
                                }) : '';

                                html += `
                                    <li class="p-3 border-bottom hover-bg-light" style="transition: background-color 0.2s;">
                                        <div class="d-flex gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ${iconColor}" style="width: 36px; height: 36px;">
                                                <i class="ti ${icon} fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1 small">
                                                <p class="mb-0 fw-bold text-dark">${n.title}</p>
                                                <p class="mb-1 text-secondary" style="line-height: 1.3;">${n.message}</p>
                                                <div class="text-muted" style="font-size: 0.75rem;">${dateStr}</div>
                                            </div>
                                        </div>
                                    </li>
                                `;
                            });
                            list.innerHTML = html;
                        } else {
                            badge.classList.add('d-none');
                            list.innerHTML = '<li class="p-4 text-center text-muted small"><i class="ti ti-bell-off fs-4 d-block mb-1 opacity-50"></i>Tidak ada notifikasi baru</li>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching notifications:', err);
                        const list = document.getElementById('notificationList');
                        if (list) {
                            list.innerHTML = '<li class="p-3 text-center text-danger small">Gagal memuat notifikasi.</li>';
                        }
                    });
            }

            // Load on page load
            loadNotifications();

            // Refresh notifications every 60 seconds
            setInterval(loadNotifications, 60000);
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="/assets/js/main.js" type="module"></script>
</body>

</html>
