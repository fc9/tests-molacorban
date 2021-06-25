<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\{
    MethodNotAllowedHttpException,
    NotFoundHttpException
};
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        #dd($request, $e);
        if ($e instanceof ValidationException) {

            return response()->json([
                'status' => 422,
                'errors' => $e->validator->errors()->messages()
            ], 422);

        } elseif ($e instanceof PurchaseException) {

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());

        } elseif (($e instanceof MethodNotAllowedHttpException || $e instanceof NotFoundHttpException)
            && $request->isMethod('post')) {

            return response()->json([
                'status' => 404,
                'message' => 'Page Not Found. If error persists, contact info@domain.com',
            ], 404);

        } elseif ($e instanceof AuthenticationException) {

            return response()->json([
                'status' => 419,
                'message' => $e->getMessage()
            ], 419);

        }

//        if ($e instanceof ModelNotFoundException) {
//            return response()->json([
//                'code' => $e->getCode(),
//                'message' => 'Entry for ' . str_replace('App', '', $e->getModel()) . ' not found'
//            ], 404);
//        }

        return parent::render($request, $e);
    }
}
