<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#667eea">
    <title>SIMASET Mobile - Stock Opname</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="manifest" href="/manifest.json">
    <style>
        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .session-card {
            background: white;
            border-radius: 16px;
            padding: 15px;
            margin: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
        }
        .btn-scan {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .progress-bar-custom {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            border-radius: 10px;
            height: 8px;
        }
        @media (prefers-color-scheme: dark) {
            body { background: #1a1a1a; }
            .mobile-container { background: #1a1a1a; }
            .session-card { background: #2a2a2a; border-color: #3a3a3a; color: white; }
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="header">
            <i class="bi bi-upc-scan fs-1"></i>
            <h4 class="mt-2">Stock Opname Mobile</h4>
            <p class="mb-0 small">SIMASET - Sistem Manajemen Aset IT</p>
        </div>

        @if($activeSession)
        <div class="session-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $activeSession->name }}</h6>
                    <small class="text-muted">Status: 
                        @if($activeSession->status == 'in_progress')
                            <span class="text-success">Berjalan</span>
                        @else
                            <span class="text-warning">Draft</span>
                        @endif
                    </small>
                </div>
                <a href="{{ route('mobile.stock-opname.scan', $activeSession) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-play-fill"></i> Lanjutkan
                </a>
            </div>
            <div class="progress mb-2" style="height: 8px;">
                @php
                    $total = $activeSession->items->count();
                    $scanned = $activeSession->items->whereNotNull('scanned_at')->count();
                    $progress = $total > 0 ? round(($scanned / $total) * 100) : 0;
                @endphp
                <div class="progress-bar-custom" style="width: {{ $progress }}%"></div>
            </div>
            <div class="d-flex justify-content-between small text-muted">
                <span><i class="bi bi-check-circle"></i> {{ $scanned }} selesai</span>
                <span><i class="bi bi-hourglass"></i> {{ $total - $scanned }} tersisa</span>
            </div>
        </div>
        @else
        <div class="session-card text-center">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-2 mb-3">Tidak ada sesi stock opname aktif</p>
            <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-primary btn-sm">
                Buat Sesi Baru
            </a>
        </div>
        @endif

        <div class="p-3">
            <h6 class="fw-bold mb-3">Riwayat Sesi</h6>
            @foreach($sessions as $session)
            <div class="session-card mb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $session->name }}</strong>
                        <br>
                        <small class="text-muted">
                            {{ $session->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                    <div>
                        @if($session->status == 'completed')
                            <span class="badge bg-success">Selesai</span>
                        @elseif($session->status == 'in_progress')
                            <span class="badge bg-primary">Berjalan</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                </div>
                @if($session->status != 'completed')
                    <a href="{{ route('mobile.stock-opname.scan', $session) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        <i class="bi bi-upc-scan"></i> Scan
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.log('Service Worker error:', err));
            });
        }
    </script>
</body>
</html>