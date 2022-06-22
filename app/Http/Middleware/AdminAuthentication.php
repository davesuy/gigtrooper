<?php namespace Gigtrooper\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Closure;
use Gigtrooper\Models\User;

class AdminAuthentication
{

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            $user = $this->auth->user();

            $roles = $user->roles;

            $status = $user->status;

            if (
            (!empty($roles) && in_array('administrator', $roles)
                || !empty($roles) && in_array('superAdmin', $roles))
                && !empty($roles) && in_array('active', $status)
            ) {
                return $next($request);
            }
        }

        return new RedirectResponse(url('/login'));
    }
}
