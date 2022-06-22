<?php namespace Gigtrooper\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Closure;

class MemberAuthentication
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
                !empty($roles) && (in_array('administrator', $roles) OR in_array('superAdmin', $roles) OR in_array('blogger', $roles) OR
                    in_array('member', $roles)) && !empty($roles) && (in_array('active', $status) OR in_array('verified',
                        $status))
            ) {
                return $next($request);
            }
        }

        \Session::flash('status', 'You need to login first.');

        \Session::put('login.previous', url()->current());

        return new RedirectResponse(url('/login'));
    }
}
