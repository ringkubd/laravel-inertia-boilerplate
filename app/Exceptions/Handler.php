<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    public function render($request, Exception|Throwable $e)
    {
        $response = parent::render($request, $e);

        $contactEmail = env('SUPPORT_EMAIL');
        if ($e instanceof NotFoundHttpException && $request->is('api/*')){
            return sendError("Page Not Found. If error persists, contact {$contactEmail}", [], NOT_FOUND);
        }
        if ($e instanceof ModelNotFoundException && $request->is('api/*')) {
            return sendError("Data not found. If error persists, contact {$contactEmail}", [], NOT_FOUND);
        }
        if ($request->is('api/*')){
            return match ($response->status()) {
                EXPIRED => sendError("Page expired. If error persists, contact {$contactEmail}", [], EXPIRED),
                SERVER_ERROR => sendError("Server error. If error persists, contact {$contactEmail}", [$response->content()], SERVER_ERROR),
                ACCESS_FORBIDDEN => sendError("Access forbidden. If error persists, contact {$contactEmail}", [], ACCESS_FORBIDDEN),
                NOT_AUTHORIZED => sendError("You are not authorized for access this page. If error persists, contact {$contactEmail}", [], NOT_AUTHORIZED),
                BAD_REQUEST => sendError("Bad request check for param. If error persists, contact {$contactEmail}", [], BAD_REQUEST),
                default => $response,
            };
        }
        return $response;

    }
}
