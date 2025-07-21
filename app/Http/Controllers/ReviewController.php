<?php

namespace Modules\Review\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Models\Order;
use Modules\Review\Http\Requests\ReviewCreateRequest;
use Modules\Review\Http\Requests\ReviewUpdateRequest;
use Modules\Review\Repositories\ReviewRepository;
use Modules\Settings\Repositories\SettingsRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReviewController extends CoreController
{
    public $repository;

    public $settingsRepository;

    public function __construct(ReviewRepository $repository, SettingsRepository $settingsRepository)
    {
        $this->repository = $repository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Review[]
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 15;
        if (isset($request['product_id']) && ! empty($request['product_id'])) {
            if ($request->user() !== null) {
                $request->user()->id; // need another way to force login
            }

            return $this->repository->where('product_id', $request['product_id'])->paginate($limit);
        }

        return $this->repository->paginate($limit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function store(ReviewCreateRequest $request)
    {
        $setting = $this->settingsRepository->first();
        $product_id = $request['product_id'];
        $order_id = $request['order_id'];
        try {
            $hasProductInOrder = Order::where('id', $order_id)->whereHas('products', function ($q) use ($product_id): void {
                $q->where('product_id', $product_id);
            })->exists();

            if ($hasProductInOrder === false) {
                throw new ModelNotFoundException(NOT_FOUND);
            }

            $user_id = $request->user()->id;
            $request['user_id'] = $user_id;

            // check if the review is following conventional system.
            if (! empty($setting->options['reviewSystem']['value']) && $setting->options['reviewSystem']['value'] === 'review_single_time') {

                // find out if any review exists or not
                if (isset($request['variation_option_id']) && ! empty($request['variation_option_id'])) {
                    $review = $this->repository->where('user_id', $user_id)->where('order_id', $order_id)->where('product_id', $product_id)->where('shop_id', $request['shop_id'])->where('variation_option_id', $request['variation_option_id'])->get();
                } else {
                    $review = $this->repository->where('user_id', $user_id)->where('order_id', $order_id)->where('product_id', $product_id)->where('shop_id', $request['shop_id'])->get();
                }

                if (count($review)) {
                    throw new HttpException(400, ALREADY_GIVEN_REVIEW_FOR_THIS_PRODUCT);
                }
            }

            return $this->repository->storeReview($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(ALREADY_GIVEN_REVIEW_FOR_THIS_PRODUCT);
        }
    }

    public function show($id)
    {
        try {
            return $this->repository->findOrFail($id);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    public function update(ReviewUpdateRequest $request, $id)
    {
        $request->merge(['id' => $id]);
        try {
            return $this->updateReview($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    public function updateReview(ReviewUpdateRequest $request)
    {
        $id = $request->id;

        return $this->repository->updateReview($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            return $this->repository->findOrFail($id)->delete();
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }
}
