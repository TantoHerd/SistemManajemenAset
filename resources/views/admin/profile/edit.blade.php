@extends('admin.layouts.app')

@section('title', 'Profile Saya')
@section('page-title', 'Profile Saya')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
    
    <!-- Avatar + Info -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center py-4">
                <!-- Avatar -->
                <div class="text-center mb-3">
                    @if(Auth::user()->avatar && file_exists(public_path('storage/' . Auth::user()->avatar)))
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                            id="avatarPreview"
                            class="rounded-circle" 
                            alt="Avatar"
                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer;"
                            onclick="document.getElementById('avatarInput').click()">
                    @else
                        <div id="avatarPreview" 
                            class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                            style="width: 120px; height: 120px; font-size: 40px; font-weight: bold; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer;"
                            onclick="document.getElementById('avatarInput').click()">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <h5 class="mt-3 mb-1 fw-bold">{{ Auth::user()->name }}</h5>
                <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                
                @php $userRole = Auth::user()->roles->first()->name ?? 'user'; @endphp
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-shield me-1"></i>{{ ucfirst(str_replace('_', ' ', $userRole)) }}
                </span>
                
                <hr>
                
                <!-- Upload Avatar -->
                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    @method('PATCH')
                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                    <input type="hidden" name="cropped_image" id="croppedImage">
                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="document.getElementById('avatarInput').click()">
                        <i class="bi bi-camera me-1"></i>Pilih Foto
                    </button>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle"></i> Klik foto untuk ganti, crop sebelum upload
                    </small>
                </form>
                
                <div class="mt-3 text-muted small">
                    <i class="bi bi-calendar me-1"></i>Bergabung {{ Auth::user()->created_at->format('d F Y') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Profile + Password -->
    <div class="col-lg-8">
        
        <!-- Informasi Profile -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person me-1 text-primary"></i>Informasi Profile</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone', Auth::user()->phone) }}" placeholder="0812xxxxxxxx">
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="address" class="form-control" 
                                       value="{{ old('address', Auth::user()->address) }}" placeholder="Alamat lengkap">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Ganti Password -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lock me-1 text-warning"></i>Ganti Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Password Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       required placeholder="••••••••">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required placeholder="Min. 8 karakter">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       required placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning mt-3">
                        <i class="bi bi-shield-lock me-1"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal Crop -->
        <div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="bi bi-crop me-1"></i>Sesuaikan Foto</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div style="max-height: 400px;">
                            <img id="cropImage" style="max-width: 100%; display: none;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="cropBtn">
                            <i class="bi bi-check-lg me-1"></i>Simpan Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;

    // Saat file dipilih
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Cek ukuran (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File terlalu besar! Max 2MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = event.target.result;
                cropImage.style.display = 'block';
                
                // Destroy cropper lama
                if (cropper) cropper.destroy();
                
                // Init cropper
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, // 1:1 square
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });
                
                // Show modal
                new bootstrap.Modal(document.getElementById('cropModal')).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Tombol Simpan Crop
    document.getElementById('cropBtn').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
            });
            
            // Convert ke base64
            const croppedImage = canvas.toDataURL('image/jpeg', 0.8);
            
            // Set hidden input
            document.getElementById('croppedImage').value = croppedImage;
            
            // Update preview
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = croppedImage;
            } else {
                // Ganti div jadi img
                const img = document.createElement('img');
                img.src = croppedImage;
                img.id = 'avatarPreview';
                img.className = 'rounded-circle';
                img.style.cssText = 'width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer;';
                img.onclick = function() { document.getElementById('avatarInput').click(); };
                preview.parentNode.replaceChild(img, preview);
            }
            
            // Show save button
            if (!document.getElementById('saveAvatarBtn')) {
                const btn = document.createElement('button');
                btn.id = 'saveAvatarBtn';
                btn.type = 'button';
                btn.className = 'btn btn-success btn-sm w-100 mt-2';
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Simpan Foto';
                btn.onclick = function() {
                    document.getElementById('avatarForm').submit();
                };
                document.getElementById('avatarForm').appendChild(btn);
            }
            
            // Hide modal
            bootstrap.Modal.getInstance(document.getElementById('cropModal')).hide();
            
            // Destroy cropper
            cropper.destroy();
            cropper = null;
        }
    });

    // Reset input saat modal ditutup tanpa save
    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('avatarInput').value = '';
        document.getElementById('cropImage').style.display = 'none';
    });
</script>
@endpush
@push('styles')
<style>
@media (max-width: 576px) {
    .card-body {
        padding: 15px;
    }
    .btn {
        width: 100%;
    }
}
</style>
@endpush