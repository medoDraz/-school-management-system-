<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ItemCoding;
use App\User;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Validator, File;

class ItemCodingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_Item'])->only('store');
        $this->middleware(['permission_api:Access_Item'])->only(['index']);
        $this->middleware(['permission_api:Edit_Item'])->only(['update', 'changestatus']);
    }

    public function index(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $item_codings = ItemCoding::with(['user', 'store_coding', 'category', 'account'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $item_codings], 200);
    }

    public function getallitems(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $item_codings = ItemCoding::with(['user'])->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $item_codings], 200);
    }

    public function store(Request $request)
    {
//        dd($request);

        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'image_Url'=>'image',
                // 'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        if ($request->hasFile('image_Url')) {
//            $md5Name = md5_file($request->file('image_Url')->getRealPath());
//            $guessExtension = $request->file('image_Url')->guessExtension();
//            $destinationPath = '/uploads/test';
//            $request->file('image_Url')->storeAs($destinationPath, $md5Name . '.' . $guessExtension);
            Image::make($request->image_Url)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/item_images/' . $request->image_Url->hashName()));

            //dd('$guessExtension');
        }

        $user = User::where('api_token', $request->token)->first();
        $allowed = array('png', 'jpg', 'jpeg');
        $destinationPath = '/uploads';
        $item_coding = new ItemCoding();
        $item_coding->name = $request->name;
        $item_coding->description = $request->description;
        $item_coding->account = $request->account_code;
        $item_coding->credit_by_limit_value = $request->credit_by_limit_value;
        $item_coding->credit_by_limit_unit = $request->credit_by_limit_unit;
        $item_coding->VATed = $request->VATed;
        $item_coding->category = $request->category;

        $item_coding->photo = $request->image_Url->hashName();
        $item_coding->store = $request->store;
        $item_coding->unit = $request->unit;
        $item_coding->created_by = $user->id;
        $item_coding->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $item_coding], 200);

    }

    public function edit(Request $request)
    {

        $item_coding = ItemCoding::with(['user', 'store_coding', 'category', 'account'])->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $item_coding], 200);
    }

    public function update(Request $request)
    {
        //dd($request);
        $item_coding = ItemCoding::where('id', $request->id)->first();
        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                // 'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();
        $allowed = array('png', 'jpg', 'jpeg');
        $destinationPath = '/uploads';
        //$item_coding = new ItemCoding();
        $item_coding->name = $request->name;
        $item_coding->description = $request->description;
        $item_coding->account = $request->account_code;
        $item_coding->credit_by_limit_value = $request->credit_by_limit_value;
        $item_coding->credit_by_limit_unit = $request->credit_by_limit_unit;
        $item_coding->VATed = $request->VATed;
        $item_coding->category = $request->category;
        // if ($request->imageUrl) {
        //     Image::make($request->imageUrl)->resize(300, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })->save(public_path('uploads/item_images/'.$request->imageUrl->hashName()));

        //     $item_coding->photo  = $request->imageUrl->hashName();

        // }
        // $item_coding->photo       			     = $request->imageUrl;
        $item_coding->store = $request->store;
        $item_coding->unit = $request->unit;
        $item_coding->updated_by = $user->id;
        $item_coding->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $item_coding], 200);

    }
}
