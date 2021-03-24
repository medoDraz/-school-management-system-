<?php

namespace App\Http\Controllers\API\Accounting;

use App\Global_Number;
use App\User;
use App\Analytics_Code;
use App\Chart_Account;
use Spatie\Permission\Models\Role;
use App\JournalVoucher;
use App\Students;
use App\Staffview;
use App\App_config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class GlobalNumbering extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission_api:Access_GlobalSerial'])->only('store');
        // $this->middleware(['permission_api:Access_Analytics'])->only('read');
        // $this->middleware(['permission_api:Edit_Analytics'])->only(['update','changestatus']);
    }
    public function dashboard(){
        $userscount=User::whereHas('roles',function($q){
            $q->where('accountant', '1');
        })->count();
        $rolescount=Role::where('accountant',1)->count();
        $analticscount=Analytics_Code::count();
        $acountscount=Chart_Account::count();
        $journalscount=JournalVoucher::count();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => [
                'userscount'=>$userscount,
                'rolescount'=>$rolescount,
                'analticscount'=>$analticscount,
                'acountscount'=>$acountscount,
                'journalscount'=>$journalscount
            ]
        ], 200);
    }

     public function getlastinsertid(){
        $userscount=User::latest('id')->first()->id;
        //dd($userscount);
        $rolescount=Role::count();
        $analticscount=Analytics_Code::count();
        $acountscount=Chart_Account::count();
        $journalid=JournalVoucher::latest('id')->first()->id;
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => [
                'userscount'=>$userscount,
                'rolescount'=>$rolescount,
                'analticscount'=>$analticscount,
                'acountscount'=>$acountscount,
                'journalid'=>$journalid
            ]
        ], 200);
    }

    public  function store(Request $request){
        //dd($request);
        $t = [
        ];
        $validator = Validator::make($request->all(),
            [
                'cash_in'=>'required',
                'cash_out'=>'required',
                'payment' => 'required',
                'purchase'=>'required',
                'journal'=>'required',
                'inventory'=>'required',
                // 'cashin_new_number'=>'required',
                // 'cashout_new_number'=>'required',
                // 'pur_new_number' => 'required',
                // 'pay_new_number'=>'required',
                // 'journal_new_number'=>'required',
                // 'inventory_new_number'=>'required'
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->errors()], 200);
        }
        
        $request_data=$request->except([
            'cashin_previous_number','cashin_new_number','cashout_previous_number',
            'cashout_new_number','pur_previous_number','pur_new_number','pay_previous_number',
            'pay_new_number','journal_previous_number','journal_new_number','inventory_previous_number','inventory_new_number'
        ]);
        $name=['cash_in','cash_out','payment','purchase','journal','inventory'];
        $tables_name=['cash_in','cash_out','payment','purchase','journal_vouchers','inventory'];
        $type=[];
        $serial_number=[];
        if($request_data['cash_in'] == 'previous'){
            array_push($type, 'previous');
            array_push($serial_number, $request->cashin_previous_number);
            $request_data['cashin_previous_number']=$request->cashin_previous_number;
        }else{
            array_push($type, 'new');
            array_push($serial_number, $request->cashin_new_number);
            $request_data['cashin_new_number']=$request->cashin_new_number;
        }
        if($request_data['cash_out'] == 'previous'){
            array_push($type, 'previous');
            array_push($serial_number, $request->cashout_previous_number);
            $request_data['cashout_previous_number']=$request->cashout_previous_number;
        }else{
            array_push($type, 'new');
            array_push($serial_number, $request->cashout_new_number);
            $request_data['cashout_new_number']=$request->cashout_new_number;
        }
        if($request_data['payment'] == 'previous'){
            array_push($type, 'previous');
            array_push($serial_number, $request->pay_previous_number);
            $request_data['pay_previous_number']=$request->pay_previous_number;
        }else{
            array_push($type, 'new');
            array_push($serial_number, $request->pay_new_number);
            $request_data['pay_new_number']=$request->pay_new_number;
        }
        if($request_data['purchase'] == 'previous'){
            array_push($type, 'previous');
            array_push($serial_number, $request->pur_previous_number);
            $request_data['pur_previous_number']=$request->pur_previous_number;
        }else{
            array_push($type, 'new');
            array_push($serial_number, $request->pur_new_number);
            $request_data['pur_new_number']=$request->pur_new_number;
        }
        if($request_data['journal'] == 'previous'){
            array_push($type, 'previous');
            //DB::statement("ALTER TABLE journal_vouchers AUTO_INCREMENT = " . (integer)$request->journal_previous_number .";");
            array_push($serial_number, $request->journal_previous_number);
            $request_data['journal_previous_number']=$request->journal_previous_number;
        }else{
            array_push($type, 'new');
            //DB::statement("ALTER TABLE journal_vouchers AUTO_INCREMENT = " + (integer)$request->journal_new_number +";");
            array_push($serial_number, $request->journal_new_number);
            $request_data['journal_new_number']=$request->journal_new_number;
        }
        if($request_data['inventory'] == 'previous'){
            array_push($type, 'previous');
            array_push($serial_number, $request->inventory_previous_number);
            $request_data['inventory_previous_number']=$request->inventory_previous_number;
        }else{
            array_push($type, 'new');
            array_push($serial_number, $request->inventory_new_number);
            $request_data['inventory_new_number']=$request->inventory_new_number;
        }

       //dd($serial_number[4]);
       $user=User::where('api_token', $request->token)->first();

       DB::statement("ALTER TABLE journal_vouchers AUTO_INCREMENT = ".$serial_number[4].";");
       foreach ($name as $key=>$name){
            $global_number=new Global_Number();
           // DB::statement("ALTER TABLE journal_vouchers AUTO_INCREMENT = " . (integer)$serial_number[$key] .";");
            $global_number->name=$name;
            $global_number->type=$type[$key];
            $global_number->serial=$serial_number[$key];
            $global_number->created_by    = $user->id;
            $global_number->save();
        }
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => []], 200);
    }

    public function accountantperiod (Request $request){

        //dd($request);

        $flight = App_config::updateOrCreate(
            ['type' => 'accountant_period'],
            ['data' => $request->period]
        );
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => []], 200);
    }

    public function getaccountantperiod (Request $request){

        //dd($request);

        $flight = App_config::where('type' , 'accountant_period')->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $flight], 200);
    }

    public function getallstudent(Request $request){
        // dd($request);
        $analytics_code = Analytics_Code::where('id', $request->analysis_id)->first();
        //dd($analytics_code);
        if ($analytics_code->records_type == 'Students') {
           if ($request->limit) {
               $students = Students::with(['school','grade','classgroup'])->select('*',DB::raw('CONCAT(first_name," ",last_name) as name'))->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $students], 200);
           } else
           {
                $students = Students::with(['school','grade','classgroup'])->select('*',DB::raw('CONCAT(first_name," ",last_name) as name'))->latest()->get();
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $students], 200);
           }
            
        } elseif($analytics_code->records_type == 'Staff'){
            if ($request->limit){
                $stuff = Staffview::paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $stuff], 200);
            } else {
                $stuff = Staffview::all();
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $stuff], 200);
            }
        }
        
    }
    
}
