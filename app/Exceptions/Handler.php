<?php

namespace App\Exceptions;

use App\Traits\Responses\BaseCommonResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use BaseCommonResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    /*
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
    */

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return Mixed
     */
    public function render($request, Exception $exception)
    {
        if(!$this->isApiCall($request)) {
            $returnValue = parent::render($request, $exception);
        } else {
            $returnValue = $this->getJsonResponseForException($request, $exception);
        }

        if($returnValue === NULL) {
            if(config('app.debug')) {
                return parent::render($request, $exception);
            }
            return $this->errorResponse('Unexpected Exception. Try later', 500);
        }

        return $returnValue;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('Unauthenticated', 401);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        return $this->errorResponse($errors, 422);
    }

    /**
     * Determines if request is an api call.
     *
     * If the request URI contains '/api/'.
     *
     * @param Request $request
     * @return bool
     */
    protected function isApiCall(Request $request)
    {
        return strpos($request->getUri(), '/api/') !== false;
    }

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        // ValidationException Handler
        if($e instanceof  ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        // ModelNotFoundException Handler
        if($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));
            return $this->errorResponse(
                "Does not exists any {$modelName} with the specified identificator",
                404
            );
        }
        // AuthenticationException Handler
        if($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        // AuthorizationException Handler
        if($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 403);
        }
        // MethodNotAllowedHttpException Handler
        if($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }
        // NotFoundHttpException Handler
        if($e instanceof NotFoundHttpException) {
            return $this->errorResponse('The specified URL cannot be found', 404);
        }
        // QueryException Handler
        if($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1451) {
                return $this->errorResponse(
                    'Cannot remove this resources permanently. It is related with any other resource.',
                    409
                );
            }
        }
        // HttpException Handler
        if($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return NULL;
    }

}
