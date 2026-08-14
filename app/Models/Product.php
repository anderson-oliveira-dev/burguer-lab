<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'image', 'status', 'price', 'description', 'preparation_time'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('images/products/' . $this->image)
            : asset('images/products/default.jpg');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponivel');
    }
}
