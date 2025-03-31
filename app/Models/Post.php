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
        'locale',
        'original_post_id',
    ];

    protected $casts = [
        'views' => 'integer',
        'content_updated_at' => 'datetime',
    ];

    protected $appends = [
        'relative_updated_at',
        'relative_content_updated_at',
    ];

    // 定義哪些欄位更新時需要更新 content_updated_at
    protected $contentFields = [
        'title',
        'content',
        'summary',
        'featured_image',
        'category_id',
        'status',
        'locale',
    ];


    public function getRelativeUpdatedAtAttribute()
    {
        return $this->content_updated_at ? $this->content_updated_at->diffForHumans() : null;
    }
    public function getRelativeContentUpdatedAtAttribute()
    {
        return $this->content_updated_at ? $this->content_updated_at->diffForHumans() : null;
    }

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
        // 不觸發更新 updated_at
        $this->increment('views', 1, ['updated_at' => $this->updated_at]);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }
    
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }
    
    /**
     * Apply search filters to the query
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        // Check if search is purely numeric (for ID search)
        if (is_numeric($search)) {
            return $query->where(function($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        } else {
            return $query->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }
    }
    
    /**
     * Apply sorting to the query
     */
    public function scopeSort($query, $sort = 'updated', $direction = 'desc')
    {
        $sortDirection = $direction === 'asc' ? 'asc' : 'desc';
        
        switch ($sort) {
            case 'views':
                return $query->orderBy('views', $sortDirection);
            case 'created':
                return $query->orderBy('created_at', $sortDirection);
            case 'updated':
            default:
                if ($sortDirection === 'asc') {
                    return $query->orderBy('content_updated_at', 'asc')
                                ->orderBy('created_at', 'asc');
                } else {
                    return $query->orderBy('content_updated_at', 'desc')
                                ->orderBy('created_at', 'desc');
                }
        }
    }
    
    /**
     * Apply common filters used across blog pages
     */
    public function scopeApplyBlogFilters($query, $filters)
    {
        return $query->when(isset($filters['locale']), function($q) use ($filters) {
                    return $q->locale($filters['locale']);
                })
                ->search($filters['search'] ?? '')
                ->sort($filters['sort'] ?? 'updated', $filters['direction'] ?? 'desc');
    }
    
    public function translations()
    {
        return $this->hasMany(Post::class, 'original_post_id');
    }
    
    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'original_post_id');
    }

    // 重寫父類別的 save 方法，使得內容相關欄位的更新會觸發 content_updated_at 的更新
    public function save(array $options = [])
    {
        // 檢查更新的欄位是否包含內容相關欄位
        if ($this->isDirty($this->contentFields)) {
            $this->content_updated_at = now();
        }

        return parent::save($options);
    }

    // 第一次創建時，將 content_updated_at 設為當前時間
    protected static function booted()
    {
        static::creating(function ($post) {
            $post->content_updated_at = now();
        });
    }
}
