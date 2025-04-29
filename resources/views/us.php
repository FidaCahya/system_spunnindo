
**Sistem Manajemen Departemen dan Karyawan Perusahaan**

Buatlah sebuah aplikasi web sederhana menggunakan Laravel untuk mengelola departemen dan karyawan perusahaan dengan ketentuan:

1. Ada halaman login untuk admin
2. Admin dapat mengelola (CRUD) data departemen
3. Admin dapat mengelola (CRUD) data karyawan dan mengatur departemen untuk setiap karyawan
4. Ada dashboard yang menampilkan jumlah departemen dan karyawan
5. Tampilkan daftar departemen beserta jumlah karyawan di setiap departemen

## Langkah-Langkah Pengerjaan Detail (Untuk Dikerjakan dalam 2 Jam)

### 1. Persiapan Lingkungan (10 menit)
- Buat project Laravel baru
- Konfigurasi database dan environment
- Install AdminLTE untuk tampilan

### 2. Perancangan Database (10 menit)
- Buat migration untuk tabel users, departments, dan employees
- Siapkan relasi antar tabel

### 3. Implementasi Authentication (15 menit)
- Buat sistem login manual tanpa Laravel Breeze/UI
- Integrasikan dengan AdminLTE

### 4. Implementasi Fitur Departemen (25 menit)
- Buat model, migration, controller untuk departemen
- Implementasi CRUD departemen
- Buat tampilan menggunakan AdminLTE

### 5. Implementasi Fitur Karyawan (25 menit)
- Buat model, migration, controller untuk karyawan
- Implementasi CRUD karyawan dengan relasi ke departemen
- Buat tampilan menggunakan AdminLTE

### 6. Implementasi Dashboard (15 menit)
- Buat halaman dashboard dengan statistik
- Tampilkan data departemen dan jumlah karyawan

### 7. Query Khusus (10 menit)
- Implementasi query untuk menampilkan data departemen beserta jumlah karyawannya
- Implementasi sorting dan filtering data

### 8. Testing dan Debugging (10 menit)
- Pastikan semua fitur berfungsi dengan baik
- Periksa tampilan di berbagai ukuran layar
- Pastikan tidak ada bug pada aplikasi

Mari kita mulai dengan panduan detail untuk masing-masing langkah:

## Panduan Detail Pengerjaan

### 1. Persiapan Lingkungan (10 menit)

#### Buat Project Laravel Baru
```bash
composer create-project laravel/laravel manajemen-karyawan
cd manajemen-karyawan
```

#### Konfigurasi Database (.env)
Buka file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_manajemen_karyawan
DB_USERNAME=root
DB_PASSWORD=
```

#### Install AdminLTE
```bash
npm install
composer require jeroennoten/laravel-adminlte
php artisan adminlte:install
```

### 2. Perancangan Database (10 menit)

#### Migration Users (jika belum ada)
File sudah ada dari Laravel: `database/migrations/{timestamp}_create_users_table.php`

#### Migration Departments
Buat migration untuk tabel departments:
```bash
php artisan make:migration create_departments_table
```

Edit file migration yang dibuat:
```php
public function up()
{
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique();
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
```

#### Migration Employees
Buat migration untuk tabel employees:
```bash
php artisan make:migration create_employees_table
```

Edit file migration yang dibuat:
```php
public function up()
{
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->string('position');
        $table->unsignedBigInteger('department_id');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        $table->timestamps();
    });
}
```

#### Jalankan Migration
```bash
php artisan migrate
```

### 3. Implementasi Authentication (15 menit)

#### Membuat Seeder untuk User Admin
```bash
php artisan make:seeder AdminUserSeeder
```

Edit file seeder:
```php
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=AdminUserSeeder
```

#### Buat Controller Auth
```bash
php artisan make:controller AuthController
```

Edit `app/Http/Controllers/AuthController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
```

#### Buat View Login
Buat file `resources/views/auth/login.blade.php`:
```php
@extends('adminlte::master')

@section('adminlte_css')
    @yield('css')
@stop

@section('body_class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}">Manajemen <b>Karyawan</b></a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Silahkan login untuk memulai sesi</p>

                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    @yield('js')
@stop
```

#### Setup Routes
Edit `routes/web.php`:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Routes yang memerlukan autentikasi akan ditambahkan di sini
});
```

### 4. Implementasi Fitur Departemen (25 menit)

#### Membuat Model Departemen
```bash
php artisan make:model Department
```

Edit `app/Models/Department.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'code', 'description'];
    
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
```

#### Membuat Controller Departemen
```bash
php artisan make:controller DepartmentController --resource
```

Edit `app/Http/Controllers/DepartmentController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments',
            'description' => 'nullable|string',
        ]);
        
        Department::create($validated);
        
        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function show(Department $department)
    {
        $department->load('employees');
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
        ]);
        
        $department->update($validated);
        
        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        
        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
```

#### Tambahkan Route Departemen
Edit `routes/web.php` tambahkan dalam grup middleware auth:
```php
use App\Http\Controllers\DepartmentController;

Route::middleware(['auth'])->group(function () {
    Route::resource('departments', DepartmentController::class);
});
```

#### Buat View Departemen
Buat file `resources/views/departments/index.blade.php`:
```php
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
```

Buat file `resources/views/departments/create.blade.php`:
```php
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
```

Buat file `resources/views/departments/edit.blade.php`:
```php
@extends('adminlte::page')

@section('title', 'Edit Departemen')

@section('content_header')
    <h1>Edit Departemen</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama Departemen</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="code">Kode Departemen</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $department->code) }}" required>
                    @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop
```

Buat file `resources/views/departments/show.blade.php`:
```php
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
```

### 5. Implementasi Fitur Karyawan (25 menit)

#### Membuat Model Karyawan
```bash
php artisan make:model Employee
```

Edit `app/Models/Employee.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'email', 'phone', 'position', 'department_id'];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
```

#### Membuat Controller Karyawan
```bash
php artisan make:controller EmployeeController --resource
```

Edit `app/Http/Controllers/EmployeeController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        Employee::create($validated);
        
        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $employee->load('department');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        $employee->update($validated);
        
        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
```

#### Tambahkan Route Karyawan
Edit `routes/web.php` tambahkan dalam grup middleware auth:
```php
use App\Http\Controllers\EmployeeController;

Route::middleware(['auth'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees', EmployeeController::class);
});
```

#### Buat View Karyawan
Buat file `resources/views/employees/index.blade.php`:
```php
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
```

Buat file `resources/views/employees/create.blade.php`:
```php
@extends('adminlte::page')

@section('title', 'Tambah Karyawan')

@section('content_header')
    <h1>Tambah Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Karyawan</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="position">Posisi / Jabatan</label>
                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" required>
                    @error('position')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="department_id">Departemen</label>
                    <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop
```

Buat file `resources/views/employees/edit.blade.php`:
```php
@extends('adminlte::page')

@section('title', 'Edit Karyawan')

@section('content_header')
    <h1>Edit Karyawan</h1>
@stop

@section('content


Melanjutkan pembuatan panduan detail untuk pengerjaan:

Buat file `resources/views/employees/edit.blade.php`:
```php
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
```

Buat file `resources/views/employees/show.blade.php`:
```php
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
```

### 6. Implementasi Dashboard (15 menit)

#### Membuat Controller Dashboard
```bash
php artisan make:controller DashboardController
```

Edit `app/Http/Controllers/DashboardController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $departmentCount = Department::count();
        $employeeCount = Employee::count();
        
        // Departemen dengan jumlah karyawan
        $departments = Department::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->get();
        
        return view('dashboard', compact('departmentCount', 'employeeCount', 'departments'));
    }
}
```

#### Tambahkan Route Dashboard
Edit `routes/web.php` tambahkan dalam grup middleware auth:
```php
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees', EmployeeController::class);
});
```

#### Buat View Dashboard
Buat file `resources/views/dashboard.blade.php`:
```php
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
```

### 7. Query Khusus (10 menit)

Kita sudah mengimplementasikan query khusus untuk menampilkan data departemen beserta jumlah karyawannya pada Dashboard dan halaman Departments.

Untuk menambahkan fitur sorting dan filtering pada halaman karyawan, edit `app/Http/Controllers/EmployeeController.php`:

```php
public function index(Request $request)
{
    $query = Employee::with('department');
    
    // Filter berdasarkan departemen
    if ($request->has('department_id') && !empty($request->department_id)) {
        $query->where('department_id', $request->department_id);
    }
    
    // Filter berdasarkan pencarian
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%");
        });
    }
    
    // Sorting
    $sortBy = $request->sort_by ?? 'name';
    $sortOrder = $request->sort_order ?? 'asc';
    $query->orderBy($sortBy, $sortOrder);
    
    $employees = $query->get();
    $departments = Department::all();
    
    return view('employees.index', compact('employees', 'departments'));
}
```

Kemudian, update file `resources/views/employees/index.blade.php` untuk menambahkan filter:

```php
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

            <form action="{{ route('employees.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Departemen</label>
                            <select name="department_id" class="form-control">
                                <option value="">Semua Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, email atau posisi" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutkan Berdasarkan</label>
                            <select name="sort_by" class="form-control">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="position" {{ request('sort_by') == 'position' ? 'selected' : '' }}>Posisi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutan</label>
                            <select name="sort_order" class="form-control">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Naik (A-Z)</option>
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Turun (Z-A)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset</a>
            </form>

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
```

### 8. Testing dan Debugging (10 menit)

#### Seeder Data Contoh
Untuk mempercepat testing, buat seeder untuk data contoh:
```bash
php artisan make:seeder DepartmentSeeder
php artisan make:seeder EmployeeSeeder
```

Edit `database/seeders/DepartmentSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Mengelola sumber daya manusia dan administrasi karyawan'
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Mengelola infrastruktur teknologi dan pengembangan software'
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Mengelola keuangan dan akuntansi perusahaan'
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Mengelola pemasaran dan promosi produk/jasa'
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
```

Edit `database/seeders/EmployeeSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '081234567890',
                'position' => 'HR Manager',
                'department_id' => 1
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '081234567891',
                'position' => 'HR Staff',
                'department_id' => 1
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael@example.com',
                'phone' => '081234567892',
                'position' => 'IT Manager',
                'department_id' => 2
            ],
            [
                'name' => 'Susan Williams',
                'email' => 'susan@example.com',
                'phone' => '081234567893',
                'position' => 'Software Developer',
                'department_id' => 2
            ],
            [
                'name' => 'Robert Brown',
                'email' => 'robert@example.com',
                'phone' => '081234567894',
                'position' => 'Finance Manager',
                'department_id' => 3
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'phone' => '081234567895',
                'position' => 'Accountant',
                'department_id' => 3
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@example.com',
                'phone' => '081234567896',
                'position' => 'Marketing Manager',
                'department_id' => 4
            ],
            [
                'name' => 'Sarah Taylor',
                'email' => 'sarah@example.com',
                'phone' => '081234567897',
                'position' => 'Marketing Staff',
                'department_id' => 4
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
```

Edit `database/seeders/DatabaseSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
```

Jalankan semua seeder:
```bash
php artisan db:seed
```

#### Uji Coba Aplikasi

Jalankan aplikasi:
```bash
php artisan serve
```

Kemudian akses aplikasi di browser dengan URL `http://localhost:8000`

Cek hal-hal berikut:
1. Login dengan email: admin@example.com dan password: password
2. Pastikan halaman dashboard menampilkan statistik dengan benar
3. Cek fungsi CRUD departemen
4. Cek fungsi CRUD karyawan
5. Pastikan fitur filter dan sorting berfungsi dengan baik
6. Cek responsivitas tampilan di berbagai ukuran layar

## Checklist Pengerjaan

Berikut adalah checklist untuk memastikan semua fitur telah selesai:

### Persiapan
- [x] Project Laravel baru dibuat
- [x] Database dikonfigurasi
- [x] AdminLTE diinstall

### Database
- [x] Tabel users (bawaan Laravel)
- [x] Tabel departments dibuat
- [x] Tabel employees dibuat dengan relasi ke departments
- [x] Seeder untuk data contoh

### Authentication
- [x] Sistem login manual dibuat
- [x] View login dengan AdminLTE
- [x] Middleware auth diterapkan pada route yang memerlukan login

### Fitur Departemen
- [x] Model Department dibuat
- [x] Controller dengan CRUD lengkap
- [x] View untuk list, create, edit, detail departemen
- [x] Validasi input

### Fitur Karyawan
- [x] Model Employee dibuat dengan relasi ke Department
- [x] Controller dengan CRUD lengkap
- [x] View untuk list, create, edit, detail karyawan
- [x] Validasi input
- [x] Filter dan sorting

### Dashboard
- [x] Menampilkan jumlah departemen dan karyawan
- [x] Menampilkan grafik distribusi karyawan per departemen
- [x] Menampilkan tabel departemen dengan jumlah karyawan

### Query Khusus
- [x] Query untuk menampilkan departemen beserta jumlah karyawannya
- [x] Fitur filter dan sorting pada data karyawan

### Testing
- [x] Semua fitur berfungsi dengan baik
- [x] Tampilan responsif di berbagai ukuran layar
- [x] Tidak ada bug pada aplikasi

## Catatan Penting

1. **Manajemen Waktu**: Pastikan Anda mengerjakan sesuai alokasi waktu yang diberikan. Jika ada bagian yang sulit, lanjutkan ke bagian lain dan kembali jika masih ada waktu.

2. **Fokus pada Fungsi Utama**: Prioritaskan fungsi CRUD dan login yang berfungsi dengan benar. Styling dapat menjadi prioritas terakhir.

3. **Pengecekan Kesalahan**: Pastikan tidak ada error pada aplikasi, terutama pada validasi input dan relasi antar tabel.

4. **Sesuaikan dengan Kebutuhan**: Anda bisa menyesuaikan kode sesuai kebutuhan atau menambahkan fitur jika masih ada waktu.

Dengan mengikuti panduan ini, Anda akan dapat menyelesaikan studi kasus Laravel untuk tes magang di Spunindo dalam waktu 2 jam. Semoga berhasil!