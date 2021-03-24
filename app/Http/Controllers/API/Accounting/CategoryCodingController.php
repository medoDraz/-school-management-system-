<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CategoryCoding;
use App\User;
use Illuminate\Validation\Rule;
use Validator;

class CategoryCodingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_Category'])->only('store');
        $this->middleware(['permission_api:Access_Category'])->only(['index']);
        $this->middleware(['permission_api:Edit_Category'])->only(['update','changestatus']);
    }

    public function index(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $category_codings = CategoryCoding::with(['user','store_coding'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $category_codings], 200);
    }

    public function getallcategories(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $category_codings = CategoryCoding::with(['user'])->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $category_codings], 200);
    }

    public function store(Request $request)
    {
        //dd($request);

        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();

        $category_coding = new CategoryCoding();
        $category_coding->name              = $request->name;
        $category_coding->description       = $request->description;
        $category_coding->stored_at         = $request->stored_at;
        $category_coding->created_by        = $user->id;
        $category_coding->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $category_coding], 200);

    }

    public function edit(Request $request){

		$category_coding = CategoryCoding::where('id', $request->id)->first();
		return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $category_coding], 200);
	}

	public function update(Request $request)
    {
        //dd($request);
    	$category_coding =CategoryCoding::where('id', $request->id)->first();
        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();

        $category_coding->name              = $request->name;
        $category_coding->description       = $request->description;
        $category_coding->stored_at         = $request->stored_at;
        $category_coding->updated_by        = $user->id;
        $category_coding->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $category_coding], 200);

    }

    public function changestatus(Request $request)
    {
		//dd($request);
        $category_coding = CategoryCoding::where('id', $request->id)->first();
		$user = User::where('api_token', $request->token)->first();
        $category_coding->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
    }
    public function destroy(Request $request)
    {

        $role = CategoryCoding::findOrFail($request->id);
        $role->delete();

        return response()->json(['error'=>false , 'message'=>__('site.deleted_successfully'),'data' => []], 200);
    }
}
