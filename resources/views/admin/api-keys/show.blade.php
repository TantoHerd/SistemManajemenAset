@extends('admin.layouts.app')

@section('title', 'Detail API Key')
@section('page-title', 'Detail API Key: ' . $apiKey->name)

@section('header-actions')
    <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('admin.api-keys.edit', $apiKey) }}" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form action="{{ route('admin.api-keys.toggle', $apiKey->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm {{ $apiKey->is_active ? 'btn-secondary' : 'btn-success' }}">
            <i class="bi {{ $apiKey->is_active ? 'bi-pause' : 'bi-play' }}"></i>
            {{ $apiKey->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>
    </form>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-key me-2"></i> Informasi API Key
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama</th>
                        <td><strong>{{ $apiKey->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>API Key</th>
                        <td>
                            <code class="bg-light p-2 rounded d-block" style="font-size: 14px; word-break: break-all;">
                                {{ $apiKey->key }}
                            </code>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="copyToClipboard('{{ $apiKey->key }}')">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $apiKey->user->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <th>IP Whitelist</th>
                        <td>
                            @if($apiKey->allowed_ips)
                                @foreach($apiKey->allowed_ips as $ip)
                                    <span class="badge bg-info me-1">{{ $ip }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Semua IP diizinkan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($apiKey->is_active)
                                <span class="badge bg-success">✅ Aktif</span>
                            @else
                                <span class="badge bg-danger">❌ Nonaktif</span>
                            @endif
                            @if($apiKey->expires_at && $apiKey->expires_at->isPast())
                                <span class="badge bg-warning ms-2">⚠️ Kadaluarsa</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Kadaluarsa</th>
                        <td>{{ $apiKey->expires_at ? $apiKey->expires_at->format('d/m/Y H:i') : 'Tidak Ada' }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Digunakan</th>
                        <td>{{ $apiKey->last_used_at ? $apiKey->last_used_at->format('d/m/Y H:i:s') : 'Belum pernah digunakan' }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $apiKey->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate</th>
                        <td>{{ $apiKey->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Cara Penggunaan -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i> Cara Penggunaan
                </h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Via Header:</h6>
                <pre class="bg-dark text-white p-2 rounded small">
curl -H "X-API-Key: {{ substr($apiKey->key, 0, 20) }}..." \
     http://10.42.1.15:8080/api/network/devices</pre>

                <h6 class="fw-bold mt-3">Via Query Parameter:</h6>
                <pre class="bg-dark text-white p-2 rounded small">
curl "http://10.42.1.15:8080/api/network/devices?api_key={{ substr($apiKey->key, 0, 20) }}..."</pre>

                <h6 class="fw-bold mt-3">Contoh Response:</h6>
                <pre class="bg-dark text-white p-2 rounded small" style="font-size: 11px;">
{
  "success": true,
  "data": [...],
  "meta": {
    "total": 157,
    "per_page": 20
  }
}</pre>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-tools me-2"></i> Aksi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.api-keys.edit', $apiKey) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit API Key
                    </a>
                    <form action="{{ route('admin.api-keys.destroy', $apiKey) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus API Key ini?')">
                            <i class="bi bi-trash"></i> Hapus API Key
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Tampilkan notifikasi sukses
        toastr.success('API Key berhasil disalin!');
    }).catch(() => {
        // Fallback jika clipboard tidak support
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        toastr.success('API Key berhasil disalin!');
    });
}
</script>
@endsection