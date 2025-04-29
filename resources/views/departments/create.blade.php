@extends('adminlte::page')

@section('title', 'Tambah Departemen')

@section('content_header')
    <h1>Tambah Departemen</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Departemen</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="code">Kode Departemen</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                    @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop