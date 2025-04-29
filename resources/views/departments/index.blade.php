@extends('adminlte::page')

@section('title', 'Daftar Departemen')

@section('content_header')
    <h1>Daftar Departemen</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-right">
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Departemen
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
                        <th>Kode</th>
                        <th>Nama Departemen</th>
                        <th>Jumlah Karyawan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $department->code }}</td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->employees_count }}</td>
                            <td>
                                <a href="{{ route('departments.show', $department) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('departments.destroy', $department) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus departemen ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data departemen</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop