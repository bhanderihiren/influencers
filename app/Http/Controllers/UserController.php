<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use App\Models\User;
use App\Models\Review;
use DataTables;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {   
        if ($request->ajax()) { // Get the authenticated user

            // Fetch users with eager-loaded studentInfo
            $users = User::where('role','!=','Admin')
            ->latest()
            ->get();


            // Use DataTables to structure the response
            return Datatables::of($users)
                    ->addIndexColumn() // Add an index column
                     ->addColumn('status', function ($row) {
                        // Determine the selected status for the dropdown
                        $selected1 = $row->user_status == 1 ? "selected" : '';
                        $selected2 = $row->user_status == 0 ? "selected" : ''; 

                        // Create a dropdown for user status
                        $selectbox = "<div>
                                        <select class='user_status' data-id=" . $row->id . ">
                                            <option value='1' " . $selected1 . ">Verified</option>
                                            <option value='0' " . $selected2 . ">Unverified</option>
                                        </select>
                                      </div>";
                        return $selectbox;
                    })
                    ->addColumn('state', function ($row) {

                        $stateName = $row->studentInfo->stateData->name ?? 'N/A'; // Adjust field name to match your schema
                        return $stateName;
                    })
                    ->addColumn('district', function ($row) { // Fixed the column name (removed extra space)
                        $districtName = $row->studentInfo->districtData->name ?? 'N/A'; // Adjust field name to match your schema
                        return $districtName;
                    }) ->addColumn('college', function ($row) { // Fixed the column name (removed extra space)
                        $college_name = $row->studentInfo->collegeData->college_name ?? 'N/A'; // Adjust field name to match your schema
                        return $college_name;
                    })
                    ->addColumn('view_details', function ($row) {
                    // Eye icon for viewing user details
                    $viewButton = "<a href='".route('view-user', $row->id)."' class='view-details btn btn-info btn-sm' data-id='" . $row->id . "'>
                                       <i class='fas fa-eye'></i> View
                                   </a>";
                
                    // Edit button
                    $editButton = "<button class='edit-details btn btn-warning btn-sm' data-id='" . $row->id . "'>
                                       <i class='fas fa-edit'></i> Edit
                                   </button>";
                
                    // Delete button
                    $deleteButton = "<button class='delete-details btn btn-danger btn-sm' data-id='" . $row->id . "'>
                                         <i class='fas fa-trash'></i> Delete
                                     </button>";
                
                    // Return all buttons concatenated
                    return $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['status', 'view_details'])// Only mark the 'action' column as raw for HTML rendering
                    ->make(true);
        }
        return view('admin/user'); 
    }

    public function deleteUser($id) {
        // Find the user by ID
        $user = User::find($id);
        
        // Check if the user exists
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }
    
        // Proceed to delete the user
        $user->delete();
    
        return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
    }

    public function view($id) {
        $user = User::where('id',$id)->first();
        $user_id = $user->id;
        $user_role = $user->role; // Ensure your User model has a 'role' attribute

        // Base query
        $query = Review::with('platform')->latest();

        if ($user_role === "customer") {
            $query->where('customer_id', $user_id);
        } else {
            $query->where('user_id', $user_id);
        }

        // Retrieve and format the reviews
        $reviews = $query->get()->map(function ($review) {
            $review->social_media = $review->platform->pluck('platform')->toArray();
            unset($review->platform);
            return $review;
        });

        return view('admin.user-view', ['reviews' => $reviews]);
    }

}
