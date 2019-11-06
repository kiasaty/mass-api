<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            $responseCode = parent::render($request, $exception)->getStatusCode();
        } else {
            $responseCode = $exception->getStatusCode();
        }

        $responseBody = [
            'success' => false,
            'errors' => [
                'status' => $responseCode,
                'source' => [
                    'pointer' => $request->path()
                ]
            ]
        ];
        
        if ($exception instanceof ValidationException) {
            $response['errors']['detail'] = $exception->validator->errors()->getMessages();
        } elseif ($responseCode == 422) {
            $responseBody['errors']['detail'] = json_decode($exception->getMessage());
        } elseif ($responseCode == 404) {
            $responseBody['errors']['detail'] = 'Not Found!';
        } elseif ($responseCode == 401) {
            $responseBody['errors']['detail'] = 'Unauthorized!';
        } else {
            $responseBody['errors']['detail'] = 'Internal Error!';
        }
        
        return response()->json($responseBody, $responseCode);
    }
}
