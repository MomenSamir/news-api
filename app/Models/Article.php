<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'source_name','source_id','title','author','summary',
        'content','url','image_url','category','published_at','language'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
