<?php

namespace Modules\Review\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Review\Models\Review;
use Modules\User\Models\User;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific review.
     */
    public function view(User $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a review.
     */
    public function create(User $user): bool
    {
        return true; // Authenticated users can create reviews
    }

    /**
     * Determine whether the user can update a specific review.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete a specific review.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can restore a specific review.
     */
    public function restore(User $user, Review $review): bool
    {
        return false; // Prevents users from restoring unless explicitly allowed
    }

    /**
     * Determine whether the user can permanently delete a specific review.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return false; // Prevents users from force deleting unless explicitly allowed
    }
}
