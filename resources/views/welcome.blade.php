@extends('layouts.app')

@section('title', $judul)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ $judul }}</div>
            <div class="card-body text-center">
                <h3>{{ $hari }}</h3>
                <p class="lead">Halo, {{ $nama }}!</p>
                <p>Hari ini tanggal: {{ date('d F Y') }}</p>
                <a href="/halo" class="btn btn-success">Coba Halaman Halo</a>
                <a href="/about" class="btn btn-outline-secondary">Halaman About</a>
            </div>
        </div>
    </div>
</div>
@endsection