<?php

namespace App\Http\Middleware;

use App\User;

//use App\Fathers;
//use App\Mothers;

use Closure;

class AccountedApiToken
{
    public $attributes;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $author = null;
        $author = User::where('api_token', $request->token)->where('status', 1)->first();
//
        if (is_null($author)) {
            return $this->sendError('No Such Author', '', $code = 401);
        }
        return $next($request);
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'error' => false,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = [
            'error' => true,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $respone['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
