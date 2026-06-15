<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gridstack@10.1.0/dist/gridstack-all.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
// ============================================
// SIDEBAR TOGGLE
// ============================================
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

if (menuToggle) menuToggle.addEventListener('click', openSidebar);
if (overlay) overlay.addEventListener('click', closeSidebar);

window.addEventListener('resize', function() {
    if (window.innerWidth > 992) closeSidebar();
});

// ============================================
// SUBMENU TOGGLE
// ============================================
function toggleSubmenu(submenuId) {
    var submenu = document.getElementById(submenuId);
    var parent = submenu ? submenu.closest('.has-submenu') : null;
    
    if (submenu) {
        submenu.classList.toggle('show');
        if (parent) {
            parent.classList.toggle('open');
        }
    }
}

// Auto open submenu based on current route
document.addEventListener('DOMContentLoaded', function() {
    const url = window.location.href;
    
    // Manajemen Aset
    if (url.includes('/admin/assets') || url.includes('/admin/categories') || url.includes('/admin/locations')) {
        document.getElementById('submenu-asset')?.classList.add('show');
    }
    // Maintenance
    if (url.includes('/admin/maintenances')) {
        document.getElementById('submenu-maintenance')?.classList.add('show');
    }
    // Peminjaman
    if (url.includes('/admin/loans')) {
        document.getElementById('submenu-loan')?.classList.add('show');
    }
    // User Management
    if (url.includes('/admin/users')) {
        document.getElementById('submenu-user')?.classList.add('show');
    }
    // Dokumen & Laporan (MeCard & Reports)
    if (url.includes('/admin/mecards') || url.includes('/admin/reports')) {
        document.getElementById('submenu-documents')?.classList.add('show');
    }
    // Stock Opname
    if (url.includes('/admin/stock-opname') || url.includes('/stock-opname-mobile')) {
        document.getElementById('submenu-stock')?.classList.add('show');
    }
    // Database Backup
    if (url.includes('/admin/backup')) {
        document.getElementById('submenu-backup')?.classList.add('show');
    }
    // CCTV
    if (url.includes('/admin/cctvs')) {
        // CCTV tidak pakai submenu, skip
    }
    // History Perpindahan
    if (url.includes('/admin/asset-location-history')) {
        // Tidak pakai submenu
    }
    // Audit Log
    if (url.includes('/admin/audit-log')) {
        // Tidak pakai submenu
    }
    // Reminder Maintenance
    if (url.includes('/admin/reminder')) {
        // Tidak pakai submenu
    }
});

// ============================================
// FIX SIDEBAR SCROLL ON MOBILE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan sidebar menu container bisa di-scroll
    const menuContainer = document.querySelector('.sidebar-menu-container');
    if (menuContainer) {
        // Cek apakah scroll bekerja
        console.log('Menu container height:', menuContainer.scrollHeight);
        console.log('Menu container client height:', menuContainer.clientHeight);
    }
});

// ============================================
// AUTO HIDE ALERTS
// ============================================
setTimeout(function() {
    document.querySelectorAll('.alert').forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() { alert.remove(); }, 500);
    });
}, 5000);

// ============================================
// EXPORT EXCEL
// ============================================
$(document).on('click', '#exportExcelBtn', function(e) {
    e.preventDefault();
    let params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("admin.assets.export") }}?' + params.toString();
});

// ============================================
// NOTIFICATION SYSTEM
// ============================================
function loadNotifications() {
    $.get('{{ route("admin.notifications.unread") }}', function(response) {
        const count = response.count;
        const notifications = response.notifications;
        
        if (count > 0) {
            $('#notificationCount').text(count > 99 ? '99+' : count).show();
        } else {
            $('#notificationCount').hide();
        }
        
        let html = '';
        if (notifications.length === 0) {
            html = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                    <p class="small mb-0">Tidak ada notifikasi baru</p>
                </div>`;
        } else {
            notifications.forEach(function(n) {
                const bgClass = n.is_read ? '' : 'unread';
                html += `
                    <a href="${n.link || '#'}" 
                       class="notification-item ${bgClass}" 
                       data-id="${n.id}">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="icon-circle bg-${n.color} bg-opacity-10 text-${n.color}">
                                <i class="bi bi-${n.icon}"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="fw-semibold small mb-1">${n.title}</div>
                                <div class="text-muted small">${n.message}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">${n.time}</small>
                            </div>
                            ${!n.is_read ? '<span class="badge bg-primary rounded-pill" style="font-size: 0.5rem;">●</span>' : ''}
                        </div>
                    </a>`;
            });
        }
        $('#notificationList').html(html);
    }).fail(function() {
        $('#notificationList').html(`
            <div class="text-center py-4 text-muted">
                <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                <p class="small mb-0">Gagal memuat notifikasi</p>
            </div>`);
    });
}

$(document).on('click', '.notification-item', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const link = $(this).attr('href');
    
    $.post(`/admin/notifications/${id}/read`, {
        _token: '{{ csrf_token() }}'
    }, function() {
        if (link && link !== '#') {
            window.location.href = link;
        } else {
            loadNotifications();
        }
    });
});

$('#markAllRead').click(function() {
    $.post('{{ route("admin.notifications.read-all") }}', {
        _token: '{{ csrf_token() }}'
    }, function() {
        loadNotifications();
    });
});

loadNotifications();
setInterval(loadNotifications, 30000);

// ============================================
// GLOBAL DELETE CONFIRMATION with SweetAlert
// ============================================
function confirmDelete(url, message) {
    Swal.fire({
        title: 'Yakin?',
        text: message || 'Data akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Buat form dengan CSRF token
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.style.display = 'none';
            
            // Tambahkan CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            
            // Tambahkan method spoofing untuk DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            
            // Submit form
            form.submit();
        }
    });
}

// Untuk form delete inline (pakai event)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmDelete(this.action, 'Data akan dihapus permanen dan tidak dapat dikembalikan!');
        });
    });
});

// ============================================
// TOGGLE SUBMENU BARU (untuk link dengan onclick)
// ============================================
// Fungsi ini akan dipanggil dari sidebar untuk toggle submenu
window.toggleSubmenuById = function(submenuId) {
    const submenu = document.getElementById(submenuId);
    if (submenu) {
        submenu.classList.toggle('show');
        // Rotate chevron icon
        const chevron = document.getElementById('chevron-' + submenuId.replace('submenu-', ''));
        if (chevron) {
            chevron.style.transform = submenu.classList.contains('show') ? 'rotate(180deg)' : '';
        }
    }
}

// Inisialisasi submenu dari localStorage (keep state)
document.addEventListener('DOMContentLoaded', function() {
    const openSubmenus = localStorage.getItem('openSubmenus');
    if (openSubmenus) {
        const submenuIds = JSON.parse(openSubmenus);
        submenuIds.forEach(id => {
            const submenu = document.getElementById(id);
            if (submenu) submenu.classList.add('show');
        });
    }
    
    // Simpan state submenu saat toggle
    document.querySelectorAll('.has-submenu > a').forEach(link => {
        link.addEventListener('click', function() {
            const submenu = this.nextElementSibling;
            if (submenu && submenu.id) {
                setTimeout(() => {
                    let openSubmenus = [];
                    document.querySelectorAll('.submenu.show').forEach(sm => {
                        openSubmenus.push(sm.id);
                    });
                    localStorage.setItem('openSubmenus', JSON.stringify(openSubmenus));
                }, 100);
            }
        });
    });
});
</script>