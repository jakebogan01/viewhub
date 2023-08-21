<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = ['tag', 'user', 'status'];

    /**
     * @param $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter($query, array $filters): void
    {
        // if search exists in filters, then search for title or tag name
        $query->when($filters['search'] ?? false, function($query, $search) {
            $query->where('title', 'LIKE', "%$search%")
                ->orWhereHas('tag', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
        });

        // if status exists in filters, then search for name
        $query->when($filters['status'] ?? false, function($query, $search) {
            $query->whereHas('status', function($query) use ($search) {
                    $query->where('name', $search);
                });
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
