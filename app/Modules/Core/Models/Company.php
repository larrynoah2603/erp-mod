<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'legal_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'siret',
        'vat_number',
        'logo_path',
        'settings',
        'working_hours',
        'is_active',
    ];
    
    protected $casts = [
        'settings' => 'array',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function roles()
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class);
    }
    
    public function getWorkingHoursAttribute($value)
    {
        $default = [
            'monday' => ['start' => '09:00', 'end' => '18:00'],
            'tuesday' => ['start' => '09:00', 'end' => '18:00'],
            'wednesday' => ['start' => '09:00', 'end' => '18:00'],
            'thursday' => ['start' => '09:00', 'end' => '18:00'],
            'friday' => ['start' => '09:00', 'end' => '17:00'],
            'saturday' => ['start' => null, 'end' => null],
            'sunday' => ['start' => null, 'end' => null],
        ];
        
        return json_decode($value, true) ?? $default;
    }
}