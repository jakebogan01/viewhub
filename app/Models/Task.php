<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $with = ['tag'];

    /**
     * @return BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->BelongsTo(Tag::class);
    }
}
