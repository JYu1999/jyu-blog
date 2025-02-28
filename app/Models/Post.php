<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'title',
        'slug',
        'content',
        'summary',
        'featured_image',
        'category_id',
        'status',
        'views',
    ];
    
    protected $casts = [
        'views' => 'integer',
    ];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }
    
    public function incrementViews(): void
    {
        $this->increment('views');
    }
    
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }
}
