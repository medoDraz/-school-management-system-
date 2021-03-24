<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StoreCoding;
use App\Jvs;
use App\User;
use Illuminate\Validation\Rule;
use Validator;

class StoreCodingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_Store'])->only('store');
        $this->middleware(['permission_api:Access_Store'])->only(['index']);
        $this->middleware(['permission_api:Edit_Store'])->only(['update','changestatus']);
    }

    public function index(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $store_codings = StoreCoding::with(['user','account'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $store_codings], 200);
    }
    public function getallstores(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $store_codings = StoreCoding::where('status','Open')->with(['user','account'])->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $store_codings], 200);
    }

    public function store(Request $request)
    {
        //dd($request);

        $t = [];
        $validator = Validator::make($request->all(),
            [
                'account_code' => 'required',
                'name' => 'required',
                'credit_by_limit_value' => 'required',
                'credit_by_limit_unit' => 'required',
                'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();

        $store_coding =new StoreCoding();
        $store_coding->account     = $request->account_code;
        $store_coding->name              = $request->name;
        $store_coding->credit_by_limit_value = $request->credit_by_limit_value;
        $store_coding->credit_by_limit_unit = $request->credit_by_limit_unit;
        $store_coding->description       = $request->description;
        $store_coding->status            = $request->status;
        $store_coding->created_by        = $user->id;
        $store_coding->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $store_coding], 200);

    }

    public function edit(Request $request){

		$store_coding = StoreCoding::with(['account'])->where('id', $request->id)->first();
		return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $store_coding], 200);
	}

	public function update(Request $request)
    {
        //dd($request);
    	$store_coding =StoreCoding::where('id', $request->id)->first();
        $t = [];
        $validator = Validator::make($request->all(),
            [
                'account_code' => 'required',
                'name' => 'required',
                'credit_by_limit_value' => 'required',
                'credit_by_limit_unit' => 'required',
                'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();

        $store_coding->account     = $request->account_code;
        $store_coding->name              = $request->name;
        $store_coding->credit_by_limit_value = $request->credit_by_limit_value;
        $store_coding->credit_by_limit_unit = $request->credit_by_limit_unit;
        $store_coding->description       = $request->description;
        $store_coding->status            = $request->status;
        $store_coding->updated_by        = $user->id;
        $store_coding->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $store_coding], 200);

    }

    public function changestatus(Request $request)
    {
		//dd($request);
        $store_coding = StoreCoding::where('id', $request->id)->first();
		$user = User::where('api_token', $request->token)->first();
        $store_coding->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
    }
}
