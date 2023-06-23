<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoEdit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_id',
        'ind',
        'original_img',
        'bg_removed_img',
        'bg_custom',
        'bg_custom_status',
        'edit_img',
    ];
}
