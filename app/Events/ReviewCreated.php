<?php

declare(strict_types=1);

namespace Modules\Review\Events;

use Modules\Review\Models\Review;

class ReviewCreated
{
    public $review;

    /**
     * Create a new event instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }
}
