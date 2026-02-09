<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable=[
        'title',
        'description',
        'price',
        'category_id',
        'user_id',
        'is_active'
    ];

    protected $cast = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
