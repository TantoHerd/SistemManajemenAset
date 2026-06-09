@extends('admin.layouts.app')

@section('title', 'Detail MeCard')
@section('page-title', $mecard->name)

@section('header-actions')
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.mecards.print', $mecard) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak Kartu
        </a>
        <a href="{{ route('admin.mecards.download-qr', $mecard) }}" class="btn btn-success btn-sm">
            <i class="bi bi-qr-code me-1"></i>Download QR
        </a>
        <a href="{{ route('admin.mecards.edit', $mecard) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.mecards.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row g-3">
    
    <!-- Detail Kontak -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-1 text-primary"></i>Detail Kontak
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th width="90">Nama</th>
                        <td>: {{ $mecard->name }}</td>
                    </tr>
                    
                    @if($mecard->title)
                    <tr>
                        <th>Jabatan</th>
                        <td>: {{ $mecard->title }}</td>
                    </tr>
                    @endif
                    
                    <!-- Multiple Phones -->
                    @php $phones = $mecard->getAllPhones(); @endphp
                    @if(count($phones) > 0)
                    <tr>
                        <th>Telepon</th>
                        <td>
                            : 
                            @foreach($phones as $phone)
                                <div>
                                    <span class="badge bg-secondary">{{ ucfirst($phone['type'] ?? 'Phone') }}</span>
                                    {{ $phone['number'] }}
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    @elseif($mecard->phone)
                    <tr>
                        <th>Telepon</th>
                        <td>: {{ $mecard->phone }}</td>
                    </tr>
                    @endif
                    
                    <!-- Multiple Emails -->
                    @php $emails = $mecard->getAllEmails(); @endphp
                    @if(count($emails) > 0)
                    <tr>
                        <th>Email</th>
                        <td>
                            : 
                            @foreach($emails as $email)
                                <div>
                                    <span class="badge bg-secondary">{{ ucfirst($email['type'] ?? 'Email') }}</span>
                                    {{ $email['address'] }}
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    @elseif($mecard->email)
                    <tr>
                        <th>Email</th>
                        <td>: {{ $mecard->email }}</td>
                    </tr>
                    @endif
                    
                    @if($mecard->company)
                    <tr>
                        <th>Perusahaan</th>
                        <td>: {{ $mecard->company }}</td>
                    </tr>
                    @endif
                    
                    <!-- Multiple Addresses -->
                    @php $addresses = $mecard->getAllAddresses(); @endphp
                    @if(count($addresses) > 0)
                    <tr>
                        <th>Alamat</th>
                        <td>
                            : 
                            @foreach($addresses as $address)
                                <div>
                                    <span class="badge bg-secondary">{{ ucfirst($address['type'] ?? 'Address') }}</span>
                                    {{ $address['text'] }}
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    @elseif($mecard->address)
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $mecard->address }}</td>
                    </tr>
                    @endif
                    
                    <!-- Multiple Socials -->
                    @php $socials = $mecard->getAllSocials(); @endphp
                    @if(count($socials) > 0)
                    <tr>
                        <th>Social Media</th>
                        <td>
                            : 
                            @foreach($socials as $social)
                                <div>
                                    <span class="badge bg-secondary">{{ ucfirst($social['type'] ?? 'URL') }}</span>
                                    <a href="{{ $social['url'] }}" target="_blank">{{ $social['url'] }}</a>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    @elseif($mecard->website)
                    <tr>
                        <th>Website</th>
                        <td>: <a href="{{ $mecard->website }}" target="_blank">{{ $mecard->website }}</a></td>
                    </tr>
                    @endif
                    
                    <!-- Custom Fields -->
                    @php $customFields = $mecard->getAllCustomFields(); @endphp
                    @foreach($customFields as $field)
                    <tr>
                        <th>{{ $field['label'] }}</th>
                        <td>: {{ $field['value'] }}</td>
                    </tr>
                    @endforeach
                    
                    @if($mecard->note)
                    <tr>
                        <th>Catatan</th>
                        <td>: {{ $mecard->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- QR Code Preview -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-qr-code me-1 text-success"></i>QR Code
                </h6>
            </div>
            <div class="text-center">
                <img src="{{ $qrCodeBase64 }}" alt="QR Code" 
                    style="width: 250px; height: 250px; image-rendering: crisp-edges;">
                <div class="mt-2">
                    <small class="text-muted">Scan untuk simpan kontak</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Kartu Nama Digital Preview -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-credit-card me-1 text-danger"></i>Preview Kartu Nama Digital
                </h6>
            </div>
            <div class="card-body p-4">
                <!-- Business Card Styling -->
                <div style="max-width: 500px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white;">
                    @if($mecard->logo_path)
                    <div class="text-center pt-4">
                        <img src="{{ Storage::url($mecard->logo_path) }}" alt="Logo" style="max-width: 70px; max-height: 70px; border-radius: 12px;">
                    </div>
                    @endif
                    
                    <div class="p-4">
                        <h5 class="text-center mb-1 fw-bold">{{ $mecard->name }}</h5>
                        
                        @if($mecard->title || $mecard->company)
                        <p class="text-center text-muted small mb-3">
                            {{ $mecard->title }} @if($mecard->title && $mecard->company)@ @endif {{ $mecard->company }}
                        </p>
                        @endif
                        
                        <div class="mt-3">
                            <!-- Phones -->
                            @php $phones = $mecard->getAllPhones(); @endphp
                            @foreach($phones as $phone)
                            <div class="d-flex align-items-center mb-2 small">
                                <i class="bi bi-telephone text-primary me-2" style="width: 20px;"></i>
                                <span>{{ $phone['number'] }}</span>
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($phone['type'] ?? 'Phone') }}</span>
                            </div>
                            @endforeach
                            
                            <!-- Emails -->
                            @php $emails = $mecard->getAllEmails(); @endphp
                            @foreach($emails as $email)
                            <div class="d-flex align-items-center mb-2 small">
                                <i class="bi bi-envelope text-primary me-2" style="width: 20px;"></i>
                                <span>{{ $email['address'] }}</span>
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($email['type'] ?? 'Email') }}</span>
                            </div>
                            @endforeach
                            
                            <!-- Addresses -->
                            @php $addresses = $mecard->getAllAddresses(); @endphp
                            @foreach($addresses as $address)
                            <div class="d-flex align-items-start mb-2 small">
                                <i class="bi bi-geo-alt text-primary me-2 mt-1" style="width: 20px;"></i>
                                <span>{{ $address['text'] }}</span>
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($address['type'] ?? 'Address') }}</span>
                            </div>
                            @endforeach
                            
                            <!-- Company -->
                            @if($mecard->company)
                            <div class="d-flex align-items-center mb-2 small">
                                <i class="bi bi-building text-primary me-2" style="width: 20px;"></i>
                                <span>{{ $mecard->company }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Raw MECARD Data -->
        <div class="card mt-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-code-square me-1 text-info"></i>Raw MECARD Data
                </h6>
                <button class="btn btn-sm btn-outline-primary" onclick="copyMecard()">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="overflow-x: auto; font-size: 11px; white-space: pre-wrap; word-break: break-all;">{{ $mecard->toMeCard() }}</pre>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard() {
    var textarea = document.getElementById('mecardString');
    textarea.classList.remove('d-none');
    textarea.select();
    document.execCommand('copy');
    textarea.classList.add('d-none');
    
    alert('MECARD string telah disalin ke clipboard!');
}

function copyMecard() {
    var text = document.querySelector('#mecardRaw pre')?.innerText || '';
    navigator.clipboard.writeText(text).then(function() {
        alert('MECARD raw data telah disalin ke clipboard!');
    });
}

function saveToContact() {
    var vcard = generateVCard();
    var blob = new Blob([vcard], {type: 'text/vcard'});
    var url = URL.createObjectURL(blob);
    
    var link = document.createElement('a');
    link.href = url;
    link.download = '{{ \Illuminate\Support\Str::slug($mecard->name) }}.vcf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

function generateVCard() {
    var vcard = 'BEGIN:VCARD\r\n';
    vcard += 'VERSION:3.0\r\n';
    vcard += 'FN:{{ addslashes($mecard->name) }}\r\n';
    
    @if($mecard->title)
    vcard += 'TITLE:{{ addslashes($mecard->title) }}\r\n';
    @endif
    
    @if($mecard->company)
    vcard += 'ORG:{{ addslashes($mecard->company) }}\r\n';
    @endif
    
    @php $phones = $mecard->getAllPhones(); @endphp
    @foreach($phones as $phone)
    vcard += 'TEL;TYPE={{ strtoupper($phone['type'] ?? 'WORK') }}:{{ addslashes($phone['number']) }}\r\n';
    @endforeach
    
    @php $emails = $mecard->getAllEmails(); @endphp
    @foreach($emails as $email)
    vcard += 'EMAIL;TYPE={{ strtoupper($email['type'] ?? 'WORK') }}:{{ addslashes($email['address']) }}\r\n';
    @endforeach
    
    @php $addresses = $mecard->getAllAddresses(); @endphp
    @foreach($addresses as $address)
    vcard += 'ADR;TYPE={{ strtoupper($address['type'] ?? 'WORK') }}:;;;{{ addslashes($address['text']) }};;\r\n';
    @endforeach
    
    @if($mecard->note)
    vcard += 'NOTE:{{ addslashes($mecard->note) }}\r\n';
    @endif
    
    vcard += 'END:VCARD\r\n';
    
    return vcard;
}
</script>
@endpush