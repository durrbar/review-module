<?php

declare(strict_types=1);

namespace Modules\Review\Events;

use Modules\Review\Models\Review;

class ReviewCreated
{
    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Review $review) {}
}
