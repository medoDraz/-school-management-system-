<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use App\Budget;
use App\User;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BudgetController extends Controller
{

    public function index(Request $request)
    {
        $budgets = Budget::with('account')->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $budgets], 200);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
//        dd($request);
        $t = [];
        $validator = Validator::make($request->all(),
            [
//                'profit' => 'required',
//                'jv_type' => 'required',
//                'reference' => 'required',
//                'create_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();
        $budget = new Budget();
        $budget->name     = $request->name;
        $budget->account_code     = $request->account_code;
        $budget->year_total     = $request->year_total;
        $budget->jan     = $request->jan;
        $budget->feb     = $request->feb;
        $budget->mar     = $request->mar;
        $budget->apr     = $request->apr;
        $budget->may     = $request->may;
        $budget->jun     = $request->jun;
        $budget->jul     = $request->jul;
        $budget->aug     = $request->aug;
        $budget->sep     = $request->sep;
        $budget->oct     = $request->oct;
        $budget->nov     = $request->nov;
        $budget->des     = $request->des;
        $budget->created_by        = $user->id;
        $budget->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $budget], 200);


    }


    public function show(Budget $budget)
    {
        //
    }


    public function edit(Budget $budget)
    {
        //
    }


    public function update(Request $request)
    {
//        dd($request);
        $t = [];
        $validator = Validator::make($request->all(),
            [
//                'profit' => 'required',
//                'jv_type' => 'required',
//                'reference' => 'required',
//                'create_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();
        $budget = Budget::with('account')->where('id',$request->id)->first();
//        dd($budget);
        $budget->update([
            'name' => $request->name,
            'account_code' => $request->account_code,
            'year_total' => $request->year_total,
            'jan' => $request->jan,
            'feb' => $request->feb,
            'mar' => $request->mar,
            'apr' => $request->apr,
            'may' => $request->may,
            'jun' => $request->jun,
            'jul' => $request->jul,
            'aug' => $request->aug,
            'sep' => $request->sep,
            'oct' => $request->oct,
            'nov' => $request->nov,
            'des' => $request->des,
            'updated_by' => $user->id
        ]);
//        $budget->name     = $request->name;
//        $budget->account_code     = $request->account_code;
//        $budget->year_total     = $request->year_total;
//        $budget->jan     = $request->jan;
//        $budget->feb     = $request->feb;
//        $budget->mar     = $request->mar;
//        $budget->apr     = $request->apr;
//        $budget->may     = $request->may;
//        $budget->jun     = $request->jun;
//        $budget->jul     = $request->jul;
//        $budget->aug     = $request->aug;
//        $budget->sep     = $request->sep;
//        $budget->oct     = $request->oct;
//        $budget->nov     = $request->nov;
//        $budget->des     = $request->des;
//        $budget->updated_by        = $user->id;
//        $budget->save();
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $budget], 200);

    }


    public function destroy(Budget $budget)
    {
        //
    }
}
