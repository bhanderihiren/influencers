<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use App\Models\SocialMediaPlatform;
use App\Models\SocialmediaPlatformReview;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

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

        foreach ($request->social_media as $platform => $data) {
            if (!$this->validateSocialMediaUsername($platform, $data['name'], $data['link'])) {
                return back()->withErrors([
                    'social_media' => "The username '{$data['name']}' doesn't appear to be a valid $platform profile"
                ])->withInput();
            }
        }
        
        $Review = Review::create([
            'customer_id' => auth()->id(), // Save user ID (if authenticated)
            'performance' => $request->quality_rating,
            'lead' => $request->value_rating,
            'overall_review' => $request->support_rating,
            'review' => $request->general_review,
        ]);

        if($social_media = $request->social_media){
            foreach ($social_media as $key => $value) {

                $socialMediaplatform = SocialMediaPlatform::where('platform', $key)
                                        ->where('name', $value['name'])
                                        ->first();

                if ($socialMediaplatform) {
                    $user_id = $socialMediaplatform->user_id;
                    $socialMediaplatform = SocialMediaPlatform::create([
                        'platform' => $key,
                        'review_id' => $Review->id,
                        'name' => $value['name'],
                        'user_id' => $user_id
                    ]);
                } else {
                    $socialMediaplatform = SocialMediaPlatform::create([
                        'platform' => $key,
                        'review_id' => $Review->id,
                        'name' => $value['name'],
                    ]);
                    $user_id = 0;
                }

                SocialmediaPlatformReview::create([
                    'platfrom_id'   => $socialMediaplatform->id,
                    'platform_link' => $value['link']
                ]);

            }
        }

        if(!empty($user_id) && $user_id != 0 ){
           $Review->update(['user_id' => $user_id]);
        }

        return redirect()->route('dashboard')->with([
            'flash' => ['success' => 'Review submitted successfully!']
        ]);
    }
    public function showReviews()
    {
        $user_id = auth()->id();
        $reviews = Review::where('customer_id', $user_id)
                    ->with('platform') // Load related platforms
                    ->latest()
                    ->get()
                    ->map(function ($review) {
                    // Extract platform name and link
                    $review->social_media = $review->platform->map(function ($platform) {
                        return [
                            'platform' => $platform->platform, // e.g., 'tiktok', 'facebook'
                            'link' => generatePlatformLink($platform->platform, $platform->name) // e.g., 'https://tiktok.com/@user'
                        ];
                    })->toArray();

                    // Add verification status if needed
                    $review->is_verified = optional($review->user)->status;

                    unset($review->platform, $review->user); // Remove unnecessary relations
                    return $review;
                    });

        return Inertia::render('Customer/review', [
            'reviews' => $reviews,

        ]);
    }

    public function allInfluencer()
    {
        $platforms = SocialMediaPlatform::select('platform')
            ->distinct()
            ->orderBy('platform')
            ->pluck('platform');

        $influencers = User::with(['socialmedia'])
            ->where('role', 'influencer')
            ->get()
            ->map(function ($user) {
                $uniquePlatforms = $user->socialmedia
                    ->unique('platform')
                    ->map(function ($account) {
                        return [
                            'platform' => $account->platform,
                            'icon' => $this->getPlatformIcon($account->platform),
                            'username' => $account->name,
                            'link' => generatePlatformLink($account->platform, $account->name),
                            'followers' => $account->follower_count ? number_format($account->follower_count) : null,
                            'verified' => $account->status
                        ];
                    })
                    ->sortByDesc('followers')
                    ->values();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ?? '/default-avatar.png',
                    'is_verified' => $user->status,
                    'bio' => $user->bio,
                    'platforms' => $uniquePlatforms,
                    'primary_platform' => $uniquePlatforms->first()['platform'] ?? null,
                    'total_followers' => $user->socialmedia->sum('follower_count')
                ];
            })
            ->sortByDesc('total_followers');

        return Inertia::render('All-Influencer', [
            'influencers' => $influencers,
            'platforms' => $platforms,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }

    private function getPlatformIcon($platform)
    {
        $icons = [
            'instagram' => 'fa-instagram',
            'tiktok' => 'fa-tiktok',
            'youtube' => 'fa-youtube',
            'facebook' => 'fa-facebook-f',
            'twitter' => 'fa-twitter',
            'linkedin' => 'fa-linkedin-in'
        ];

        return $icons[strtolower($platform)] ?? 'fa-share-nodes';
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
