<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    let submenu = document.getElementById(submenuId);
    if (submenu) submenu.classList.toggle('show');
}

// Auto open submenu based on current route
document.addEventListener('DOMContentLoaded', function() {
    const url = window.location.href;
    if (url.includes('/admin/assets') || url.includes('/admin/categories') || url.includes('/admin/locations')) {
        document.getElementById('submenu-asset')?.classList.add('show');
    }
    if (url.includes('/admin/maintenances')) {
        document.getElementById('submenu-maintenance')?.classList.add('show');
    }
    if (url.includes('/admin/users')) {
        document.getElementById('submenu-user')?.classList.add('show');
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

// Loan
if (url.includes('/admin/loans')) {
    document.getElementById('submenu-loan')?.classList.add('show');
}
</script>