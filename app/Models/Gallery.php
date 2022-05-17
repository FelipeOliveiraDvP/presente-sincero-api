<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'contest_id',
        'image_path',
        'thumbnail'
    ];

    protected $casts = [
        'thumbnail' => 'boolean'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
