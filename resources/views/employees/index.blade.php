@extends('adminlte::page')

@section('title', 'Daftar Karyawan')

@section('content_header')
    <h1>Daftar Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-right">
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Posisi</th>
                        <th>Departemen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ $employee->department->name }}</td>
                            <td>
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus karyawan ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data karyawan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop