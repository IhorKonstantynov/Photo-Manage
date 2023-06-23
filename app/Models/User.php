<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'plan_id',
        'spent',
        'permission',
        'account_type',
        'company_name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'promo_code',
        'click_count',
        'credit',
        'available_employee',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function plan(): HasOne
    {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }
    public function enterprise(): HasMany
    {
        return $this->hasMany(Employee::class, 'email', 'email');
    }
    public function photos(): HasMany
    {
        return $this->hasMany(Photos::class, 'user_id', 'id');
    }

    public function photoEdit(): HasMany
    {
        return $this->hasMany(PhotoEdit::class, 'user_id', 'id');
    }

    public function checkPlan()
    {
        return isset($this->plan_id) || ($this->account_type == 1 && $this->available_employee > 0);
    }

    public function isDiscount()
    {
        return isset($this->promo_code) && !empty($this->promo_code) && ($this->account_type == 0);
    }

    /**
     * Update user Balance.
     */

    public function setCredit($amount = -1)
    {
        if($amount == -1 && $this->credit == 0) {
            return false;
        }
        $this->credit += $amount;
        $this->save();
        return true;
    }

    public function checkCredit()
    {
        return $this->credit > 0;
    }

    public function isAdmin()
    {
        return $this->permission == 1;
    }

    public function isEnterprise()
    {
        return $this->account_type == 1;
    }

    public function increase_click_button()
    {
        $this->click_count++;
        $this->save();
    }
}