<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @else
        <link rel="shortcut icon" href="{{ asset('assets/admin/img/favicon/favicon.ico') }}">
    @endif
    
    <title>@yield('title') - {{ $systemName }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* CSS styles sama seperti sebelumnya */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fc; font-family: 'Inter', sans-serif; }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #1e1e2f 0%, #2d2d44 100%);
            color: #e0e0e0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .brand h3 {
            font-size: 1.2rem;
            color: #fff;
            margin: 10px 0 5px;
        }
        
        .sidebar .brand small {
            font-size: 0.7rem;
            color: #a0a0b0;
        }
        
        .sidebar .nav {
            padding: 0 10px;
        }
        
        .sidebar .nav-link {
            color: #d1d1e0;
            padding: 12px 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }
        
        .navbar-top {
            background: white;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }

        .content-wrapper {
            padding: 20px;
        }

        /* ============================================ */
        /* RESPONSIVE - TAMBAHAN UNTUK MOBILE */
        /* ============================================ */
        
        /* Tablet (max-width: 992px) */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
            }
        }
        
        /* Mobile (max-width: 768px) */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 12px;
            }
            
            .navbar-top {
                padding: 8px 12px;
            }
            
            h4 {
                font-size: 18px;
            }
            
            .breadcrumb {
                font-size: 12px;
            }
            
            /* Tabel scroll horizontal */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table {
                min-width: 600px;
            }
            
            /* Card padding lebih kecil */
            .card-header {
                padding: 10px 12px;
            }
            
            .card-body {
                padding: 12px;
            }
            
            /* Button lebih kecil */
            .btn {
                padding: 5px 10px;
                font-size: 12px;
            }
            
            /* Form lebih kecil */
            .form-control, .form-select {
                font-size: 14px;
                padding: 6px 10px;
            }
            
            /* Filter row menjadi column */
            .filter-row {
                flex-direction: column;
            }
            
            .filter-row .col-md-3,
            .filter-row .col-md-4,
            .filter-row .col-md-5 {
                width: 100%;
                margin-bottom: 8px;
            }
        }
        
        /* Mobile kecil (max-width: 480px) */
        @media (max-width: 480px) {
            .content-wrapper {
                padding: 8px;
            }
            
            .navbar-top .btn-light {
                padding: 4px 8px;
                font-size: 12px;
            }
            
            .navbar-top .avatar {
                width: 32px;
                height: 32px;
            }
            
            /* Sembunyikan teks nama user di mobile */
            .navbar-top .btn-light span {
                display: none;
            }
            
            h4 {
                font-size: 16px;
            }
            
            /* Badge lebih kecil */
            .badge {
                padding: 3px 6px;
                font-size: 10px;
            }
        }
        
        /* Overlay untuk sidebar di mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Utility */
        .bg-success-subtle { background-color: #d1e7dd; }
        .bg-primary-subtle { background-color: #cfe2ff; }
        .bg-warning-subtle { background-color: #fff3cd; }
        .bg-danger-subtle { background-color: #f8d7da; }
        .bg-info-subtle { background-color: #cff4fc; }
        
        .text-success { color: #0f5132 !important; }
        .text-primary { color: #084298 !important; }
        .text-warning { color: #664d03 !important; }
        .text-danger { color: #842029 !important; }
        .text-info { color: #055160 !important; }
        
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Submenu Styles */
        .has-submenu {
            position: relative;
        }

        .submenu {
            list-style: none;
            padding-left: 35px;
            margin: 5px 0;
            display: none;
        }

        .submenu.show {
            display: block;
        }

        .submenu li {
            margin-bottom: 3px;
        }

        .submenu .nav-link {
            padding: 8px 12px;
            font-size: 13px;
        }

        .chevron {
            margin-left: auto;
            transition: transform 0.3s;
        }

        .has-submenu.active .chevron {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
        }

        /* Footer Styles */
        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
            font-size: 0.85rem;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
        }

        .footer {
            margin-top: auto;
        }
    </style>
    
    @stack('styles')
</head>
<body>

<!-- Overlay untuk mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="brand">
        @if($companyLogo)
            <img src="{{ $companyLogo }}" alt="Logo" style="max-width: 80px; margin-bottom: 10px;">
        @endif
        <h3><i class="bi bi-box-seam"></i> {{ $systemName }}</h3>
        <small>{{ $companyName }}</small>
    </div>
    
    <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        
        <!-- Manajemen Aset (dengan Sub Menu) -->
        <li class="nav-item has-submenu">
            <a class="nav-link {{ request()->routeIs('admin.assets.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.locations.*') ? 'active' : '' }}" 
               href="javascript:void(0)" onclick="toggleSubmenu('submenu-asset')">
                <i class="bi bi-hdd-stack"></i> Manajemen Aset
                <i class="bi bi-chevron-down chevron" id="chevron-asset"></i>
            </a>
            <ul class="submenu" id="submenu-asset">
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.assets.index') ? 'active' : '' }}" href="{{ route('admin.assets.index') }}">
                        <i class="bi bi-list-ul"></i> Daftar Aset
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.assets.create') ? 'active' : '' }}" href="{{ route('admin.assets.create') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Aset
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ url('/assets/import') }}">
                        <i class="bi bi-upload"></i> Import Aset
                    </a>
                </li>
                <li><hr class="dropdown-divider" style="margin: 8px 0; background: rgba(255,255,255,0.1);"></li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">
                        <i class="bi bi-geo-alt"></i> Lokasi
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Maintenance with Submenu -->
        <li class="nav-item has-submenu">
            <a class="nav-link {{ request()->routeIs('admin.maintenances.*') || request()->routeIs('admin.maintenances.schedule') || request()->routeIs('admin.maintenances.history') ? 'active' : '' }}" 
            href="javascript:void(0)" onclick="toggleSubmenu('submenu-maintenance')">
                <i class="bi bi-wrench"></i> Maintenance
                <i class="bi bi-chevron-down chevron" id="chevron-maintenance"></i>
            </a>
            <ul class="submenu" id="submenu-maintenance">
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenances.index') ? 'active' : '' }}" 
                    href="{{ route('admin.maintenances.index') }}">
                        <i class="bi bi-list-ul"></i> Semua Maintenance
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenances.create') ? 'active' : '' }}" 
                    href="{{ route('admin.maintenances.create') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Maintenance
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenances.schedule') ? 'active' : '' }}" 
                    href="{{ route('admin.maintenances.schedule') }}">
                        <i class="bi bi-calendar"></i> Jadwal Maintenance
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenances.history') ? 'active' : '' }}" 
                    href="{{ route('admin.maintenances.history') }}">
                        <i class="bi bi-clock-history"></i> Riwayat Maintenance
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenances.report') ? 'active' : '' }}" 
                    href="{{ route('admin.maintenances.report') }}">
                        <i class="bi bi-file-text"></i> Laporan Maintenance
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- User Management -->
        <li class="nav-item has-submenu">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
            href="javascript:void(0)" onclick="toggleSubmenu('submenu-user')">
                <i class="bi bi-people"></i> User Management
                <i class="bi bi-chevron-down chevron" id="chevron-user"></i>
            </a>
            <ul class="submenu" id="submenu-user">
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-list-ul"></i> Daftar User
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                        <i class="bi bi-person-plus"></i> Tambah User
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ url('/users/import') }}">
                        <i class="bi bi-upload"></i> Import User
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.users.export') }}">
                        <i class="bi bi-download"></i> Export User
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Laporan & Export -->
        <li class="nav-item has-submenu">
            <a class="nav-link" href="javascript:void(0)" onclick="toggleSubmenu('submenu-report')">
                <i class="bi bi-file-earmark-text"></i> Laporan & Export
                <i class="bi bi-chevron-down chevron" id="chevron-report"></i>
            </a>
            <ul class="submenu" id="submenu-report">
                <li>
                    <a class="nav-link" href="#" id="exportExcelBtn">
                        <i class="bi bi-file-earmark-excel"></i> Export Aset (Excel)
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.amortization.export') }}">
                        <i class="bi bi-graph-down"></i> Export Amortisasi
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Konfigurasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                <i class="bi bi-gear"></i> Konfigurasi
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <nav class="navbar-top">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <div class="avatar">
                    @if(Auth::user()->avatar && file_exists(storage_path('app/public/' . Auth::user()->avatar)))
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                    @else
                        <span class="avatar-initial bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <span class="user-name">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    
    
    <div class="content-wrapper p-4">
        <div class="content-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold">@yield('page-title')</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <!-- DEBUG: Tampilkan teks jika header-actions kosong -->
                <div class="p-2 m-0" style="font-size: 12px;">
                    @yield('header-actions')
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

        <!-- Footer -->
        <footer class="footer mt-auto py-3 bg-light border-top">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                        <span class="text-muted">
                            &copy; {{ date('Y') }} {{ $companyName ?? 'PT. NAMA PERUSAHAAN' }}
                        </span>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <span class="text-muted">
                            <i class="bi bi-box-seam"></i> {{ $systemName }} v1.0
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
<script>
    // Toggle submenu function
    function toggleSubmenu(submenuId) {
        let submenu = document.getElementById(submenuId);
        submenu.classList.toggle('show');
        
        // Toggle chevron icon
        let menuId = submenuId.replace('submenu-', '');
        let chevron = document.getElementById('chevron-' + menuId);
        if (chevron) {
            chevron.classList.toggle('bi-chevron-down');
            chevron.classList.toggle('bi-chevron-up');
        }
    }
    
    // Auto open submenu based on current route
    document.addEventListener('DOMContentLoaded', function() {
        let currentUrl = window.location.href;
        
        // Check Asset submenu
        if (currentUrl.includes('/admin/assets') || 
            currentUrl.includes('/admin/categories') || 
            currentUrl.includes('/admin/locations')) {
            document.getElementById('submenu-asset')?.classList.add('show');
            let chevron = document.getElementById('chevron-asset');
            if (chevron) {
                chevron.classList.remove('bi-chevron-down');
                chevron.classList.add('bi-chevron-up');
            }
        }
        
        // Check Maintenance submenu
        if (currentUrl.includes('/admin/maintenances')) {
            document.getElementById('submenu-maintenance')?.classList.add('show');
            let chevron = document.getElementById('chevron-maintenance');
            if (chevron) {
                chevron.classList.remove('bi-chevron-down');
                chevron.classList.add('bi-chevron-up');
            }
        }
        
        // Check User submenu
        if (currentUrl.includes('/admin/users')) {
            document.getElementById('submenu-user')?.classList.add('show');
            let chevron = document.getElementById('chevron-user');
            if (chevron) {
                chevron.classList.remove('bi-chevron-down');
                chevron.classList.add('bi-chevron-up');
            }
        }
    });
    
    // Toggle sidebar mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    function openSidebar() {
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (menuToggle) {
        menuToggle.addEventListener('click', openSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Auto close sidebar on window resize (desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            closeSidebar();
        }
    });
    
    // Toggle submenu function
    function toggleSubmenu(submenuId) {
        let submenu = document.getElementById(submenuId);
        if (submenu) {
            submenu.classList.toggle('show');
        }
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);
    
    // Export Excel button handler
    $('#exportExcelBtn').on('click', function(e) {
        e.preventDefault();
        let params = new URLSearchParams(window.location.search);
        let url = '{{ route("admin.assets.export") }}?' + params.toString();
        window.location.href = url;
    });
</script>

@stack('scripts')
</body>
</html>