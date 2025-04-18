<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Influencer;
use Illuminate\Support\Facades\DB;
use App\Models\SocialMediaPlatform;
use App\Models\InfluencerCategory;
use App\Models\Review;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InfluencerController extends Controller
{
   public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch social media data and structure the array
        $socialMedia = SocialMediaPlatform::where('user_id', $user->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->platform => [
                        'name' => $item->name,
                        'link' => in_array($item->platform, ['tiktok']) ? "" : null, // Add link only for tiktok
                    ]
                ];
            });

        // Fetch influencer categories
        $category = InfluencerCategory::where('user_id', $user->id)
                    ->get()
                    ->mapWithKeys(function ($item) {
                    return [
                        $item->id =>[
                            'id' => $item->category_id,
                        ]
                    ];
                });

        return Inertia::render('Influencer/EdiDetail', [
            'user' => $user,
            'social_media' => $socialMedia, // Pass existing social media data
            'category' => $category
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'categories' => 'array|max:3',
        ]);

        $social_media = $request->social_media;
        $errors = [];
        $user_id = auth()->id();

        $review_id = array(); 
        foreach ($social_media as $key => $value) {
            // Check if a record exists with the same platform and name
            $existingRecord = SocialMediaPlatform::where('platform', $key)
                ->where('name', $value['name'])
                ->first();

            if ($existingRecord) {
                if (!$existingRecord->user_id) {
                    // If no user is assigned, assign the current user and update details
                    $existingRecord->update([
                        'user_id' => $user_id,
                    ]);

                    $review_id[] = $existingRecord->review_id;

                } elseif ($existingRecord->user_id === $user_id) {
                    // If the record already belongs to the current user, update details
                    $existingRecord->update([
                        'name' => $value['name'],
                    ]);
                }
            } else {
                // If no existing record, create a new entry
                SocialMediaPlatform::create([
                    'platform' => $key,
                    'user_id'  => $user_id,
                    'name'     => $value['name'],
                ]);
            }
        }

        if(!empty($review_id)){
            foreach($review_id as $key => $id){
                $review = Review::find($id);
                $review->user_id = $user_id;
                $review->save();
            }
        }


        $categories = $request->categories;
        $existingCategories = InfluencerCategory::where('user_id', $user_id)->pluck('category_id')->toArray();
        $newCategories = array_column($categories, 'id');

        $categoriesToDelete = array_diff($existingCategories, $newCategories);
        

        // Identify categories to insert
        $categoriesToInsert = array_diff($newCategories, $existingCategories);
        $insertData = array_map(function ($categoryId) use ($user_id) {
            return ['user_id' => $user_id, 'category_id' => $categoryId, 'created_at' => now(), 'updated_at' => now()];
        }, $categoriesToInsert);

        if (!empty($insertData)) {
            InfluencerCategory::insert($insertData); // Bulk insert new categories
        }
        InfluencerCategory::where('user_id', $user_id)
            ->whereIn('category_id', $categoriesToDelete)
            ->delete();
        // If there are errors, return back with errors
        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        return redirect()->route('influencer.edi-detail')->with('success', 'Influencer added successfully!');
    }

    public function myReviews(Request $request)
    {
        $user_id = auth()->id();
        
        $reviews = Review::where('user_id', $user_id)
                ->with(['platform', 'report']) // Changed from 'reports' to 'report'
                ->latest()
                ->get()
                ->map(function ($review) {
                    // Extract platform name and link
                    $review->social_media = $review->platform ? $review->platform->map(function ($platform) {
                        return [
                            'platform' => $platform->platform,
                            'link' => generatePlatformLink($platform->platform, $platform->name)
                        ];
                    })->toArray() : [];

                    // Add report details if report exists
                    if ($review->report) {
                        $review->report_details = [
                            'status' => $this->getStatusText($review->report->status),
                            'reason' => $review->report->reason,
                            'created_at' => $review->report->created_at->format('M d, Y'),
                            'updated_at' => $review->report->updated_at ? $review->report->updated_at->format('M d, Y') : null
                        ];
                    }

                    unset($review->platform, $review->report); // Remove unnecessary relations
                    return $review;
                });

        return Inertia::render('Influencer/MyReviews', [
            'reviews' => $reviews,
        ]);
    }

    protected function getStatusText($statusCode)
    {
        return match($statusCode) {
            Report::STATUS_PENDING => 'pending',
            Report::STATUS_APPROVED => 'approved',
            Report::STATUS_REJECTED => 'rejected',
            default => 'unknown'
        };
    }


}
