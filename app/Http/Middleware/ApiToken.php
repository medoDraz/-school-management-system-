<?php

namespace App\Http\Middleware;
use App\Bus_driver;
use App\User;
use App\Fathers;
use App\Mothers;
use App\Teacher;
use App\Security;
use App\Bus_supervisor;

use Closure;

class ApiToken
{
	public $attributes;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  
	
	$url=$request->path();
	//$url=explode('/',$url);
	//dd($url[1]);
	$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$uri_segments = explode('/', $uri_path);
	$parent_type="";




        if(!filter_has_var(INPUT_SERVER, 'HTTP_AUTHORIZATION')) {
            return $this->sendError('Please Login', '', $code = 401, '');
        }
        $api_token=$request->bearerToken();//filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION');strpos($url, 'user') !== false
        $author=null;
		if($uri_segments[2]=="user"){
			$author=User::where('api_token', $api_token)->first();
		}elseif($uri_segments[2]=="accounting"){
			$author=User::where('api_token', $api_token)->first();
		}elseif($uri_segments[2]=="driver"){
			$author=Bus_driver::where('api_token', $api_token)->first();
		}elseif($uri_segments[2]=="supervisor"){
			$author=Bus_supervisor::where('api_token', $api_token)->first();
		}
		elseif($uri_segments[2]=="teacher"){
			$author=Teacher::where('api_token', $api_token)->first();
		}
		elseif($uri_segments[2]=="security"){
			$author=Security::where('api_token', $api_token)->first();
		}
		elseif($uri_segments[2]=="parent"){
			$author=Fathers::where('api_token', $api_token)->first();
			if(!is_null($author)){
				$parent_type="father";
			}
			if(is_null($author)){
				$author=Mothers::where('api_token', $api_token)->first();
				if(!is_null($author)){
					$parent_type="mother";
				}
				
			}
		}else{
			$authors=array();
			$authors["user"]=User::where('api_token', $api_token)->first();
			$authors["accounting"]=User::where('api_token', $api_token)->first();
			$authors["driver"]=Bus_driver::where('api_token', $api_token)->first();
			$authors["supervisor"]=Bus_supervisor::where('api_token', $api_token)->first();
			$authors["teacher"]=Teacher::where('api_token', $api_token)->first();
			$authors["father"]=Fathers::where('api_token', $api_token)->first();
			$authors["security"]=Security::where('api_token', $api_token)->first();
			if(!is_null($authors["father"])){
				
				$parent_type="father";
			}
			$authors["mother"]=Mothers::where('api_token', $api_token)->first();
			if(!is_null($authors["mother"])){
				
				$parent_type="mother";
			}
			foreach($authors as $a){
				if(!is_null($a)){
					$author=$a;
					break;
				}
			}
		}
		
		
        if(is_null($author)){
            return $this->sendError('No Such Author', '',$code=401,'');
        }
		$request->attributes->add(['user' => $author,'parent_type'=>$parent_type]);
		//dd($author);
      return $next($request)
        ;
    }

    public function sendResponse($result, $message)
    {
       $response = [
            'error' => false,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code =200)
    {
        $response = [
            'error' => true,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $respone['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
