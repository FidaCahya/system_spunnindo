@extends('adminlte::page')

@section('title', 'Detail Departemen')

@section('content_header')
    <h1>Detail Departemen</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">Kode Departemen</th>
                    <td>{{ $department->code }}</td>
                </tr>
                <tr>
                    <th>Nama Departemen</th>
                    <td>{{ $department->name }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $department->description ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Jumlah Karyawan</th>
                    <td>{{ $department->employees->count() }}</td>
                </tr>
            </table>
            
            <div class="mt-4">
                <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
            
            <div class="mt-4">
                <h4>Daftar Karyawan</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Posisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($department->employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->position }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada karyawan di departemen ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop