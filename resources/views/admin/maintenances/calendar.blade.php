@extends('admin.layouts.app')

@section('title', 'Kalender Maintenance')
@section('page-title', 'Kalender Maintenance')

@section('header-actions')
    <a href="{{ route('admin.maintenances.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Tambah Maintenance
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="eventTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm mb-0">
                    <tr><th width="80">Aset</th><td id="eventAsset"></td></tr>
                    <tr><th>Status</th><td id="eventStatus"></td></tr>
                    <tr><th>Biaya</th><td id="eventCost"></td></tr>
                </table>
                <a href="#" id="eventLink" class="btn btn-primary btn-sm w-100 mt-3">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    #calendar { max-width: 100%; }
    .fc-toolbar-title { font-size: 1.2rem !important; }
    .fc-button { padding: 5px 12px !important; font-size: 0.85rem !important; }
    .fc-daygrid-event { border-radius: 6px !important; padding: 2px 6px !important; font-size: 0.8rem !important; }
    @media (max-width: 768px) {
        .fc-toolbar { flex-direction: column; gap: 8px; }
        .fc-toolbar-title { font-size: 1rem !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const events = @json($maintenances);
    
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            list: 'List'
        },
        events: events,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            document.getElementById('eventTitle').textContent = info.event.title;
            document.getElementById('eventAsset').textContent = props.asset;
            document.getElementById('eventStatus').innerHTML = '<span class="badge" style="background:' + info.event.backgroundColor + '">' + props.status + '</span>';
            document.getElementById('eventCost').textContent = 'Rp ' + props.cost;
            document.getElementById('eventLink').href = info.event.url;
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        },
        eventDidMount: function(info) {
            info.el.setAttribute('title', info.event.title);
        }
    });
    
    calendar.render();
});
</script>
@endpush