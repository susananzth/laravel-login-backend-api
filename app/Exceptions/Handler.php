<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;
use Throwable;
use DB;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $response = $this->handleException($request, $exception);
        return $response;
    }

    public function handleException($request, Throwable $exception)
    {
        DB::rollback();
        //dd($exception);
        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(
                $exception->getMessage(), 
                Response::HTTP_UNAUTHORIZED
            );
        }
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(
                $exception->getMessage(), 
                Response::HTTP_UNAUTHORIZED
            );
        }
        if ($exception instanceof ValidationException) {
            return $this->errorValidationsResponse(
                null, 
                $exception->validator->messages(), 
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->errorResponse(
                'Entry for '.str_replace('App', '', $exception->getModel()).' not found',
                Response::HTTP_NOT_FOUND);

        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                'The specified method for the request is invalid',
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(
                'The specified URL cannot be found', 
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof RouteNotFoundException) {
            return $this->errorResponse(
                'The specified URL cannot be  found.', 
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof RequestException) {
            return $this->errorResponse(
                'External API call failed.', 
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse(
                $exception->getMessage(), 
                $exception->getStatusCode()
            );
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);            
        }

        return $this->errorResponse(
            'Unexpected Exception. Try later', 
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

    }
}
