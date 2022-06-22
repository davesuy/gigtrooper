<?php namespace Gigtrooper\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Closure;

class BloggerAuthentication
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
                !empty($roles) && (in_array('administrator', $roles) OR in_array('superAdmin', $roles) OR in_array('blogger', $roles))
                && !empty($roles) && in_array('active', $status) OR in_array('verified', $status)
            ) {
                return $next($request);
            }
        }

        return new RedirectResponse(url('/'));
    }
}
