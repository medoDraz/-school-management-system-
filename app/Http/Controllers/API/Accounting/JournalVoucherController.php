<?php

namespace App\Http\Controllers\API\Accounting;

use App\Chart_Account;
use App\Http\Controllers\Controller;
use App\Imports\JVSImport;
use Illuminate\Http\Request;
use App\JournalVoucher;
use App\Jvs;
use App\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Add_JV'])->only('store');
        $this->middleware(['permission_api:Access_JV'])->only(['index']);
        $this->middleware(['permission_api:Edit_JV'])->only(['update', 'changestatus']);
    }

    public function index(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $journal_voucher = JournalVoucher::with(['user', 'jvscount'])->where('created_by', $user->id)->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $journal_voucher], 200);
    }

    public function getalljournals(Request $request)
    {

//        dd($ids);
        $journal_voucher = JournalVoucher::with([
            'user', 'jvscount' => function ($q) use ($request) {
                return $q
                    ->when($request->date_from, function ($query) use ($request) {
                        return $query->date($request->date_from, $request->date_to);
                    })
                    ->when($request->account_no_from, function ($query) use ($request) {
                        $accounts = Chart_Account::whereBetween('account_code', [$request->account_no_from, $request->account_no_to])->get();
                        $ids = [];
                        foreach ($accounts as $account) {
                            array_push($ids, $account->id);
                        }
                        return $query->accountcode($ids);
                    });
            }, 'jvscount.journal', 'jvscount.account', 'jvscount.analysis', 'jvscount.dimension_code'
        ])->where(function ($q) use ($request) {
            return $q->when($request->search, function ($query) use ($request) {
                return $query->where('journal_reference', 'like', '%' . $request->search . '%');
            });
        })->when($request->profit, function ($q) use ($request) {
            return $q->where('profit_centre', $request->profit);
        })->when($request->type, function ($q) use ($request) {
            return $q->where('type', $request->type);
        })->when($request->jv_no, function ($q) use ($request) {
            return $q->where('id', $request->jv_no);
        })->when($request->date, function ($q) use ($request) {
            return $q->where('date', $request->date);
        })->when($request->status, function ($q) use ($request) {
            return $q->where('status', $request->status);
        })->latest()->paginate($request->limit);

        $journal_voucherbefor = JournalVoucher::with([
            'jvscount' => function ($q) use ($request) {
                return $q
                    ->when($request->date_from, function ($query) use ($request) {
                        return $query->whereDate('date', '<', $request->date_from);
                    })
                    ->when($request->account_no_from, function ($query) use ($request) {
                        $accounts = Chart_Account::whereBetween('account_code', [$request->account_no_from, $request->account_no_to])->get();
                        $ids = [];
                        foreach ($accounts as $account) {
                            array_push($ids, $account->id);
                        }
                        return $query->accountcode($ids);
                    });
            }, 'jvscount.journal', 'jvscount.account', 'jvscount.analysis', 'jvscount.dimension_code'
        ])->when($request->profit, function ($q) use ($request) {
            return $q->where('profit_centre', $request->profit);
        })->latest()->get();

        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' =>[
            'journal_voucher' =>$journal_voucher,
            'journal_voucherbefor' =>$journal_voucherbefor,
            ] ], 200);
    }

    public function store(Request $request)
    {
        //dd($request);

        $t = [

        ];
        $validator = Validator::make($request->all(),
            [
                'profit' => 'required',
                'jv_type' => 'required',
                'reference' => 'required',
                'create_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();

        $journal_voucher = new JournalVoucher();
        $journal_voucher->profit_centre = $request->profit;
        $journal_voucher->type = $request->jv_type;
        $journal_voucher->journal_reference = $request->reference;
        $journal_voucher->date = $request->date;
        // $journal_voucher->create_type       = $request->create_type;
        $journal_voucher->status = 'Hold';
        $journal_voucher->created_by = $user->id;
        $journal_voucher->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $journal_voucher], 200);

    }

    public function journaledit(Request $request)
    {

        $journal_voucher = JournalVoucher::with('user')->where('id', $request->id)->first();
        $jvs = Jvs::with(['account', 'analysis','dimension_code'])->where('journal_id', $request->id)->latest()->get();
        $jvs_debit = Jvs::where('journal_id', $request->id)->where('debit_credit', 'Debit')->get();
        $jvs_credit = Jvs::where('journal_id', $request->id)->where('debit_credit', 'Credit')->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => [
            'journal_voucher' => $journal_voucher,
            'jvs' => $jvs,
            'jvs_debit' => $jvs_debit,
            'jvs_credit' => $jvs_credit,
        ]], 200);

    }

    public function update(Request $request)
    {
        //dd($request);

        $t = [

        ];
        $validator = Validator::make($request->all(),
            [
                'profit' => 'required',
                'jv_type' => 'required',
                'reference' => 'required',
                //'create_type' => 'required',
                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();

        $journal_voucher = JournalVoucher::where('id', $request->id)->first();
        $journal_voucher->profit_centre = $request->profit;
        $journal_voucher->type = $request->jv_type;
        $journal_voucher->journal_reference = $request->reference;
        $journal_voucher->amount = $request->amount;
        $journal_voucher->date = $request->date;
        // $journal_voucher->create_type       = $request->create_type;
        $journal_voucher->status = 'Posted';
        $journal_voucher->updated_by = $user->id;
        $journal_voucher->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $journal_voucher], 200);

    }

    public function getallJvs(Request $request)
    {
        $jvs = Jvs::where('journal_id', $request->journal_id)->with(['account','analysis','dimension_code'])->latest()->get();
        $jvs_debit = Jvs::where('journal_id', $request->journal_id)->where('debit_credit', 'Debit')->get();
        $jvs_credit = Jvs::where('journal_id', $request->journal_id)->where('debit_credit', 'Credit')->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => ['jvs' => $jvs, 'jvs_debit' => $jvs_debit, 'jvs_credit' => $jvs_credit]], 200);
    }

    public function addjvs(Request $request)
    {
        //dd($request);

        $t = [

        ];
        $validator = Validator::make($request->all(),
            [
                'account_code' => 'required',
                'amount' => 'required',
                'currency' => 'required',
                'debit_credit' => 'required',
                'description' => 'required',


                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

        $user = User::where('api_token', $request->token)->first();
        if ($request->id == null) {
            $jvs = new Jvs();
            $jvs->date = $request->date;
            $jvs->account_code = $request->account_code;
            $jvs->amount = $request->amount;
            $jvs->currency = $request->currency;
            $jvs->debit_credit = $request->debit_credit;
            $jvs->description = $request->description;
            $jvs->analytics_code = $request->analytics_code;
            $jvs->dimension = $request->dimension;
            $jvs->journal_id = $request->journal_id;
            $jvs->save();

            return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $jvs], 200);
        } else {
            $jvs = Jvs::where('id', $request->id)->first();
            $jvs->date = $request->date;
            $jvs->account_code = $request->account_code;
            $jvs->amount = $request->amount;
            $jvs->currency = $request->currency;
            $jvs->debit_credit = $request->debit_credit;
            $jvs->description = $request->description;
            $jvs->analytics_code = $request->analytics_code;
            $jvs->dimension = $request->dimension;
            $jvs->journal_id = $request->journal_id;
            $jvs->save();

            return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $jvs], 200);
        }


    }

    public function changestatus(Request $request)
    {
        //dd($request);
        if ($request->status == 'copy') {
            $account = JournalVoucher::with(['jvscount'])->where('id', $request->id)->first();

            $new_journal = new JournalVoucher();
            $new_journal = $account;
            $new_journal->status = 'Hold';
            unset($new_journal['id']);
            // $new_journal->save();
            $data = json_decode($new_journal, true);
            DB::beginTransaction();
            $new = JournalVoucher::create($data);

            foreach ($account->jvscount as $key => $value) {
                //dd($value);
                $jv = new Jvs();
                $jv = $value;
                $jv->journal_id = $new->id;

                unset($jv['id']);
                // $new_journal->save();
                $data_jv = json_decode($jv, true);
                $new_jv = Jvs::create($data_jv);

            }
            DB::commit();
            return response()->json(['error' => false, 'message' => __('site.copy_successfully'), 'data' => []], 200);

        } elseif ($request->status == 'reverse') {
            $account = JournalVoucher::with(['jvscount'])->where('id', $request->id)->first();

            $new_journal = new JournalVoucher();
            $new_journal = $account;
            $new_journal->status = 'Hold';
            unset($new_journal['id']);
            // $new_journal->save();
            $data = json_decode($new_journal, true);
            DB::beginTransaction();
            $new = JournalVoucher::create($data);

            foreach ($account->jvscount as $key => $value) {
                //dd($value);
                $jv = new Jvs();
                $jv = $value;
                $jv->journal_id = $new->id;
                if ($jv->debit_credit == 'Debit') {
                    $jv->debit_credit = 'Credit';
                } elseif ($jv->debit_credit == 'Credit') {
                    $jv->debit_credit = 'Debit';
                }
                unset($jv['id']);
                $data_jv = json_decode($jv, true);
                $new_jv = Jvs::create($data_jv);
            }
            DB::commit();
            return response()->json(['error' => false, 'message' => __('site.reversed_successfully'), 'data' => []], 200);
        } else {
            $journal = JournalVoucher::where('id', $request->id)->first();
            $user = User::where('api_token', $request->token)->first();
//            dd($request);
            $journal->update([
                'status' => $request->status,
                'amount' => $request->amount,
                'updated_by' => $user->id,
            ]);

            return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);

        }

    }

    public function delete_jvs(Request $request)
    {
        $jvs = Jvs::findOrFail($request->id);
        $jvs->delete();
        return response()->json(['error' => false, 'message' => __('site.deleted_successfully'), 'data' => []], 200);
    }

    public function delete_journal(Request $request)
    {
        $journal = JournalVoucher::where('id', $request->id)->first();
        $journal->delete();
        $jvs = Jvs::where('journal_id', $request->id)->get();
        foreach ($jvs as $item) {
            $item->delete();
        }

        return response()->json(['error' => false, 'message' => __('site.deleted_successfully'), 'data' => []], 200);
    }

    public function edit(Request $request)
    {
//        dd($request);
        $account = Jvs::with(['account', 'analysis','dimension_code'])->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $account], 200);

    }

    public function getjournal(Request $request)
    {
//        dd($request);
        $account = JournalVoucher::where('profit_centre', $request->profit)->where('type', $request->jv_type)
            ->where('journal_reference', $request->reference)->whereDate('created_at', $request->date)
            ->where('status', 'Posted')
            ->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $account], 200);

    }

    public function upload_jvs(Request $request)
    {
        $t = [
        ];
        $validator = Validator::make($request->all(),
            [
                'journal_id' => 'required',
                'file_Url' => 'required|mimes:xls,xlsx,xlm,xla,xlc,xlt,xlw,ods',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $journal_id = $request->journal_id;
        if ($request->hasFile('file_Url')) {
            try {
                Excel::import(new JVSImport($journal_id), request()->file('file_Url'));
                return response()->json(['error' => false, 'message' => 'Insert Record successfully.', 'data' => []], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => $e, 'data' => []], 200);
            }
        }
    }

    public function update_jvs(Request $request)
    {
//        dd($request);
        $jvs = Jvs::where('id', $request->id)->first();
        $jvs->description = $request->description;
        $jvs->analytics_code = $request->analytics_code;
        $jvs->dimension = $request->dimension;
        $jvs->allocated = $request->allocated;

        $jvs->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $jvs], 200);
    }

}
