<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 支持响应格式
     *
     * @var array
     */
    protected $types = ['html', 'json'];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable               $e
     * @return \Illuminate\Http\Response
     */
    public function responseException($request, Throwable $e)
    {
        if ($request->acceptsJson()) {
            return $this->prepareJsonResponse($request, $e);
        }

        $message    = config('app.env') === 'local' ? $e->getMessage() : '系统繁忙';
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        if ($e instanceof NotFoundHttpException) {
            $statusCode = 404;
        }

        $content = view(
            $statusCode === 404 ? 'error.404' : 'error.error',
            ['exception' => $e, 'statusCode' => $statusCode, 'msg' => $message]
        );

        return response($content, 200, ['Content-Type' => 'text/html']);
    }
}
