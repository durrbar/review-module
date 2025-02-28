<?php

namespace Modules\Review\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Review\Http\Requests\ReviewRequest;
use Modules\Review\Http\Resources\ReviewResource;
use Modules\Review\Models\Review;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index($modelType, $modelId)
    {
        $model = $this->getModelInstance($modelType, $modelId);

        // Build the base query for comments
        $reviews = $model->reviews()->with(['user', 'comments.user'])->paginate(2);

        return response()->json(['reviews' => $reviews], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, $modelType, $modelId)
    {
        $validatedData = $request->validated();

        $model = $this->getModelInstance($modelType, $modelId);

        $review = $model->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment']
        ]);

        // Handle attachments if provided
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $image) {
                $path = $image->store('uploads/reviews/attachments', 'public');

                $review->attachments()->create([
                    'path' => $path,
                ]);
            }
        }

        // Return the newly created comment as a resource
        return response()->json(['review' => new ReviewResource($review->load('user', 'attachments'))], Response::HTTP_CREATED);
    }

    /**
     * Show the specified resource.
     */
    public function show($modelType, $modelId, Review $review): JsonResponse
    {
        // Authorize the action using policies
        $this->authorize('view', $review);

        // Return a single comment
        return response()->json(['review' => new ReviewResource($review)], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, $modelType, $modelId, Review $review): JsonResponse
    {
        // Authorize the action using policies
        $this->authorize('update', $review);

        $validatedData = $request->validated();

        $review->update([
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment']
        ]);
    
        // Initialize an array to track which URLs should be kept
        $keepUrls = [];
    
        // Check if new images or URLs are provided
        if ($request->has('attachments')) {
            foreach ($request->input('attachments') as $attachment) {
                // If the attachment is a URL, add it to the keepUrls array
                if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                    $keepUrls[] = $attachment;
                } else {
                    // Otherwise, it's assumed to be a new file upload
                    $path = $attachment->store('uploads/reviews/attachments', 'public');
                    $review->attachments()->create([
                        'path' => $path,
                    ]);
                }
            }
        }
    
        // Now, remove attachments that are not in the keepUrls
        foreach ($review->attachments as $attachment) {
            if (!in_array($attachment->url, $keepUrls)) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }
        }

        // Return updated comment as a resource
        return response()->json(['review' => new ReviewResource($review->load('user', 'comments.user', 'attachments'))], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($modelType, $modelId, Review $review): JsonResponse
    {
        // Authorize the action using policies
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(['message' => 'Comment deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    public function incrementHelpful($modelType, $modelId, Review $review)
    {
        $review->incrementHelpful();

        return response()->json([
            'message' => 'Helpful count incremented',
            'helpful' => $review->helpful
        ], Response::HTTP_OK);
    }

    public function decrementHelpful($modelType, $modelId, Review $review)
    {
        $review->decrementHelpful();

        return response()->json([
            'message' => 'Helpful count decremented',
            'helpful' => $review->helpful
        ], Response::HTTP_OK);
    }

    /**
     * Get model instance based on type.
     */
    private function getModelInstance($modelType, $modelId)
    {
        // This resolves the model class based on the polymorphic type
        $modelClass = Relation::getMorphedModel($modelType);

        if (!$modelClass) {
            abort(Response::HTTP_NOT_FOUND, "Invalid model type");
        }

        return $modelClass::findOrFail($modelId);
    }
}
