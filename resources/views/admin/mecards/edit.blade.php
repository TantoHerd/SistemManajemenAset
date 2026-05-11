@extends('admin.layouts.app')

@section('title', 'Edit MeCard')
@section('page-title', 'Edit MeCard')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mecards.update', $mecard) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $mecard->name }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="title" class="form-control" value="{{ $mecard->title }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ $mecard->phone }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $mecard->email }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="company" class="form-control" value="{{ $mecard->company }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="address" class="form-control" value="{{ $mecard->address }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2">{{ $mecard->note }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('admin.mecards.index') }}" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection