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
                <li><a class="nav-link {{ request()->routeIs('admin.assets.index') ? 'active' : '' }}" href="{{ route('admin.assets.index') }}"><i class="bi bi-list-ul"></i> Daftar Aset</a></li>
                @endcan
                @can('create assets')
                <li><a class="nav-link {{ request()->routeIs('admin.assets.create') ? 'active' : '' }}" href="{{ route('admin.assets.create') }}"><i class="bi bi-plus-circle"></i> Tambah Aset</a></li>
                @endcan
                @can('import assets')
                <li><a class="nav-link" href="{{ route('admin.assets.import') }}"><i class="bi bi-upload"></i> Import Aset</a></li>
                @endcan
                @canany(['view categories', 'view locations'])
                <li><hr class="dropdown-divider" style="margin: 8px 0; background: rgba(255,255,255,0.1);"></li>
                @endcanany
                @can('view categories')
                <li><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Kategori</a></li>
                @endcan
                @can('view locations')
                <li><a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}"><i class="bi bi-geo-alt"></i> Lokasi</a></li>
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
                <li><a class="nav-link {{ request()->routeIs('admin.maintenances.index') ? 'active' : '' }}" href="{{ route('admin.maintenances.index') }}"><i class="bi bi-list-ul"></i> Semua</a></li>
                @can('create maintenances')
                <li><a class="nav-link {{ request()->routeIs('admin.maintenances.create') ? 'active' : '' }}" href="{{ route('admin.maintenances.create') }}"><i class="bi bi-plus-circle"></i> Tambah</a></li>
                @endcan
                <li><a class="nav-link {{ request()->routeIs('admin.maintenances.schedule') ? 'active' : '' }}" href="{{ route('admin.maintenances.schedule') }}"><i class="bi bi-calendar"></i> Jadwal</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.maintenances.history') ? 'active' : '' }}" href="{{ route('admin.maintenances.history') }}"><i class="bi bi-clock-history"></i> Riwayat</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.maintenances.report') ? 'active' : '' }}" href="{{ route('admin.maintenances.report') }}"><i class="bi bi-file-text"></i> Laporan</a></li>
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
                <li><a class="nav-link {{ request()->routeIs('admin.loans.index') ? 'active' : '' }}" href="{{ route('admin.loans.index') }}"><i class="bi bi-list-ul"></i> Daftar</a></li>
                @can('create loans')
                <li><a class="nav-link {{ request()->routeIs('admin.loans.create') ? 'active' : '' }}" href="{{ route('admin.loans.create') }}"><i class="bi bi-plus-circle"></i> Ajukan</a></li>
                @endcan
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
                <li><a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-list-ul"></i> Daftar</a></li>
                @can('create users')
                <li><a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus"></i> Tambah</a></li>
                @endcan
                @can('import users')
                <li><a class="nav-link" href="{{ route('admin.users.import') }}"><i class="bi bi-upload"></i> Import</a></li>
                @endcan
                @can('export users')
                <li><a class="nav-link" href="{{ route('admin.users.export') }}"><i class="bi bi-download"></i> Export</a></li>
                @endcan
            </ul>
        </li>
        @endcan

        <!-- Report-->
        @can('view reports')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
        </li>
        @endcan
        
        <!-- Laporan -->
        {{-- @canany(['export assets', 'view reports'])
        <li class="nav-item has-submenu">
            <a class="nav-link" href="javascript:void(0)" onclick="toggleSubmenu('submenu-report')">
                <i class="bi bi-file-earmark-text"></i> Laporan & Export
                <i class="bi bi-chevron-down chevron" id="chevron-report"></i>
            </a>
            <ul class="submenu" id="submenu-report">
                @can('export assets')
                <li><a class="nav-link" href="#" id="exportExcelBtn"><i class="bi bi-file-earmark-excel"></i> Export Aset</a></li>
                <li><a class="nav-link" href="{{ route('admin.amortization.export') }}"><i class="bi bi-graph-down"></i> Export Amortisasi</a></li>
                @endcan
            </ul>
        </li>
        @endcanany --}}
        
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