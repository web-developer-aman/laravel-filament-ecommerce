<?php

namespace App\Models\Blog;

use Spatie\Tags\HasTags;
use App\Models\Blog\Author;
use App\Models\Blog\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    use HasTags;

    protected $table = 'blog_posts';
    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'image',
        'blog_author_id',
        'blog_category_id',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    /**
     * Get the author that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'blog_author_id');
    }

    /**
     * Get the category that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'blog_category_id');
    }
}
