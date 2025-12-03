<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'bio'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCoadmin()
    {
        return $this->role === 'coadmin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // Method untuk scope query
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeCoadmins($query)
    {
        return $query->where('role', 'coadmin');
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('role', 'user');
    }

    // Method untuk panggilan personal berdasarkan role
    public function getPersonalGreeting()
    {
        $greetings = [
            'admin' => 'Yang Mulia Administrator',
            'coadmin' => 'Tuan/Ko-Administrator', 
            'user' => 'Saudara/i'
        ];

        return $greetings[$this->role] ?? 'Saudara/i';
    }

    // Method untuk sambutan lengkap
    public function getFormalGreeting()
    {
        return $this->getPersonalGreeting() . ' ' . $this->name;
    }

    // Method untuk sambutan informal
    public function getInformalGreeting()
    {
        return 'Halo, ' . $this->name . '!';
    }
}