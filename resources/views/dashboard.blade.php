@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $departmentCount }}</h3>
                    <p>Total Departemen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('departments.index') }}" class="small-box-footer">
                    Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $employeeCount }}</h3>
                    <p>Total Karyawan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('employees.index') }}" class="small-box-footer">
                    Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Departemen dan Jumlah Karyawan</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Departemen dan Jumlah Karyawan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Departemen</th>
                                <th>Kode</th>
                                <th>Jumlah Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $department)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->code }}</td>
                                    <td>{{ $department->employees_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data departemen</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function () {
            // Data untuk grafik
            var departments = @json($departments->pluck('name'));
            var employeeCounts = @json($departments->pluck('employees_count'));
            
            // Buat grafik bar
            var ctx = document.getElementById('barChart').getContext('2d');
            var barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departments,
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: employeeCounts,
                        backgroundColor: 'rgba(60,141,188,0.8)',
                        borderColor: 'rgba(60,141,188,1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
