<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'enterprise_id',
        'email',
        'status',
        'generated',
        'credit',
        'token',
    ];

    public function enterprise(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'enterprise_id');
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
