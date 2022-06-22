<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Models\Page;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Http\Request;

use Mail;
use Session;
use Validator;
use ReCaptcha\ReCaptcha;

class ContactFormController extends Controller
{
	private $fieldTypes;
	private $elementsService;

	public function __construct(FieldTypes $fieldTypes = null, ElementsService $elementsService)
	{
		$this->fieldTypes      = $fieldTypes;
		$this->elementsService = $elementsService;
	}

	public function index()
	{
		$fieldTypes = $this->fieldTypes->getFieldsByHandles(['title', 'slug', 'subTitle', 'excerpt', 'body']);

		$page = Page::findByAttribute('slug', 'contact-us');

		if ($page == null)
		{
			return redirect('login');
		}

		$page->setFieldTypes($fieldTypes);

		return view(env('CONTACT_FORM_VIEW', 'contact'), [
			'page' => $page
		]);
	}

	public function post(Request $request)
	{
		// get the data and its validation rules
		$data = [
			'email' => $request->input('email'),
			'name' => $request->input('name'),
			'message' => $request->input('message')];
		$rules = [
			'email' => 'bail|required|email',
			'message' => 'required'];

		// build the validator
		$validator = Validator::make($data, $rules);

		// verify the reCAPTCHA with Google
		$recaptcha = new ReCaptcha(env('RECAPTCHA_SECRET'));
		$recaptcha_resp = $recaptcha->verify($request->input('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);

		// validate the request
		$recaptcha_failed = $recaptcha_resp->isSuccess() == FALSE;
		$validator_failed = $validator->fails();

		// if the reCAPTCHA failed then add a message to the validator
		if ($recaptcha_resp->isSuccess() == FALSE) {
			$validator->errors()->add('recaptcha', 'Prove you are not a robot.');
		}

		// if the validation failed then redirect back to the register page
		if ($recaptcha_failed || $validator_failed) {
			return redirect(route('contact.get'))->withErrors($validator)->withInput();
		}

		// send the email to the webmaster
		$email = $data['email'];
		$name =  (!empty($data['name']))? $data['name'] : 'No name given';

		Mail::raw($data['message'], function ($m) use ($email, $name) {
			// @todo temporary for sparkpost mail to work
			$m->from(env('MAIL_FROM_ADDRESS'), $name);
			$m->to(env('CONTACT_FORM_EMAIL'), env('CONTACT_FORM_NAME'));
			$m->subject(env('CONTACT_FORM_SUBJECT', 'Message received from user: ' . $email ));
		});

		// flash a success message back to the contact page
		Session::flash('success', env('CONTACT_FORM_SUCCESS', 'Your message has been sent'));
		return redirect(route('contact.get'));
	}
}
