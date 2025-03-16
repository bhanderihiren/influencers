<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\SocialMediaPlatform;
use App\Models\SocialmediaPlatformReview;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'social_media' => 'required',
            'quality_rating' => 'required|min:1|max:5',
            'value_rating' => 'required|min:1|max:5',
            'support_rating' => 'required|min:1|max:5',
            'general_review' => 'nullable|string|max:1000',
        ]);

        $Review = Review::create([
            'customer_id' => auth()->id(), // Save user ID (if authenticated)
            'performance' => $request->quality_rating,
            'lead' => $request->value_rating,
            'overall_review' => $request->support_rating,
            'review' => $request->general_review,
        ]);

        if($social_media = $request->social_media){
            foreach ($social_media as $key => $value) {
                $socialMediaplatform = SocialMediaPlatform::create([
                    'platform' => $key,
                    'review_id' => $Review->id, // Save user ID (if authenticated)
                    'name' => $value['name'],
                ]);

                SocialmediaPlatformReview::create([
                    'platfrom_id'   => $socialMediaplatform->id,
                    'platform_link' => $value['link']
                ]);

            }
        }

        return redirect()->route('dashboard')->with([
            'flash' => ['success' => 'Review submitted successfully!']
        ]);
    }
    public function showReviews()
    {
        $user_id = auth()->id();
        $reviews = Review::where('customer_id',$user_id)->latest()->get(); // Fetch latest reviews
        return Inertia::render('Customer/review', [
            'reviews' => $reviews
        ]);
    }
    public function edit($id){
       $reviews             = Review::where('id',$id)->first();
       $socialMediaplatform = SocialMediaPlatform::where('review_id', $id)
            ->with('platformLink') // Ensures the 'platformLink' relationship exists
            ->get();

            $socialMediaData = [];

            foreach ($socialMediaplatform as $platform) {
                $platformLink = $platform->platformLink->platform_link;
                $name         = $platform->name;
                $platform     = $platform->platform;

                $socialMediaData[$platform] = array('link' => $platformLink, "name" => $name);
            }

        return Inertia::render('Customer/EditInfluencer', [
            'reviews' => $reviews,
            'socialMediaplatform' => $socialMediaData,
            'reviewId' => $id
        ]);   
    }

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'social_media' => 'required|array',
            'quality_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'support_rating' => 'required|integer|min:1|max:5',
            'general_review' => 'nullable|string|max:1000',
        ]);

        // Find the review by ID
        $review = Review::findOrFail($request->id);

        // Update the review
        $review->update([
            'performance' => $request->quality_rating,
            'lead' => $request->value_rating,
            'overall_review' => $request->support_rating,
            'review' => $request->general_review,
        ]);

        // Get the existing social media platforms for this review
        $existingPlatforms = SocialMediaPlatform::where('review_id', $review->id)->pluck('platform')->toArray();

        // Get the platforms from the request
        $updatedPlatforms = array_keys($request->social_media);

        // Find platforms to delete (existing platforms not in the updated platforms)
        $platformsToDelete = array_diff($existingPlatforms, $updatedPlatforms);

        // Delete platforms that are no longer in the request
        if (!empty($platformsToDelete)) {
            SocialMediaPlatform::where('review_id', $review->id)
                ->whereIn('platform', $platformsToDelete)
                ->delete();
        }

        // Update or create social media platforms
        if ($request->social_media) {
            foreach ($request->social_media as $key => $value) {
                // Find or create the social media platform
                $socialMediaPlatform = SocialMediaPlatform::updateOrCreate(
                    [
                        'review_id' => $review->id,
                        'platform' => $key, // Unique identifier: review_id + platform
                    ],
                    [
                        'name' => $value['name'], // Data to update or create
                    ]
                );

                // Update or create the social media platform link
                SocialmediaPlatformReview::updateOrCreate(
                    [
                        'platfrom_id' => $socialMediaPlatform->id, // Unique identifier: platfrom_id
                    ],
                    [
                        'platform_link' => $value['link'], // Data to update or create
                        'review_id' => $review->id, // Add review_id to maintain the relationship
                    ]
                );
            }
        }

        // Redirect with success message
        return back()->with([
            'flash' => ['success' => 'Review updated successfully!']
        ]);
    }
}
