<?php

namespace Modules\Review\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\Review\Events\ReviewCreated;
use Modules\Review\Models\Review;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'rating',
        'shop_id',
        'product_id',
    ];

    /**
     * @var array[]
     */
    protected $dataArray = [
        'order_id',
        'product_id',
        'variation_option_id',
        'user_id',
        'shop_id',
        'comment',
        'rating',
        'photos',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Review::class;
    }

    /**
     * @return LengthAwarePaginator|JsonResponse|Collection|mixed
     */
    public function storeReview($request)
    {
        // add logic to verified purchase and only one rating on each product
        try {
            $reviewInput = $request->only($this->dataArray);
            $review = $this->create($reviewInput);

            event(new ReviewCreated($review));

            return $review;
        } catch (Exception $e) {
            throw new HttpException(400, SOMETHING_WENT_WRONG);
        }
    }

    public function updateReview($request, $id)
    {
        try {
            $review = $this->findOrFail($id);
            $review->update($request->only($this->dataArray));

            return $review;
        } catch (Exception $e) {
            throw new HttpException(400, SOMETHING_WENT_WRONG);
        }
    }
}
