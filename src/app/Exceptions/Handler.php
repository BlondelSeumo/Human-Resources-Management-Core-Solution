<?php

namespace App\Exceptions;

use App\Exceptions\Traits\ExceptionHelper;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    use ExceptionHelper;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        GeneralException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     *
     * @return mixed|void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($this->apiFailResponse($request, $exception))
            return $this->apiFailResponse($request, $exception);

        // for custom error page
        if(!$request->expectsJson() && app()->environment('production')){
            $statusCode = $exception->getCode();

            if(view()->exists('custom_errors.'.$statusCode)) {
                $message = $exception->getMessage();

                if ($exception instanceof QueryException) {
                    $message = trans('default.failed_response');
                }

                if ($exception instanceof ModelNotFoundException) {
                    $message = trans('default.resource_not_found', ['resource' => trans('default.resource')]);
                }

                if (view()->exists('custom_errors.'.$statusCode)) {
                    return response()->view('custom_errors.'.$statusCode, [
                        'message' => $message
                    ]);
                }
            }
        }


        if (!$request->expectsJson() && $exception instanceof ModelNotFoundException) {
            return response()->view('custom_errors.404', [
                'message' => trans('default.resource_not_found', ['resource' => trans('default.resource')])
            ]);
        }

        if (!$request->expectsJson() && $exception instanceof AuthorizationException) {
            return response()->view('custom_errors.403', [
                'message' => $exception->getMessage()
            ]);
        }

        if ($request->expectsJson() && $exception instanceof TokenMismatchException) {
            $message = trans('default.csrf_token_mismatch_message') == 'default.csrf_token_mismatch_message' ?
                'CSRF token mismatch.' : trans('default.csrf_token_mismatch_message');

            return response()->json(['status' => false, 'message' => $message ], 419);
        }

        return parent::render($request, $exception);
    }

    /**
     * @param Request $request
     * @param AuthenticationException  $exception
     *
     * @return JsonResponse|RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : redirect()->guest(route('users.login.index'));
    }
}
