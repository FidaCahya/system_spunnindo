<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'department_id',
    ];
    
    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Scope a query to filter employees by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        if ($departmentId) {
            return $query->where('department_id', $departmentId);
        }
        return $query;
    }
    
    /**
     * Scope a query to search employees.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}