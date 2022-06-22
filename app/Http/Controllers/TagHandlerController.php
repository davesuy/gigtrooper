<?php

namespace Gigtrooper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagHandlerController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	public function getTags(Request $request)
	{
		$label    = $request->input('label');
		$name     = $request->input('name');
		$id       = $request->input('id');
		$property = $request->input('property');

		$namespace = '\Gigtrooper\\Models\\Field';
		$fieldModel    = new $namespace;
		$fieldModel->defineLabel($label);
		$id = (int) $id;
		$model = $fieldModel->findField($id);

		$json = array();
		$json['availableTags'] = array();
		$json['assignedTags'] = array();

		$tagModel    = new $namespace;
		$tagModel->defineLabel($name);

		\Criteria::setOptions($tagModel);
		$tags = \Criteria::find()->all();

		if (!empty($tags))
		{
			foreach ($tags as $tag)
			{
				$availableTags[] = $tag->$property;
			}

			$json['availableTags'] = $availableTags;
		}

		if ($model != null)
		{
			$assignedTags = $model->getFieldsArray($name, $property);
			if (!empty($assignedTags))
			{
				$json['assignedTags'] = $assignedTags;
			}
		}

		return response()->json($json)->getContent();
	}
}
