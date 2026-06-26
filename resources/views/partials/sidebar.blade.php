<div class="sidebar" id="sidebar">
    <div class="brand">
        @if($companyLogo)
            <img src="{{ $companyLogo }}" alt="Logo" style="max-width: 80px; margin-bottom: 10px;">
        @endif
        <h3><i class="bi bi-box-seam"></i> {{ $systemName }}</h3>
        <small>{{ $companyName }}</small>
    </div>
    
    <div class="sidebar-menu-container" style="overflow-y: auto; flex: 1; min-height: 0;">
        <ul class="nav flex-column">
            <!-- SEPARATOR -->
            <li class="nav-header" style="padding: 15px 20px 5px; font-size: 11px; text-transform: uppercase; color: #8b8b9e;">MENU UTAMA</li>
            <!-- SEPARATOR -->
            <li class="nav-header" style="padding: 15px 20px 5px; font-size: 11px; text-transform: uppercase; color: #8b8b9e;"></li>
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <!-- Manajemen Aset -->
            @canany(['view assets', 'view categories', 'view locations'])
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.assets.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.locations.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-asset')">
                    <i class="bi bi-hdd-stack"></i> Manajemen Aset
                    <i class="bi bi-chevron-down chevron" id="chevron-asset"></i>
                </a>
                <ul class="submenu" id="submenu-asset">
                    @can('view assets')
                    <li><a class="nav-link" href="{{ route('admin.assets.index') }}"><i class="bi bi-list-ul"></i> Daftar Aset</a></li>
                    @endcan
                    @can('create assets')
                    <li><a class="nav-link" href="{{ route('admin.assets.create') }}"><i class="bi bi-plus-circle"></i> Tambah Aset</a></li>
                    @endcan
                    @can('view categories')
                    <li><a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Kategori</a></li>
                    @endcan
                    @can('view locations')
                    <li><a class="nav-link" href="{{ route('admin.locations.index') }}"><i class="bi bi-geo-alt"></i> Lokasi</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany
            
            <!-- Maintenance -->
            @can('view maintenances')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.maintenances.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-maintenance')">
                    <i class="bi bi-wrench"></i> Maintenance
                    <i class="bi bi-chevron-down chevron" id="chevron-maintenance"></i>
                </a>
                <ul class="submenu" id="submenu-maintenance">
                    <li><a class="nav-link" href="{{ route('admin.maintenances.index') }}"><i class="bi bi-list-ul"></i> Semua</a></li>
                    <li><a class="nav-link" href="{{ route('admin.maintenances.create') }}"><i class="bi bi-plus-circle"></i> Tambah</a></li>
                    <li><a class="nav-link" href="{{ route('admin.maintenances.schedule') }}"><i class="bi bi-calendar"></i> Jadwal</a></li>
                    <li><a class="nav-link" href="{{ route('admin.maintenances.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a></li>
                    <li><a class="nav-link" href="{{ route('admin.maintenances.history') }}"><i class="bi bi-clock-history"></i> Riwayat</a></li>
                </ul>
            </li>
            @endcan
            
            <!-- Peminjaman -->
            @can('view loans')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-loan')">
                    <i class="bi bi-box-arrow-in-right"></i> Peminjaman
                    <i class="bi bi-chevron-down chevron" id="chevron-loan"></i>
                </a>
                <ul class="submenu" id="submenu-loan">
                    <li><a class="nav-link" href="{{ route('admin.loans.index') }}"><i class="bi bi-list-ul"></i> Daftar</a></li>
                    <li><a class="nav-link" href="{{ route('admin.loans.create') }}"><i class="bi bi-plus-circle"></i> Ajukan</a></li>
                </ul>
            </li>
            @endcan
            
            <!-- User Management -->
            @can('view users')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-user')">
                    <i class="bi bi-people"></i> User Management
                    <i class="bi bi-chevron-down chevron" id="chevron-user"></i>
                </a>
                <ul class="submenu" id="submenu-user">
                    <li><a class="nav-link" href="{{ route('admin.users.index') }}"><i class="bi bi-list-ul"></i> Daftar</a></li>
                    <li><a class="nav-link" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus"></i> Tambah</a></li>
                </ul>
            </li>
            @endcan
            
            <!-- CCTV -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.cctvs.*') ? 'active' : '' }}" href="{{ route('admin.cctvs.index') }}">
                    <i class="bi bi-camera-video"></i> CCTV
                </a>
            </li>
            
            <!-- Dokumen & Laporan (Gabungan) -->
            @can('view reports')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.mecards.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-documents')">
                    <i class="bi bi-files"></i> Dokumen & Laporan
                    <i class="bi bi-chevron-down chevron" id="chevron-documents"></i>
                </a>
                <ul class="submenu" id="submenu-documents">
                    <li><a class="nav-link" href="{{ route('admin.mecards.index') }}"><i class="bi bi-person-vcard"></i> MeCard</a></li>
                    <li><a class="nav-link" href="{{ route('admin.reports.index') }}"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a></li>
                </ul>
            </li>
            @endcan
            
            <!-- Stock Opname -->
            @role('super_admin|admin')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.stock-opname.*') || request()->routeIs('mobile.stock-opname.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-stock')">
                    <i class="bi bi-clipboard-check"></i> Stock Opname
                    <i class="bi bi-chevron-down chevron" id="chevron-stock"></i>
                </a>
                <ul class="submenu" id="submenu-stock">
                    <li><a class="nav-link" href="{{ route('admin.stock-opname.index') }}"><i class="bi bi-list-ul"></i> Manajemen Stock</a></li>
                    <li><a class="nav-link" href="{{ route('mobile.stock-opname.index') }}" target="_blank"><i class="bi bi-phone"></i> Stock Mobile</a></li>
                </ul>
            </li>
            @endrole
            
            <!-- SEPARATOR -->
            <li class="nav-header" style="padding: 15px 20px 5px; font-size: 11px; text-transform: uppercase; color: #8b8b9e;">SISTEM & KEAMANAN</li>
            <!-- SEPARATOR -->
            <li class="nav-header" style="padding: 15px 20px 5px; font-size: 11px; text-transform: uppercase; color: #8b8b9e;"></li>
            
            <!-- History Perpindahan -->
            @role('super_admin|admin')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.asset-location-history.index') }}">
                    <i class="bi bi-arrow-left-right"></i> History Perpindahan
                </a>
            </li>
            @endrole
            
            <!-- Audit Log -->
            @role('super_admin|admin')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.audit-log.index') }}">
                    <i class="bi bi-journal-text"></i> Audit Log
                </a>
            </li>
            @endrole
            
            <!-- Reminder Maintenance -->
            @role('super_admin|admin')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.reminder.index') }}">
                    <i class="bi bi-bell"></i> Reminder Maintenance
                </a>
            </li>
            @endrole
            
            <!-- Database Backup -->
            @role('super_admin|admin')
            <li class="nav-item has-submenu">
                <a class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" 
                   href="javascript:void(0)" onclick="toggleSubmenu('submenu-backup')">
                    <i class="bi bi-database"></i> Database Backup
                    <i class="bi bi-chevron-down chevron" id="chevron-backup"></i>
                </a>
                <ul class="submenu" id="submenu-backup">
                    <li><a class="nav-link" href="{{ route('admin.backup.index') }}"><i class="bi bi-hdd-stack"></i> Backup & Restore</a></li>
                    <li><a class="nav-link" href="{{ route('admin.backup.schedule') }}"><i class="bi bi-clock"></i> Schedule Backup</a></li>
                </ul>
            </li>
            @endrole

            <!-- API Keys -->
            @role('super_admin|admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.api-keys.*') ? 'active' : '' }}" 
                href="{{ route('admin.api-keys.index') }}">
                    <i class="bi bi-key"></i> API Keys
                </a>
            </li>
            @endrole
            
            <!-- Konfigurasi -->
            @can('view settings')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                    <i class="bi bi-gear"></i> Konfigurasi
                </a>
            </li>
            @endcan
        </ul>
    </div>
</div>