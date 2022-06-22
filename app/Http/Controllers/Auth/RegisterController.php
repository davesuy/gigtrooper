<?php

namespace Gigtrooper\Http\Controllers\Auth;

use Gigtrooper\Elements\UserElement;
use Gigtrooper\Mail\EmailVerification;
use Gigtrooper\Models\Country;
use Gigtrooper\Models\Page;
use Gigtrooper\Models\User;
use Gigtrooper\Services\FieldTypes;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Services\MessageChainService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Gigtrooper\Services\ElementsService;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FieldTypes $fieldTypes = null, ElementsService $elementsService)
    {
        $this->fieldTypes = $fieldTypes;
        $this->elementsService = $elementsService;

        $this->middleware('guest');

        $this->redirectTo = '/account/profile';
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|string|email|max:255|unique:User,email,null,id,status,unverified',
            'password' => 'required|string|min:6|confirmed',
            'register.memberCategory' => 'required',
            'register.countryCode' => 'required'
        ]);

        $validator->setAttributeNames([
            'register.memberCategory' => 'Category',
            'register.countryCode' => 'Country'
        ]);


        return $validator;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function($validator) {

            $validator->errors()->add('email', 'Something is wrong with this field!');
        });
    }

    /**
     *  Over-ridden the register method from the "RegistersUsers" trait
     *  Remember to take care while upgrading laravel
     */

    public function register(Request $request)
    {
        // Laravel validation
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = $this->create($request->all());

        $userVerify = new User();
        $userVerify->email_token = $user->email_token;
        $userVerify->name = $user->name;

        //After creating the user send an email with the random token generated in the create method above
        $email = new EmailVerification($userVerify);

        Mail::to($user->email)->send($email);

        return back()->withInput()->with('status', 'Verification email sent!');
    }

    /**
     * @param array  $data
     * @param string $status
     *
     * @return mixed|null
     */
    protected function create(array $data, $status = 'unverified')
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles([
            'name', 'email', 'password', 'slug', 'fee', 'email_token', 'Role',
            'Status', 'Country', 'memberCategory', 'dateCreated', 'Created', 'points', 'adminPoints'
        ]);
        // Allow runtime email assign
        $fieldTypes['email']['disabled'] = false;

        $userModel = new User();

        $findUser = User::findByAttribute('email', $data['email']);

        if ($findUser) {
            $userModel = $findUser;
        }

        $userElement = new UserElement();
        $userElement->setModel($userModel);
        $userElement->setFieldTypes($fieldTypes);

        \Field::setElement($userElement);

        $countryCode = $data['register']['countryCode'] ?? null;
        $countryModelId = null;

        if ($countryCode) {
            $countryModel = Country::findByAttribute('countryCode', $countryCode);
            $countryModelId = ($countryModel != null)? $countryModel->id : null;
        }

        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_token' => str_random(10),
            'Role' => 'member',
            'fee' => ['min' => 0, 'max' => 0],
            'points' => 0,
            'adminPoints' => 0,
            'Status' => $status,
            'dateCreated' => date('j-M-Y'),
            'Country' => $countryModelId,
            'memberCategory' => $data['register']['memberCategory'] ?? null
        ];

        \Field::validateFields($fields);

        return \Field::saveElementFields($fields);
    }

    public function verify($token)
    {
        $fieldTypesService = \App::make('fieldTypes');
        $fieldTypes = $fieldTypesService->getFieldsByHandles(['Status', 'loginMethod', 'email_token', 'slug']);

        $model = new User();
        $options = [];
        $options['fields'][0]['handles'][0]['handle'] = "email_token";
        $options['fields'][0]['handles'][0]['value'] = $token;
        $options['fields'][0]['relation'] = "AND";
        $options['fields'][1]['handles'][0]['handle'] = "Status";
        $options['fields'][1]['handles'][0]['value'] = "unverified";

        \Criteria::setOptions($model, $options, $fieldTypes);

        $user = \Criteria::find()->first();

        if ($user) {
            $userElement = new UserElement();

            $userElement->setModel($user);
            $userElement->setFieldTypes($fieldTypes);

            \Field::setElement($userElement);

            $userService = \App::make('userService');

            \Field::saveElementFields([
                'Status' => 'active',
                'loginMethod' => 'email',
                'slug' => $userService->generateUserSlug($user->name, $user->id)
            ]);

            return redirect('login')->with('messages', 'Your account has been activated, you can now login');
        } else {
            return redirect('login')->with('error', 'The url is either invalid or you already have activated your account.');
        }
    }

    public function showRegistrationForm()
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles(['title', 'slug', 'subTitle', 'excerpt', 'body']);

        $page = Page::findByAttribute('slug', 'register');

        if ($page == null) {
            return redirect('login');
        }

        $page->setFieldTypes($fieldTypes);

        return view('auth.register', [
            'page' => $page
        ]);
    }
}
