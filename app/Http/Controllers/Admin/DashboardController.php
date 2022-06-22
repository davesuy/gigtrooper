<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Gigtrooper\Http\Controllers\Controller;

class DashboardController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return view('admin.dashboard');
	}
}