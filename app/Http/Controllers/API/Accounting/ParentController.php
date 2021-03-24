<?php

namespace App\Http\Controllers\API\Accounting;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Family; 


use App\Student; 
use App\Std_Bus;
use App\Bus_info;
use App\Bus_routes; 
use App\Bus_supervisor;
use App\Bus_driver;  
use Illuminate\Support\Facades\DB;

use App\Fathers;
use App\Mothers;


use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;

class ParentController extends Controller 
{

public $successStatus = 200;

/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
	 
	
      public function login(Request $request){ 
		//dd(is_numeric($request->username));

		 if(!empty($request->email)){
				
				
				if(is_numeric($request->email)){
					
					if(Auth::guard('father')->attempt(['mobile' => $request->email, 'password' => $request->password, 'status'=> 0]) 
						|| Auth::guard('mother')->attempt(['mobile' => $request->email, 'password' => $request->password, 'status'=> 0])){ 
						return response()->json(['error'=>true , 'message'=>'Sorry not active account ','data' =>[]], 401); 
					}
					
					elseif(Auth::guard('father')->attempt(['mobile' => $request->email, 'password' => $request->password, 'status'=> 1])){ 
						
						$father = Auth::guard('father')->user(); 
						
						$success['token'] =  $father->createToken('Student Father')-> accessToken; 
						//dd("succes");
						$father->api_token = $success['token'];
						$father->save();
						$father->type="father";
						return response()->json(['error'=>false , 'message'=>'Login success','token' => $father->api_token], $this-> successStatus); 
					}
					
					elseif(Auth::guard('mother')->attempt(['mobile' => $request->email, 'password' => $request->password, 'status'=> 1])){ 
						//dd("tr");
						$mother = Auth::guard('mother')->user(); 
						
						$success['token'] =  $mother->createToken('Student mother')-> accessToken; 

						$mother->api_token = $success['token'];
						$mother->save();
						//dd($mother);
						$mother->type="mother";
						return response()->json(['error'=>false , 'message'=>'Login success','token' => $mother->api_token], $this-> successStatus); 
					}  
					else{ 
						
						
						return response()->json(['error'=>true , 'message'=>'Unauthorised','data' =>[]], 401); 
					}
				}
				else{
					if(Auth::guard('father')->attempt(['email' => $request->email, 'password' => $request->password, 'status'=> 0])
						||Auth::guard('mother')->attempt(['email' => $request->email, 'password' => $request->password, 'status'=> 0])){ 
						return response()->json(['error'=>true , 'message'=>'Sorry not active account ','data' =>[]], 401); 
					}
					elseif(Auth::guard('father')->attempt(['email' => $request->email, 'password' => $request->password, 'status'=> 1])){ 
						
						$father = Auth::guard('father')->user(); 
						
						$success['token'] =  $father->createToken('Student Father')-> accessToken; 
						//dd("succes");
						$father->api_token = $success['token'];
						$father->save();
						$father->type="father";
						return response()->json(['error'=>false , 'message'=>'Login success','token' => $father->api_token], $this-> successStatus); 
					}
					elseif(Auth::guard('mother')->attempt(['email' => $request->email, 'password' => $request->password, 'status'=> 1])){ 
						
						$mother = Auth::guard('mother')->user(); 
						
						$success['token'] =  $mother->createToken('Student mother')-> accessToken; 
						//dd("succes");
						$mother->api_token = $success['token'];
						$mother->save();
						$mother->type="mother";

						return response()->json(['error'=>false , 'message'=>'Login success','token' => $mother->api_token], $this-> successStatus); 
					}
					else{ 
						
						
						return response()->json(['error'=>true , 'message'=>'Unauthorised','data' =>[]], 401); 
					}
					
				}
			
			
				
			
				
			 
		}else{
				return response()->json(['error'=>true , 'message'=>'Unauthorised ','data' =>[]], 401); 

		} 
    }

/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
	 public function getsoninfo(Request $request) 
    { 
        //$user = Auth::user(); 
		$user = $request->get('user');
		
		
		if(!is_null($user) && isset($request->type)&& !empty($request->type)){
			$res="";
			if($request->type=="father"){
				//$student=Student::where('father_id',$user->id)->get();
				$res=DB::table('Student')
					->join('Std_Bus', 'Student.id', '=', 'Std_Bus.stdid')
					->join('Bus_info', 'Bus_info.id', '=', 'Bus_info.id')
					->join('station', 'Student.station_id', '=', 'station.id')
					 ->select('Student.id','Student.name','Student.photo','Student.lat','Student.lng','Student.station_id','Bus_info.id As bus_id','Bus_info.busno As bus_no'
					 , 'station.lat', 'station.lng')
					->where('Student.deleted_at',null)
					->where('Std_Bus.deleted_at',null)
					->where('Bus_info.deleted_at',null)
					->where('station.deleted_at',null)
					->get();
				
			}elseif($request->type=="mother"){
				//$student=Student::where('mother_id',$user->id)->get();
				$res=DB::table('Student')
					->join('Std_Bus', 'Student.id', '=', 'Std_Bus.stdid')
					->join('Bus_info', 'Std_Bus.bus_id', '=', 'Bus_info.id')
					->join('station', 'Student.station_id', '=', 'station.id')
					
					 ->select('Student.id','Student.name','Student.photo','Student.lat','Student.lng','Student.station_id','Bus_info.id As bus_id','Bus_info.busno As bus_no'
					 , 'station.lat As station_lat', 'station.lng As station_lng', 'station.name As station_name', 'station.description As station_description')
						->where('mother_id',$user->id)->get();
			}
			
		}
		//dd($user);
        if(isset($user->status) && $user->status == 1) 
			return response()->json(['error'=>false , 'message'=>' success','data' => $res], $this-> successStatus); 
		else
			 return response()->json(['error'=>true , 'message'=>'Unauthorised','data' =>[]], 401); 
    } 
	
    public function getbusinfo(Request $request) 
    { 
		
		$user = $request->get('user');
		$arr=array();
		
		
		 
		if(isset($request->busid)&&!empty($request->busid)){
			$route = DB::table('bus_routes')
            ->join('bus_driver', 'bus_routes.driver_name', '=', 'bus_driver.id')
            ->join('bus_supervisor', 'bus_routes.superv1', '=', 'bus_supervisor.id')
			->join('bus_supervisor as supervisor2', 'bus_routes.superv2', '=', 'supervisor2.id')
			
            ->select('bus_routes.*',DB::raw("CONCAT(bus_driver.a_name,' ',bus_driver.e_name) AS driver_name") , 'bus_driver.id AS driver_id' ,'bus_driver.mobile AS driver_mobile'
			, 'bus_driver.photo AS driver_photo',DB::raw("CONCAT(bus_supervisor.a_name,' ',bus_supervisor.e_name) AS supervisor1_name") ,'bus_supervisor.id AS supervisor1_id' , 'bus_supervisor.mobile AS supervisor1_mobile'
			, 'bus_supervisor.photo AS supervisor1_photo',DB::raw("CONCAT(supervisor2.a_name,' ',supervisor2.e_name) AS supervisor2_name") ,'supervisor2.id AS supervisor2_id' , 'supervisor2.mobile AS supervisor2_mobile'
			, 'supervisor2.photo AS supervisor2_photo')
            ->where("bus_id",$request->busid)
			->where('bus_routes.deleted_at',null)
					->where('bus_driver.deleted_at',null)
					->where('bus_supervisor.deleted_at',null)
					->where('supervisor2.deleted_at',null)
					->get();
			
			
			

		}
        if(isset($user->status) && $user->status == 1) 
			return response()->json(['error'=>false , 'message'=>'success','data' => $route], $this-> successStatus); 
		else
			 return response()->json(['error'=>true , 'message'=>'Unauthorised','data' =>[]], 401); 
    } 

	
	function get_parent_info(Request $request){
		$user = $request->get('user');
		
		if(!empty($user)){
			
				 
			return response()->json(['error'=>false , 'message'=>'success','data' => $user], $this-> successStatus); 
		
		}
		else
			 return response()->json(['error'=>true , 'message'=>'Unauthorised','data' =>[]], 401); 
		 
	}

	public function details(Request $request)
    { 
        //$user = auth()->user();
		//dd($request);
		// $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		// $uri_segments = explode('/', $uri_path);
		//dd($uri_segments[2]);
		$user=null;
		$user = Fathers::where('api_token', $request->token)->first();
		if(is_null($user)){
			$user=Mothers::where('api_token', $request->token)->first();
		}
		//dd($user);
        if(isset($user->status) && $user->status == 1) {
        	//$permissionsofRole=$user->roles[0]->getAllPermissions()->pluck('name');
        	//$permissionsofUser=$user->getAllPermissions()->pluck('name');
        	//dd($permissionsofUser);
			return response()->json([
            'id'=>$user->id,
            'api_token'=>$user->api_token,
            'ask_change_pass'=>$user->ask_change_pass,
            'roles'=>['a'],
			'permissions' => [],
            'name'=>$user->first_name.' '.$user->last_name ,
            'avatar'=>$user->img ?? 'uploads/987e5295e42fc82faa7459f44f9c7587.png',
            'introduction'=>$user->title,
        ]);
		}	//return response()->json(['user' => $user], 200);
		else
			 return response()->json(['error'=>true , 'message'=>'Unauthorised541541','data' =>[]], 401); 
    } 
		
		
	
	
	 
}