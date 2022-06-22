<?php

namespace Gigtrooper\Http\Controllers;

use Illuminate\Http\Request;
use Gigtrooper\Services\FileUploadHandlerServiceBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RichTextImageController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	public function images(Request $request)
	{
		$dir = $request->get('dir');
		//$dir = str_replace('/', '\\', $dir);
	//	dd($dir);
		$configPrefix = config('filesystems.prefix');

		$path = $configPrefix . $dir;

		$default = config('filesystems.default');

		$disk = Storage::disk('s3');

		if ($default == 's3')
		{
			$files = $disk->allFiles($path);
		}
		else
		{
			$files = Storage::allFiles($path);
		}

		$images = array();

		if (!empty($files))
		{
			foreach ($files as $file)
			{
				if ($this->isThumb($file)) continue;

				if ($default == 's3')
				{
					$url = $disk->url($file);
				}
				else
				{
					$url = '/' . $file;
				}

				$images[] = array(
					'image' => $url
				);
			}
		}

		return response()->json($images)->getContent();
	}

	public function isThumb($file)
	{
		$result = false;

		$dir = dirname($file);

		$segments = explode('/', $dir);

		$end = count($segments) - 1;

		$last = $segments[$end];

		if ($last == 'thumbnail')
		{
			$result = true;
		}

		return $result;
	}
}
