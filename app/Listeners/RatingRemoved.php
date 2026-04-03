<?php

declare(strict_types=1);

namespace Modules\Review\Listeners;

use Modules\Refund\Events\RefundApproved;
use Modules\Review\Models\Review;

class RatingRemoved
{
    /**
     * Handle the event.
     */
    public function handle(RefundApproved $event): void
    {
        Review::where('user_id', $event->refund->customer_id)->where('order_id', $event->refund->order_id)->delete();
    }
}
