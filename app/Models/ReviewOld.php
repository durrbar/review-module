<?php

namespace Modules\Review\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Comment\Models\Comment;
use Modules\User\Models\User;

// use Modules\Review\Database\Factories\ReviewFactory;

class ReviewOld extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'reviews';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'is_purchased',
        'helpful',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_purchased' => 'boolean', // Cast 'is_purchased' to boolean
        'rating' => 'float', // Cast 'rating' to float
        'helpful' => 'integer', // Cast 'helpful' to integer
        'created_at' => 'datetime', // Cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Cast 'updated_at' to a Carbon instance
        'deleted_at' => 'datetime', // Cast 'deleted_at' to a Carbon instance
    ];

    // protected static function newFactory(): ReviewFactory
    // {
    //     // return ReviewFactory::new();
    // }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all of the review's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Return the user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function incrementHelpful(): void
    {
        $this->increment('helpful');
    }

    public function decrementHelpful(): void
    {
        if ($this->helpful > 0) {
            $this->decrement('helpful');
        }
    }
}
