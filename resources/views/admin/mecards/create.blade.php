@extends('admin.layouts.app')

@section('title', 'Buat MeCard')
@section('page-title', 'Buat MeCard Baru')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.mecards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Nama -->
                    <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Perusahaan -->
                    <div class="mb-3">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" name="company" class="form-control @error('company') is-invalid @enderror" value="{{ old('company') }}">
                        @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Phone Numbers (Multiple) -->
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <div id="phone-container">
                            <div class="input-group mb-2 phone-item">
                                <select name="phones[0][type]" class="form-select" style="max-width: 120px">
                                    <option value="WORK">Kantor</option>
                                    <option value="CELL">HP</option>
                                    <option value="HOME">Rumah</option>
                                    <option value="FAX">Fax</option>
                                </select>
                                <input type="text" name="phones[0][number]" class="form-control" placeholder="Nomor telepon">
                                <button type="button" class="btn btn-danger remove-phone" style="display: none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addPhone">
                            <i class="bi bi-plus-circle"></i> Tambah Nomor
                        </button>
                    </div>

                    <!-- Emails (Multiple) -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div id="email-container">
                            <div class="input-group mb-2 email-item">
                                <select name="emails[0][type]" class="form-select" style="max-width: 120px">
                                    <option value="WORK">Kantor</option>
                                    <option value="PERSONAL">Personal</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                                <input type="email" name="emails[0][address]" class="form-control" placeholder="Alamat email">
                                <button type="button" class="btn btn-danger remove-email" style="display: none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addEmail">
                            <i class="bi bi-plus-circle"></i> Tambah Email
                        </button>
                    </div>

                    <!-- Addresses (Multiple) -->
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <div id="address-container">
                            <div class="mb-2 address-item">
                                <div class="input-group mb-2">
                                    <select name="addresses[0][type]" class="form-select" style="max-width: 120px">
                                        <option value="WORK">Kantor</option>
                                        <option value="HOME">Rumah</option>
                                        <option value="OTHER">Lainnya</option>
                                    </select>
                                    <input type="text" name="addresses[0][text]" class="form-control" placeholder="Alamat lengkap">
                                    <button type="button" class="btn btn-danger remove-address" style="display: none">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addAddress">
                            <i class="bi bi-plus-circle"></i> Tambah Alamat
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Social Media -->
                    <div class="mb-3">
                        <label class="form-label">Social Media / Website</label>
                        <div id="social-container">
                            <div class="input-group mb-2 social-item">
                                <select name="socials[0][type]" class="form-select" style="max-width: 140px">
                                    <option value="WEBSITE">Website</option>
                                    <option value="LINKEDIN">LinkedIn</option>
                                    <option value="INSTAGRAM">Instagram</option>
                                    <option value="FACEBOOK">Facebook</option>
                                    <option value="TWITTER">Twitter/X</option>
                                    <option value="GITHUB">GitHub</option>
                                    <option value="WHATSAPP">WhatsApp</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                                <input type="url" name="socials[0][url]" class="form-control" placeholder="URL">
                                <button type="button" class="btn btn-danger remove-social" style="display: none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addSocial">
                            <i class="bi bi-plus-circle"></i> Tambah Social Media
                        </button>
                    </div>

                    <!-- Custom Fields -->
                    <div class="mb-3">
                        <label class="form-label">Informasi Tambahan</label>
                        <div id="custom-container">
                            <div class="input-group mb-2 custom-item">
                                <input type="text" name="custom_fields[0][label]" class="form-control" placeholder="Label (contoh: NPWP, Hobi)" style="max-width: 150px">
                                <input type="text" name="custom_fields[0][value]" class="form-control" placeholder="Nilai">
                                <button type="button" class="btn btn-danger remove-custom" style="display: none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="addCustom">
                            <i class="bi bi-plus-circle"></i> Tambah Field
                        </button>
                    </div>

                    <!-- Note -->
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('note') }}</textarea>
                    </div>

                    <!-- Logo -->
                    <div class="mb-3">
                        <label class="form-label">Logo / Foto</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan MeCard
                </button>
                <a href="{{ route('admin.mecards.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let phoneIndex = 1;
let emailIndex = 1;
let addressIndex = 1;
let socialIndex = 1;
let customIndex = 1;

function updateIndices() {
    // Update phone indices
    $('.phone-item').each(function(idx) {
        $(this).find('select[name^="phones"]').attr('name', `phones[${idx}][type]`);
        $(this).find('input[name^="phones"]').attr('name', `phones[${idx}][number]`);
    });
    
    // Update email indices
    $('.email-item').each(function(idx) {
        $(this).find('select[name^="emails"]').attr('name', `emails[${idx}][type]`);
        $(this).find('input[name^="emails"]').attr('name', `emails[${idx}][address]`);
    });
    
    // Update address indices
    $('.address-item').each(function(idx) {
        $(this).find('select[name^="addresses"]').attr('name', `addresses[${idx}][type]`);
        $(this).find('input[name^="addresses"]').attr('name', `addresses[${idx}][text]`);
    });
    
    // Update social indices
    $('.social-item').each(function(idx) {
        $(this).find('select[name^="socials"]').attr('name', `socials[${idx}][type]`);
        $(this).find('input[name^="socials"]').attr('name', `socials[${idx}][url]`);
    });
    
    // Update custom indices
    $('.custom-item').each(function(idx) {
        $(this).find('input[name^="custom_fields"]:eq(0)').attr('name', `custom_fields[${idx}][label]`);
        $(this).find('input[name^="custom_fields"]:eq(1)').attr('name', `custom_fields[${idx}][value]`);
    });
}

// Add Phone
$('#addPhone').click(function() {
    let newIndex = $('.phone-item').length;
    let newPhone = `
        <div class="input-group mb-2 phone-item">
            <select name="phones[${newIndex}][type]" class="form-select" style="max-width: 120px">
                <option value="WORK">Kantor</option>
                <option value="CELL">HP</option>
                <option value="HOME">Rumah</option>
                <option value="FAX">Fax</option>
            </select>
            <input type="text" name="phones[${newIndex}][number]" class="form-control" placeholder="Nomor telepon">
            <button type="button" class="btn btn-danger remove-phone">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    $('#phone-container').append(newPhone);
    $('.remove-phone').show();
});

$(document).on('click', '.remove-phone', function() {
    $(this).closest('.phone-item').remove();
    updateIndices();
    if ($('.phone-item').length === 1) $('.remove-phone').hide();
});

// Add Email
$('#addEmail').click(function() {
    let newIndex = $('.email-item').length;
    let newEmail = `
        <div class="input-group mb-2 email-item">
            <select name="emails[${newIndex}][type]" class="form-select" style="max-width: 120px">
                <option value="WORK">Kantor</option>
                <option value="PERSONAL">Personal</option>
                <option value="OTHER">Lainnya</option>
            </select>
            <input type="email" name="emails[${newIndex}][address]" class="form-control" placeholder="Alamat email">
            <button type="button" class="btn btn-danger remove-email">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    $('#email-container').append(newEmail);
    $('.remove-email').show();
});

$(document).on('click', '.remove-email', function() {
    $(this).closest('.email-item').remove();
    updateIndices();
    if ($('.email-item').length === 1) $('.remove-email').hide();
});

// Add Address
$('#addAddress').click(function() {
    let newIndex = $('.address-item').length;
    let newAddress = `
        <div class="mb-2 address-item">
            <div class="input-group mb-2">
                <select name="addresses[${newIndex}][type]" class="form-select" style="max-width: 120px">
                    <option value="WORK">Kantor</option>
                    <option value="HOME">Rumah</option>
                    <option value="OTHER">Lainnya</option>
                </select>
                <input type="text" name="addresses[${newIndex}][text]" class="form-control" placeholder="Alamat lengkap">
                <button type="button" class="btn btn-danger remove-address">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#address-container').append(newAddress);
    $('.remove-address').show();
});

$(document).on('click', '.remove-address', function() {
    $(this).closest('.address-item').remove();
    updateIndices();
    if ($('.address-item').length === 1) $('.remove-address').hide();
});

// Add Social
$('#addSocial').click(function() {
    let newIndex = $('.social-item').length;
    let newSocial = `
        <div class="input-group mb-2 social-item">
            <select name="socials[${newIndex}][type]" class="form-select" style="max-width: 140px">
                <option value="WEBSITE">Website</option>
                <option value="LINKEDIN">LinkedIn</option>
                <option value="INSTAGRAM">Instagram</option>
                <option value="FACEBOOK">Facebook</option>
                <option value="TWITTER">Twitter/X</option>
                <option value="GITHUB">GitHub</option>
                <option value="WHATSAPP">WhatsApp</option>
                <option value="OTHER">Lainnya</option>
            </select>
            <input type="url" name="socials[${newIndex}][url]" class="form-control" placeholder="URL">
            <button type="button" class="btn btn-danger remove-social">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    $('#social-container').append(newSocial);
    $('.remove-social').show();
});

$(document).on('click', '.remove-social', function() {
    $(this).closest('.social-item').remove();
    updateIndices();
    if ($('.social-item').length === 1) $('.remove-social').hide();
});

// Add Custom
$('#addCustom').click(function() {
    let newIndex = $('.custom-item').length;
    let newCustom = `
        <div class="input-group mb-2 custom-item">
            <input type="text" name="custom_fields[${newIndex}][label]" class="form-control" placeholder="Label" style="max-width: 150px">
            <input type="text" name="custom_fields[${newIndex}][value]" class="form-control" placeholder="Nilai">
            <button type="button" class="btn btn-danger remove-custom">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    $('#custom-container').append(newCustom);
    $('.remove-custom').show();
});

$(document).on('click', '.remove-custom', function() {
    $(this).closest('.custom-item').remove();
    updateIndices();
    if ($('.custom-item').length === 1) $('.remove-custom').hide();
});

// Initial hide of remove buttons
if ($('.phone-item').length === 1) $('.remove-phone').hide();
if ($('.email-item').length === 1) $('.remove-email').hide();
if ($('.address-item').length === 1) $('.remove-address').hide();
if ($('.social-item').length === 1) $('.remove-social').hide();
if ($('.custom-item').length === 1) $('.remove-custom').hide();
</script>
@endpush
@endsection