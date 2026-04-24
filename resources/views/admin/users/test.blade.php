@extends('admin.layouts.app')

@section('title', 'Test')
@section('page-title', 'Test Page')

@section('header-actions')
    <a href="#" class="btn btn-primary">Test Button</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <p>Halaman test berhasil diload</p>
    </div>
</div>
@endsection