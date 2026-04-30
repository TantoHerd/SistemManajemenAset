<nav class="navbar-top">
    <!-- Kiri: Menu Toggle -->
    <button class="menu-toggle" id="menuToggle">
        <i class="bi bi-list"></i>
    </button>
    
    <!-- Kanan: Notifikasi + User -->
    <div class="d-flex align-items-center gap-3 ms-auto">
        
        <!-- NOTIFIKASI BELL -->
        <div class="dropdown">
            <a class="notification-bell" href="#" id="notificationDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger rounded-pill notification-badge" id="notificationCount" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                <!-- Header -->
                <div class="dropdown-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bell me-1"></i>Notifikasi</h6>
                    <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllRead">
                        <small>Tandai semua dibaca</small>
                    </button>
                </div>
                
                <!-- List -->
                <div id="notificationList">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        <p class="small mb-0">Memuat notifikasi...</p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="notification-footer text-center">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-link text-decoration-none">
                        <i class="bi bi-inbox me-1"></i>Lihat Semua Notifikasi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- USER DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <div class="avatar">
                    @if(Auth::user()->avatar && file_exists(storage_path('app/public/' . Auth::user()->avatar)))
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar">
                    @else
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600; font-size: 14px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <span class="user-name d-none d-md-inline">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        
    </div>
</nav>