<?php

namespace App\Exceptions;

use App\Libraries\Response;
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
        if ($e instanceof BatchException) {
            return Response::json($e->getCode() > 0 ? $e->getCode() : 404, [], null, $e->getMessage());
        } elseif (($e instanceof MethodNotAllowedHttpException || $e instanceof NotFoundHttpException) && $request->isMethod('post')) {
            return Response::json(404, [], null, 'Page Not Found. If error persists, contact info@domain.com');
        } elseif ($e instanceof MethodNotAllowedHttpException ) {
            return Response::json(405, [], null, $e->getMessage());
        } elseif ($e instanceof AuthenticationException) {
            return Response::json(419, [], null, $e->getMessage());
        } elseif ($e instanceof ValidationException) {
            return Response::json(422, [], null, $e->validator->errors()->messages());
        }

        return parent::render($request, $e);
    }
}
