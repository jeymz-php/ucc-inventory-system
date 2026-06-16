<?php

namespace App\Exceptions;

use App\Models\SystemLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            try {
                if (app()->runningInConsole()) return;
                if ($e instanceof \Illuminate\Validation\ValidationException) return;
                if ($e instanceof \Illuminate\Auth\AuthenticationException) return;

                SystemLog::create([
                    'type'       => 'error',
                    'title'      => class_basename($e),
                    'message'    => substr($e->getMessage(), 0, 500),
                    'url'        => request()->fullUrl(),
                    'method'     => request()->method(),
                    'user_id'    => auth()->id(),
                    'user_role'  => auth()->user()?->role,
                    'ip_address' => request()->ip(),
                ]);
            } catch (\Exception $logEx) {
                // Silently fail to avoid infinite loop
            }
        });
    }
}