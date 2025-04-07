<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use DataTables;
class ReviewReportController extends Controller
{
    public function index(Request $request, Report $report)
    {
        if ($request->ajax()) { // Get the authenticated user

            // Fetch users with eager-loaded studentInfo
            $Report = Report::latest()->get();

            return Datatables::of($Report)
                    ->addIndexColumn() // Add an index column
                    ->addColumn('status', function ($row) {
                        // Determine the selected status for the dropdown
                        $selected1 = $row->status == 1 ? "selected" : '';
                        $selected2 = $row->status == 2 ? "selected" : ''; 
                        $selected3 = $row->status == 3 ? "selected" : ''; 
                        // Create a dropdown for user status
                        $selectbox = "<div>
                                        <select class='user_status' data-id=" . $row->id . ">
                                            <option value='1' " . $selected1 . ">Pending</option>
                                            <option value='2' " . $selected2 . ">Approved</option>
                                            <option value='3' " . $selected3 . ">Rejected</option>
                                        </select>
                                      </div>";
                        return $selectbox;
                    })
                ->rawColumns(['status',])// Only mark the 'action' column as raw for HTML rendering
                    ->make(true);
        }
        return view('admin/report-review'); 
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if user already reported this review
        /*$existingReport = Report::where('review_id', $validated['review_id'])
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReport) {
            return back()->with('error', 'You have already reported this review.');
        }*/

        Report::create([
            'review_id' => $validated['review_id'],
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
            'status' => Report::STATUS_PENDING,
            'description' => $validated['description'] ?? null
        ]);

        return back()->with('success', 'Thank you for your report. We will review it shortly.');
    }

    // Add this method for admin to update status
    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', [
                Report::STATUS_PENDING,
                Report::STATUS_APPROVED,
                Report::STATUS_REJECTED
            ])
        ]);

        $report->update(['status' => $validated['status']]);

        return back()->with('success', 'Report status updated successfully');
    }

    /*public function AdminUpdateStatus(Request $request)
    {


        $report = Report::find($id);
        $report->status = $request['status'];
        $report->save();

        //return back()->with('success', 'Report status updated successfully');
    }*/

    public function reviews(Request $request)
    {
        $reviews = Review::with(['platform', 'user'])
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

        ///echo "<pre>"; print_r( $reviews); exit();
        return view('admin.reviews', ['reviews' => $reviews]);
    }

    public function approve($id, Request $request)
    {
        $review = Review::findOrFail($id);
        
        if ($request->input('is_reverting')) {
            $review->update(['status' => '1']);
        } else {
            $review->update(['status' => '1', 'rejection_reason' => null]);
        }
        
        return response()->json(['success' => true]);
    }

    public function reject($id, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);
        
        $review = Review::findOrFail($id);
        
        if ($request->input('is_reverting')) {
            $review->update(['status' => '0']);
        } else {
            $review->update([
                'status' => '0',
                'rejection_reason' => $validated['reason'] ?? null
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}
