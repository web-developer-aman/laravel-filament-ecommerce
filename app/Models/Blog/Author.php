<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $table = 'blog_authors';
    protected $fillable = [
        'name',
        'email',
        'photo',
        'bio',
        'github',
        'twitter'
    ];
}
