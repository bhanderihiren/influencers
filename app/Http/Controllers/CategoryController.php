<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use App\Models\Category;
use DataTables;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
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
            $category = Category::latest()->get();

            return Datatables::of($category)
                    ->addIndexColumn() // Add an index column
                     ->addColumn('status', function ($row) {
                        // Determine the selected status for the dropdown
                        $selected1 = $row->user_status == 1 ? "selected" : '';
                        $selected2 = $row->user_status == 0 ? "selected" : ''; 

                        // Create a dropdown for user status
                        $selectbox = "<div>
                                        <select class='user_status' data-id=" . $row->id . ">
                                            <option value='1' " . $selected1 . ">Active</option>
                                            <option value='0' " . $selected2 . ">Deactive</option>
                                        </select>
                                      </div>";
                        return $selectbox;
                    })
                    ->addColumn('view_details', function ($row) {
                    // Eye icon for viewing user details
                
                    // Edit button
                    $editButton = "<button class='edit-details btn btn-warning btn-sm' data-id='" . $row->id . "'>
                                       <i class='fas fa-edit'></i> Edit
                                   </button>";
                
                    // Delete button
                    $deleteButton = "<button class='delete-details btn btn-danger btn-sm' data-id='" . $row->id . "'>
                                         <i class='fas fa-trash'></i> Delete
                                     </button>";
                
                    // Return all buttons concatenated
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['status', 'view_details'])// Only mark the 'action' column as raw for HTML rendering
                    ->make(true);
        }
        return view('admin/category'); 
    }

    public function newCategoyCreate(Request $request){

        if(request('id') && request('id') != ""){
            $id = request('id');
            $skill = Category::find($id);
        }else{
            $skill = new Category;
        }
        $skill->name   = request('name');
        $skill->save();
    }

    public function Delete($id){
        $id = request('id');
        $user = Category::where('id', $id)->firstorfail()->delete();
    }

    public function edit($id){
        $id = request('id');
        $category = Category::find($id);
        $popup = view('admin/popup/user',['category'=>$category]);
        echo $popup; exit;
        return response()->json(array("data" => $popup, 'extra' => '00' ));
    }

    public function statusUpdate(Request $request){
        $id = request('id');
        $status = request('status');
        $Skill = Skill::find($id);
        $Skill->status = $status;
        $Skill->save();
        return response()->json(array("status" =>"true"));
    }

    public function listApi() {
        return response()->json(Category::select('id', 'name')->get()->toArray());
    }
}
