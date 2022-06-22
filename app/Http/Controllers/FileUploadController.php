<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Services\BaseS3UploadHandlerService;
use Gigtrooper\Services\S3FileUploadHandlerService;
use Illuminate\Http\Request;
use Gigtrooper\Services\FileUploadHandlerService;
use Illuminate\Support\Facades\Auth;

class FileUploadController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function upload(Request $request)
	{
		$options = array();

		$options['fileuploadHandle'] = $request->input('fileuploadHandle');
		$options['fileuploadId']     = $request->input('fileuploadId');
		$options['fileuploadLabel']  = $request->input('fileuploadLabel');

		$handle          = $options['fileuploadHandle'];
		$fileuploadId    = $options['fileuploadId'];
		$fileuploadLabel = strtolower($options['fileuploadLabel']);

		//$options['upload_dir'] = storage_path("app/public/images/$fileuploadLabel/$fileuploadId/$handle/");
		//$options['upload_url'] = url("images/$fileuploadLabel/$fileuploadId") . "/$handle/";

		$default = config('filesystems.default');

		if ($default == 's3')
		{
			$uploadHandler = new S3FileUploadHandlerService($options);
		}
		else
		{
			$uploadHandler = new FileUploadHandlerService($options);
		}
	}
}
