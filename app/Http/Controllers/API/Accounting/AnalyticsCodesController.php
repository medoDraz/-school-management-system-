<?php

namespace App\Http\Controllers\API\Accounting;

use App\Analytics_Code;
use App\User;
use App\DimensionsCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;

class AnalyticsCodesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_Analytics'])->only('store');
        $this->middleware(['permission_api:Access_Analytics'])->only(['index']);
        $this->middleware(['permission_api:Edit_Analytics'])->only(['update','changestatus']);
    }
    public function index(Request $request)
    {
        $analytics_codes = Analytics_Code::with('user')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('analysis_name', 'like', '%' . $request->search . '%')
                    ->orWhere('code_id', 'like', '%' . $request->search . '%')
					->orWhere('records_type', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $analytics_codes], 200);
    }

    public function getallanalystic(Request $request)
    {
        $analytics_codes = Analytics_Code::with('user')->where('status','Open')->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $analytics_codes], 200);
    }

    public function store(Request $request)
    {

        $t = [
            'code_id' => __('site.code_id'),
            'analysis_name' => __('site.analysis_name'),
            'records_type' => __('site.records_type'),
            'status' => __('site.status'),
        ];
        $validator = Validator::make($request->all(),
            [
                'code_id' => 'required',
                'analysis_name' => 'required',
                'records_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();

        $analytics_codes =new Analytics_Code();
        $analytics_codes->code_id       = $request->code_id;
        $analytics_codes->analysis_name = $request->analysis_name;
        $analytics_codes->description   = $request->description;
        $analytics_codes->records_type  = $request->records_type;
        $analytics_codes->type          = $request->type;
        $analytics_codes->status        = 'Open';
        $analytics_codes->created_by    = $user->id;
        $analytics_codes->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => []], 200);

    }
	
	public function edit(Request $request){
		
		$analytics_code = Analytics_Code::where('id', $request->id)->first();
		return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $analytics_code], 200);

	}

    public function update(Request $request)
    {
		
        $account = Analytics_Code::where('id', $request->id)->first();
        $t = [
            'code_id' => __('site.code_id'),
            'analysis_name' => __('site.analysis_name'),
            'records_type' => __('site.records_type'),
            'status' => __('site.status'),
        ];
        $validator = Validator::make($request->all(),
            [
                'code_id' => 'required',
                'analysis_name' => 'required',
                'records_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();
		//dd($request);
        $account->update([
            'code_id'       => $request->code_id,
            'analysis_name' => $request->analysis_name,
            'description'   => $request->description,
            'records_type'  => $request->records_type,
            'type'          => $request->type,
            'status'        => 'Open',
            'updated_by'    => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);


    }

    public function changestatus(Request $request)
    {
		//dd($request);
        $account = Analytics_Code::where('id', $request->id)->first();
		$user = User::where('api_token', $request->token)->first();
        $account->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
    }

    public function changestatusdimension(Request $request)
    {
        //dd($request);
        $account = DimensionsCode::where('id', $request->id)->first();
        $user = User::where('api_token', $request->token)->first();
        $account->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);

    }

    public function getdimensionscode(Request $request)
    {
        //dd($request);
        if($request->analysis_id){
            $analytics_code = Analytics_Code::where('id', $request->analysis_id)->first();
            if($analytics_code->records_type =='Manual'){
                if($request->limit)
                    $dimensionscode = DimensionsCode::with('user')->where('analtics_id', $request->analysis_id)->latest()->paginate($request->limit);
                else
                    $dimensionscode = DimensionsCode::with('user')->where('analtics_id', $request->analysis_id)->latest()->get();

                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $dimensionscode], 200);
            }else{
            //     $dimensionscode = DimensionsCode::with('user')->where('analtics_id', $request->analysis_id)->latest()->get();
            // return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $dimensionscode], 200);
            }
        } else{
            $analytics_code = Analytics_Code::where('analysis_name', $request->analysis_name)->first();
            $dimensionscode = DimensionsCode::with('user')->where('analtics_id', $analytics_code->id)->latest()->get();
            return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $dimensionscode], 200);
        }
        
        
    }

    public function adddimensionscode(Request $request)
    {
        //dd($request);
        if($request->id){
            $t = [];
            $validator = Validator::make($request->all(),
                [
                    'code_id' => 'required|unique:dimensions_codes,code_id',
                    'name' => 'required',
                    //'status' => 'required',
                ],
             [], $t);

            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
            }

            $user = User::where('api_token', $request->token)->first();
            $dimensionscode = DimensionsCode::where('id', $request->id)->first();
            $dimensionscode->analtics_id = $request->analysis_id;
            $dimensionscode->code_id = $request->code_id;
            $dimensionscode->name = $request->name;
            $dimensionscode->status = 'Active';
            $dimensionscode->updated_by = $user->id;
            $dimensionscode->save();
            return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $dimensionscode], 200);
        }else{


            $t = [];
            $validator = Validator::make($request->all(),
                [
                    'code_id' => 'required|unique:dimensions_codes,code_id',
                    'name' => 'required',
                    //'status' => 'required',
                ],
             [], $t);

            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
            }

            $user = User::where('api_token', $request->token)->first();
            $dimensionscode = new DimensionsCode();
            $dimensionscode->analtics_id = $request->analysis_id;
            $dimensionscode->code_id = $request->code_id;
            $dimensionscode->name = $request->name;
            $dimensionscode->status = 'Active';
            $dimensionscode->created_by = $user->id;
            $dimensionscode->save();
            return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $dimensionscode], 200);
        }   
    }
}
