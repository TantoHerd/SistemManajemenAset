@extends('admin.layouts.app')

@section('title', 'Buat MeCard')
@section('page-title', 'Buat MeCard Baru')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mecards.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama kontak">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="title" class="form-control" placeholder="Jabatan / Posisi">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="0812xxxxxxxx">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="company" class="form-control" placeholder="Nama perusahaan">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="address" class="form-control" placeholder="Alamat lengkap">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('admin.mecards.index') }}" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection