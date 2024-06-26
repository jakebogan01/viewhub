<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $with = ['tag', 'user', 'status', 'likes', 'images', 'comments'];

    /**
     * @param $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter($query, array $filters): void
    {
        // if search exists in filters, then search by title or tag name
        $query->when($filters['search'] ?? false, function($query, $search) {
            $query->where('title', 'LIKE', "%$search%")
                ->orWhereHas('tag', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                })->orWhereHas('user', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
        });

        // if status exists in filters, then search by name
        $query->when($filters['status'] ?? false, function($query, $search) {
            $query->whereHas('status', function($query) use ($search) {
                $query->where('name', $search);
            });
        });

        // if tag exists in filters, then search by name
        $query->when($filters['tag'] ?? false, function($query, $search) {
            $query->whereHas('tag', function($query) use ($search) {
                $query->where('name', $search);
            });
        });

        // if liked exists in filters, then sort by count likes
        if (request('liked')) {
            $query->withCount('likes')->orderByDesc('likes_count');
        }

        // if commented exists in filters, then sort by count comments
        if (request('commented')) {
            $query->withCount('comments')->orderByDesc('comments_count');
        }

        // if sort exists in filters, then sort by newest or oldest
        if (request('sortby') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->BelongsTo(Tag::class);
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->BelongsTo(Status::class);
    }

    /**
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_likes')->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
