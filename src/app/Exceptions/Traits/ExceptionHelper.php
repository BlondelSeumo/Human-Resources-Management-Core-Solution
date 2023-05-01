<?php


namespace App\Exceptions\Traits;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Swift_TransportException;
use Throwable;

trait ExceptionHelper
{
    public function apiFailResponse($request, Throwable $exception)
    {
        if ($request->expectsJson() && app()->environment('production')) {
            $message = trans('default.failed_response');
            $methodName = 'whenItIs'.$exception->getCode();

            if (method_exists($this, $methodName)) {
                $message = $this->{$methodName}($request, $exception);
            }

            if ($exception instanceof QueryException){
                return response()->json(['status' => false, 'message' => $message], 424);
            }

            if ($exception instanceof ModelNotFoundException) {
                $message = trans('default.resource_not_found', ['resource' => trans('default.resource')]);
                return response()->json(['status' => false, 'message' => $message], 404);
            }

            if ($exception instanceof Swift_TransportException) {
                return response()->json(['status' => false, 'message' => __t('incorrect_delivery_credential')], 403);
            }
        }
        return false;
    }

    public function whenItIs23000($request, Throwable $exception)
    {
        return trans('default.this_resource_already_referenced_message');
    }
}
