@extends('adminlte::page')

@section('title', 'Edit Karyawan')

@section('content_header')
    <h1>Edit Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama Karyawan</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="position">Posisi / Jabatan</label>
                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                    @error('position')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="department_id">Departemen</label>
                    <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop