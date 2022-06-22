<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Elements\QuoteElement;
use Gigtrooper\Elements\UserElement;
use Gigtrooper\Models\Quote;
use Gigtrooper\Models\User;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Gigtrooper\Services\MessageChainService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class RequestQuoteController extends Controller
{
    use AuthenticatesUsers;

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];
    private $extraFields = [];

    public function __construct(
        QuoteElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'eventType', 'eventDate', 'eventLocation', 'eventStartTime', 'eventStatus',
            'eventServiceLength', 'eventGuests', 'eventDetails', 'dateUpdated'
        ];

        $this->fieldTypes = $fieldTypes;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $this->element->setFieldTypes($fieldTypes);
    }

    public function requestQuote()
    {
        $messageChain = \App::make('messageChainService');

        $deleteQuote = \Request::get('deleteQuote');

        if ($deleteQuote) {
            $quoteModelDelete = new Quote();
            $quoteModelDelete->id = $deleteQuote;
            $messageChain->deleteQuote($quoteModelDelete);
        }

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);
        // Removes title field
        foreach ($fieldTypes as $key => $fieldType) {
            $fieldTypes[$key]['title'] = '';
        }

        $this->element->setFieldTypes($fieldTypes);

        \Field::setElement($this->element);

        $isLoggedIn = \Auth::check();

        $providers = $this->getProviderByIds();

        return view('request-quote', [
            'element'    => $this->element,
            'providers'  => $providers,
            'isLoggedIn' => $isLoggedIn
        ]);
    }

    public function addProvider($memberId = null)
    {
        $memberId = (int) $memberId;

        $session = \Request::session();

        $quote = $session->get('gig-quote') ?? null;

        if ($quote) {
            if (!in_array($memberId, $quote)) {
                $count = count($quote);
                // limit to 10 requests only
                if ($count >= 10) {
                    array_shift($quote);
                    array_push($quote, $memberId);
                    $session->put('gig-quote', $quote);
                } else {
                    $session->push('gig-quote', $memberId);
                }
            }
        } else {
            $session->put('gig-quote', [$memberId]);
        }

        return redirect('request-quote');
    }

    /**
     * @return null
     */
    private function getProviderByIds()
    {
        $session = \Request::session();
        $providers = $session->get('gig-quote');

        $members = null;

        if ($providers) {

            $fieldTypes = $this->fieldTypes->indexByHandle();
            $userElement = new UserElement();
            $model = $userElement->initModel();

            $options['fields'][0]['handles'][0]['handle'] = "id";
            $options['fields'][0]['handles'][0]['value'] = $providers;

            \Criteria::setOptions($model, $options, $fieldTypes);

            $members = \Criteria::find()->all();
            $members = $this->elementsService->getModelsWithFields($members, $fieldTypes);
        }

        return $members;
    }

    public function submit(Request $request)
    {
        $session = \Request::session();
        $providers = $session->get('gig-quote');
        if ($providers == null) {
            return redirect()
                ->back()
                ->with('error', 'Add providers.');
        }

        $fields = $request->input('fields');

        $fields['dateUpdated'] = date('j-M-Y');
        $fields['eventStatus'] = 'active';

        $requestFields = ['fields' => $fields];

        $request->merge($requestFields);

        $fields = $request->input('fields');

        \Field::setElement($this->element);

        $rules = \Field::getFieldRules($fields);

        $friendlyNames = \Field::getFriendlyNames($fields);

        $isLoggedIn = \Auth::check();

        $attemptLogin = false;
        $attemptLoginUser = null;

        if (!$isLoggedIn) {
            $userFields = $request->input('user');
            $attemptLogin = \Auth::attempt($userFields);

            $userRules = [
                'user.name' => 'required',
                'user.contactNumber' => 'required'
            ];

            if ($attemptLogin) {
                $email = $userFields['email'];
                $attemptLoginUser = User::findByAttribute('email', $email);
            } else {
                $userRules['user.email']    = 'required|email|unique:User,email';
                $userRules['user.password'] = 'required|nullable|min:6';
            }

            $rules = array_merge($rules, $userRules);

            $userFriendlyNames = [
                'user.name'  => 'Name',
                'user.email' => 'Email',
                'user.contactNumber' => 'Contact Number',
                'user.password' => 'Password'
            ];

            $friendlyNames = array_merge($friendlyNames, $userFriendlyNames);
        }

        if (!empty($rules)) {
            $validator = \Validator::make($request->all(), $rules);

            $validator->setAttributeNames($friendlyNames);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $quoteModel = null;
        /** @var  $messageChain MessageChainService */
        $messageChain = \App::make('messageChainService');

        if (!$isLoggedIn) {
            if ($attemptLogin == false) {
                $userFields = $request->input('user');

                $fromModel = $this->createUser($userFields);

                \Auth::loginUsingId($fromModel->id);
            } else {
                $fromModel =  $attemptLoginUser;
            }
        } else {
            $user = \Auth::user();

            $fromModel = $user->getModel();
        }

        $quoteModel = $messageChain->sendQuotes($fields, $fromModel);

        return redirect('/account/messages/' . $quoteModel->id)
            ->with('messages', 'Quote Request(s) sent.');
    }

    public function createUser($data)
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles([
            'name', 'email', 'password', 'slug', 'fee', 'email_token', 'Role',
            'Status', 'Country', 'dateCreated', 'contactNumber'
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

        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_token' => str_random(10),
            'Role' => 'member',
            'fee' => ['min' => 0, 'max' => 0],
            'points' => 0,
            'adminPoints' => 0,
            'Status' => 'active',
            'contactNumber' => $data['contactNumber'],
            'dateCreated' => date('j-M-Y'),
            'memberCategory' => $data['register']['memberCategory'] ?? null
        ];

        return \Field::saveElementFields($fields);
    }

    public function removeProvider(Request $request)
    {
        $session = \Request::session();

        $gigQuote = $session->get('gig-quote');
        $memberId = $request->get('id');

        if ($gigQuote && $memberId) {
            $memberId = (int) $memberId;
            $ids = array_flip($gigQuote);
            unset($ids[$memberId]);
            $gigQuote = array_keys($ids);

            $session->put('gig-quote', $gigQuote);
        }
    }
}
