<?php

namespace Gigtrooper\Http\Controllers\Auth;

use Gigtrooper\Elements\UserElement;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Models\User;
use Gigtrooper\Services\MessageChainService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Socialite;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Response
     */
    public function redirectToProvider()
    {
        /**
         * @var $socialite \Laravel\Socialite\Facades\Socialite;
         */
        $socialite = Socialite::driver('facebook')->fields([
            'first_name', 'last_name', 'email', 'gender', 'birthday', 'location'
        ]);

        $this->putSession('page.redirect', 'redirect');
        $this->putSession('member.categoryId', 'categoryId');

        return $socialite->redirect();
    }

    private function putSession($name, $request)
    {
        $categoryId = \Request::session()->get($name);

        if ($categoryId) {
            \Request::session()->forget($name);
        }

        $idValue = \Request::get($request);

        if ($idValue != null) {
            \Request::session()->put($name, $idValue);
        }
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Response|bool
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->fields([
            'first_name', 'last_name', 'email', 'gender', 'birthday', 'location'
        ])->user();

        if ($user->email == null) {
            return redirect('/register')->with('error', 'We cannot register you because your Facebook does not have an email.');
        }

        $redirectUrl = \Request::session()->get('page.redirect') ?? "/account/profile" ;

        $firstName = (isset($user->user['first_name'])) ? $user->user['first_name'] : '';
        $lastName = (isset($user->user['last_name'])) ? $user->user['last_name'] : '';

        $name = $firstName.' '.$lastName;
        $email = $user->email;

        $findUser = User::findByAttribute('email', $email);

        if ($findUser) {
            Auth::login($findUser);

            return redirect($redirectUrl);
        }

        $fieldTypes = \App::make('fieldTypes');

        $fieldTypes = $fieldTypes->getFieldsByHandles([
            'name', 'email', 'Role', 'slug', 'fee',
            'Status', 'loginMethod', 'dateCreated', 'memberCategory',
            'points', 'adminPoints'
        ]);
        // Allow runtime email assign
        $fieldTypes['email']['disabled'] = false;

        $userElement = new UserElement();
        $userElement->setFieldTypes($fieldTypes);

        \Field::setElement($userElement);

        $fields = [
            'name' => $name,
            'email' => $email,
            'Role' => 'member',
            'Status' => 'active',
            'slug' => '',
            'fee' => ['min' => 0, 'max' => 0],
            'points' => 0,
            'adminPoints' => 0,
            'loginMethod' => 'facebook',
            'dateCreated' => date('j-M-Y')
        ];

        $categoryId = \Request::session()->get('member.categoryId');

        if ($categoryId != null) {
            $fields['memberCategory'] = $categoryId;
        }

        $user = \Field::saveElementFields($fields);

        if ($user) {
            Auth::login($user);

            return redirect($redirectUrl);
        }

        return false;
    }

    public function loginQuote(Request $request)
    {
        $this->redirectTo = '/account/messages';

        return $this->login($request);
    }

    public function redirectPath()
    {
        $url = \Session::pull('login.previous');

        if ($url) {
            return $url;
        }

        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
