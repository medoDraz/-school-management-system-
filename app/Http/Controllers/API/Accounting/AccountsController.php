<?php

namespace App\Http\Controllers\API\Accounting;

use App\Chart_Account;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;

class AccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_Accounts'])->only('store');
        $this->middleware(['permission_api:Access_Accounts'])->only(['index']);
        $this->middleware(['permission_api:Edit_Accounts'])->only(['update', 'changestatus']);
    }

    public function index(Request $request)
    {
        $new_code = "CONVERT(account_code,CHAR) as new_code";
        $accounts = Chart_Account::with('user')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('account_code', 'like', '%' . $request->search . '%')
                    ->orWhere('account_name', 'like', '%' . $request->search . '%')
                    ->orWhere('account_type', 'like', '%' . $request->search . '%');
            });

        })->select('*', DB::raw('CONVERT(account_code,CHAR) as new_code'))->orderBy('new_code')->latest()->paginate($request->limit);
        //dd($accounts);

        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $accounts], 200);
    }

    public function getallaccounts(Request $request)
    {
        $accounts = Chart_Account::with(['user', 'categories', 'jvs', 'budgets'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('account_code', 'like', '%' . $request->search . '%')
                    ->orWhere('account_name', 'like', '%' . $request->search . '%');
            });

        })->where('status', 'Open')->select('*', DB::raw('CONVERT(account_code,CHAR) as new_code'))->orderBy('new_code')->latest()->get();
        //dd($accounts);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $accounts], 200);
    }

    public function getaccountswithjvs(Request $request)
    {
//        dd($from = $request->pfrom);
        $accounts = Chart_Account::with(['user', 'categories', 'jvs' => function ($q) use ($request) {
            return $q
                ->when($request->year, function ($query) use ($request) {
                    return $query->whereYear('date', $request->year);
                })
                ->when($request->pfrom, function ($query) use ($request) {
                    return $query->date($request->pfrom, $request->pto);
                });
        }, 'jvs_before' => function ($q) use ($request) {
            return $q
                ->when($request->pfrom, function ($query) use ($request) {
                    return $query->whereDate('date', '<', $request->pfrom);
                });
        }, 'jvs_after' => function ($q) use ($request) {
            return $q
                ->when($request->pto, function ($query) use ($request) {
                    return $query->whereDate('date', '>', $request->pto);
                });
        }, 'jvs.journal' => function ($q) use ($request) {
            return $q
                ->when($request->profit, function ($query) use ($request) {

                    return $query->where('profit_centre', $request->profit);
                });
        }, 'budgets' => function ($q) use ($request) {
            return $q->when($request->pfrom, function ($query) use ($request) {
                return $query->whereDate('budgets.created_at', '>=', $request->pfrom)->whereDate('budgets.created_at', '<=', $request->pto);
            });
        }])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('account_code', 'like', '%' . $request->search . '%')
                    ->orWhere('account_name', 'like', '%' . $request->search . '%');
            });

        })->where('status', 'Open')->where(function ($q) use ($request) {

            return $q->when($request->account_type, function ($query) use ($request) {

                return $query->where('account_type', $request->account_type);
            });

        })->select('*', DB::raw('CONVERT(account_code,CHAR) as new_code'))->orderBy('new_code')->latest()->get();
        //dd($accounts);

        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $accounts
        ], 200);
    }

    public function getallaccountscategory(Request $request)
    {
        $accounts = Chart_Account::with(['user', 'jvs', 'budgets'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('account_code', 'like', '%' . $request->search . '%')
                    ->orWhere('account_name', 'like', '%' . $request->search . '%');
            });

        })->where('header', 'true')->select('*', DB::raw('CONVERT(account_code,CHAR) as new_code'))->orderBy('new_code')->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $accounts], 200);
    }

    public function store(Request $request)
    {

        $t = [
            'global_account' => __('site.global_account'),
            'code' => __('site.account_code'),
            'account_name' => __('site.account_name'),
            'account_type' => __('site.account_type'),
            'currency' => __('site.currency'),
            'status' => __('site.status'),
        ];
        $validator = Validator::make($request->all(),
            [
                'code' => 'required|unique:chart__accounts,account_code',
                'account_name' => 'required',
                'account_type' => 'required',
                'currency' => 'required',
                // 'global_account' => 'required',
                // 'functional_area' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $request_data = $request->except(['analytics_code1']);
        if ($request->analytics_code == 'true') {
            $request_data['analytics_code'] = $request->analytics_code1;
        }
        //dd($request_data);
        $user = User::where('api_token', $request->token)->first();

        $account = new Chart_Account();
        $account->account_code = $request_data['code'];
        $account->header = $request_data['header'];
        $account->category = $request_data['category'];
        $account->functional_area = $request_data['functional_area'];
        $account->account_name = $request_data['account_name'];
        $account->account_name_ar = $request_data['account_name_ar'];
        $account->description = $request_data['description'];
        $account->account_type = $request_data['account_type'];
        $account->analytics_code = $request_data['analytics_code'];
        $account->currency = $request_data['currency'];
        $account->status = $request_data['status'];
        $account->global_account = $request_data['global_account'];
        $account->created_by = $user->id;
        $account->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => []], 200);

    }

    public function edit(Request $request)
    {

        // dd($request);
        // if($request->account_code){
        //     $account = Chart_Account::where('account_code', $request->account_code)->orWhere('account_code',$request->id)->first();
        // }
        // ->orWhere('account_code',$request->account_code)->orWhere('category',$request->category)
        $account = Chart_Account::with('analysis')->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $account], 200);

    }

    public function update(Request $request)
    {
        //dd($request);
        $account = Chart_Account::where('id', $request->id)->first();
        $t = [
            'global_account' => __('site.global_account'),
            'account_code' => __('site.account_code'),
            'account_name' => __('site.account_name'),
            'account_type' => __('site.account_type'),
            'currency' => __('site.currency'),
            'status' => __('site.status'),
        ];
        $validator = Validator::make($request->all(),
            [
                'account_code' => ['required', Rule::unique('chart__accounts')->ignore($account->id)],
                'account_name' => 'required',
                'account_type' => 'required',
                'currency' => 'required',
                // 'global_account' => 'required',
                // 'functional_area' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $user = User::where('api_token', $request->token)->first();
        $request->request->add(['updated_by' => $user->id]);
        //$request_data['updated_by'] = $user->id;


//dd($request);
        //      $account->update($request);
        $account->account_code = $request->account_code;
        $account->header = $request->header;
        $account->category = $request->category;
        $account->functional_area = $request->functional_area;
        $account->account_name = $request->account_name;
        $account->account_name_ar = $request->account_name_ar;
        $account->description = $request->description;
        $account->account_type = $request->account_type;
        $account->analytics_code = $request->analytics_code;
        $account->currency = $request->currency;
        $account->status = $request->status;
        $account->global_account = $request->global_account;
        $account->updated_by = $user->id;
        $account->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);


    }

    public function destroy(Request $request)
    {

        $account = Chart_Account::findOrFail($request->id);
        $account->delete();

        return response()->json(['error' => false, 'message' => __('site.deleted_successfully'), 'data' => []], 200);

    }

    public function changestatus(Request $request)
    {
        $account = Chart_Account::where('id', $request->id)->first();
        $user = User::where('api_token', $request->token)->first();
        $account->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);

    }
}
