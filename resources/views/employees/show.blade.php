@extends('adminlte::page')

@section('title', 'Detail Karyawan')

@section('content_header')
    <h1>Detail Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">Nama Karyawan</th>
                    <td>{{ $employee->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $employee->email }}</td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td>{{ $employee->phone ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Posisi / Jabatan</th>
                    <td>{{ $employee->position }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $employee->department->name }}</td>
                </tr>
            </table>
            
            <div class="mt-4">
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@stop