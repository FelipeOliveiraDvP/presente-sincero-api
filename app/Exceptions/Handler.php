<?php

namespace App\Exceptions;

use BadMethodCallException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
        $this->renderable(function (BindingResolutionException $e, $request) {
            return response()->json([
                'message' => 'Recurso não encontrado!',
                'error' => $e->getMessage()
            ], 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'message' => 'Método http não permitido para esse recurso!',
                'error' => $e->getMessage()
            ], 405);
        });

        $this->renderable(function (BadMethodCallException $e, $request) {
            return response()->json([
                'message' => 'O método chamado não existe',
                'error' => $e->getMessage()
            ], 500);
        });
    }
}
