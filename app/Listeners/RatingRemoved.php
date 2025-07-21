<?php

namespace Modules\Review\Listeners;

use Modules\Refund\Events\RefundApproved;
use Modules\Review\Models\Review;

class RatingRemoved
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(RefundApproved $event)
    {
        Review::where('user_id', $event->refund->customer_id)->where('order_id', $event->refund->order_id)->delete();
    }
}
