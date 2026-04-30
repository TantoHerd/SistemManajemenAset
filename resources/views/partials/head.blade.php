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
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background-color: #f8f9fc; font-family: 'Inter', sans-serif; }
    
    /* ============================================ */
    /* SIDEBAR */
    /* ============================================ */
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
    
    /* ============================================ */
    /* MAIN CONTENT */
    /* ============================================ */
    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .content-wrapper {
        padding: 20px;
        flex: 1;
    }
    
    /* ============================================ */
    /* NAVBAR */
    /* ============================================ */
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
    
    /* ============================================ */
    /* NOTIFICATION BELL */
    /* ============================================ */
    .notification-bell {
        position: relative;
        cursor: pointer;
        font-size: 1.3rem;
        color: #555;
        transition: color 0.2s;
        text-decoration: none !important;
    }
    .notification-bell:hover {
        color: #4361ee;
    }
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -10px;
        font-size: 0.6rem;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    .notification-dropdown {
        width: 380px;
        max-height: 480px;
        overflow-y: auto;
        padding: 0;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border-radius: 12px;
        margin-top: 12px !important;
    }
    .notification-dropdown .dropdown-header-custom {
        padding: 12px 16px;
        background: white;
        border-bottom: 1px solid #eee;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
        text-decoration: none !important;
        display: block;
    }
    .notification-item:hover {
        background: #f8f9fc;
    }
    .notification-item.unread {
        background: #f0f4ff;
        border-left: 3px solid #4361ee;
    }
    .notification-item .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .notification-footer {
        padding: 10px;
        background: white;
        border-radius: 0 0 12px 12px;
        position: sticky;
        bottom: 0;
        border-top: 1px solid #eee;
    }
    
    /* ============================================ */
    /* AVATAR */
    /* ============================================ */
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9ecef;
        overflow: hidden;
    }
    .avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    /* ============================================ */
    /* SUBMENU */
    /* ============================================ */
    .has-submenu { position: relative; }
    .submenu {
        list-style: none;
        padding-left: 35px;
        margin: 5px 0;
        display: none;
    }
    .submenu.show { display: block; }
    .submenu li { margin-bottom: 3px; }
    .submenu .nav-link { padding: 8px 12px; font-size: 13px; }
    .chevron { margin-left: auto; transition: transform 0.3s; }
    
    /* ============================================ */
    /* FOOTER */
    /* ============================================ */
    .footer {
        font-size: 0.85rem;
        margin-top: auto;
    }
    
    /* ============================================ */
    /* RESPONSIVE - TABLET */
    /* ============================================ */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
            z-index: 1050;
        }
        .sidebar.show { transform: translateX(0); }
        .main-content { margin-left: 0; }
        .menu-toggle { display: block; }
    }
    
    /* ============================================ */
    /* RESPONSIVE - MOBILE */
    /* ============================================ */
    @media (max-width: 768px) {
        .content-wrapper { padding: 12px; }
        .navbar-top { padding: 8px 12px; }
        h4 { font-size: 18px; }
        .breadcrumb { font-size: 12px; }
        .notification-dropdown {
            width: 300px;
            right: -60px !important;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table { min-width: 600px; }
        .card-header { padding: 10px 12px; }
        .card-body { padding: 12px; }
        .btn { padding: 5px 10px; font-size: 12px; }
        .form-control, .form-select { font-size: 14px; padding: 6px 10px; }
        .filter-row { flex-direction: column; }
        .filter-row .col-md-3, .filter-row .col-md-4 { width: 100%; margin-bottom: 8px; }
    }
    
    /* ============================================ */
    /* RESPONSIVE - MOBILE KECIL */
    /* ============================================ */
    @media (max-width: 480px) {
        .content-wrapper { padding: 8px; }
        .navbar-top .user-name { display: none; }
        h4 { font-size: 16px; }
        .badge { padding: 3px 6px; font-size: 10px; }
        .notification-dropdown {
            width: 280px;
            right: -80px !important;
        }
    }
    
    /* ============================================ */
    /* UTILITY */
    /* ============================================ */
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
    .sidebar-overlay.show { display: block; }
    
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
</style>

@stack('styles')