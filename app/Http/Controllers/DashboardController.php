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

        $departments = Department::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->get();
        
        return view('dashboard', compact('departmentCount', 'employeeCount', 'departments'));
    }
}
