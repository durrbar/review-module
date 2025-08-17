<?php

namespace Modules\Review\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Models\AbusiveReport;
use Modules\Ecommerce\Models\Feedback;
use Modules\Ecommerce\Models\Product;
use Modules\User\Models\User;

class Review extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'reviews';

    public $guarded = [];

    protected $casts = [
        'photos' => 'json',
    ];

    protected $appends = [
        'positive_feedbacks_count',
        'negative_feedbacks_count',
        'my_feedback',
        'abusive_reports_count',
    ];

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id');
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the reviews feedbacks.
     */
    public function feedbacks()
    {
        return $this->morphMany(Feedback::class, 'model');
    }

    public function abusive_reports()
    {
        return $this->morphMany(AbusiveReport::class, 'model');
    }

    /**
     * Positive feedback count of review .
     *
     * @return int
     */
    public function getPositiveFeedbacksCountAttribute()
    {
        return $this->feedbacks()->wherePositive(1)->count();
    }

    /**
     * Negative feedback count of review .
     *
     * @return int
     */
    public function getNegativeFeedbacksCountAttribute()
    {
        return $this->feedbacks()->whereNegative(1)->count();
    }

    /**
     * Get authenticated user feedback
     *
     * @return object | null
     */
    public function getMyFeedbackAttribute()
    {
        if (auth()->user()) {
            return $this->feedbacks()->where('user_id', auth()->user()->id)->first();
        }

        return null;
    }

    /**
     * Count no of abusive reports in the review.
     *
     * @return int
     */
    public function getAbusiveReportsCountAttribute()
    {
        return $this->abusive_reports()->count();
    }
}
