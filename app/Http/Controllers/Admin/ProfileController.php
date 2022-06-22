<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Models\MemberCategory;
use Gigtrooper\Models\User;
use Gigtrooper\Traits\SubField;
use Illuminate\Http\Request;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\UserElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;

class ProfileController extends Controller
{
    use SubField;

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];

    public function __construct(
        UserElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'name', 'slug', 'email', 'introduction', 'aboutMe', 'memberCategory', 'fee', 'imageGallery', 'Avatar', 'password',
            'contactNumber', 'Country',
            'facebookUrl', 'youtubeUrl', 'twitterUrl', 'instagramUrl', 'linkedInUrl'
        ];

        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $user = \Auth::user();

        $userId = (int)$user->id;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $element->findModel($userId);

        $model = $element->getModel();

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        $model->setFieldTypes($fieldTypes);

        $subsHtmlDisplay = '';

        $memberCategoryModel = $model->getFieldValue('memberCategory');

        if (!empty($memberCategoryModel)) {
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel[0], $this->element);

            $subsHtmlDisplay = $subsHtml->getDisplaySubsHtml();
        }

        $subsHtmlDisplayRegion = '';

        $countryModel = $model->getFieldValueFirst('Country');

        if (!empty($countryModel)) {
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($countryModel, $this->element);

            $subsHtmlDisplayRegion = $subsHtml->getDisplaySubsHtml();
        }

        return view('admin.profile', [
            'element' => $element,
            'model' => $model,
            'jsScripts' => $scripts,
            'memberCategoryId' => (!empty($memberCategoryModel)) ? $memberCategoryModel[0]->id : null,
            'countryId' => (!empty($countryModel)) ? $countryModel->id : null,
            'currency' => (!empty($countryModel)) ? $countryModel->currency : '',
            'subsHtmlDisplay' => $subsHtmlDisplay,
            'subsHtmlDisplayRegion' => $subsHtmlDisplayRegion
        ]);
    }

    public function delete()
    {
        return view('admin.delete');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = \Auth::user();

        $userId = (int)$user->id;

        $subFieldHandles = $this->getCategorySubFieldHandles($request);

        $fieldHandles = array_merge($this->handles, $subFieldHandles);

        array_push($fieldHandles, 'dateUpdated');
        array_push($fieldHandles, 'shareBox');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($fieldHandles);

        $element = $this->element;

        $field = $element::getTheFieldByKey('handle', 'email', $fieldTypes);
        $key = array_search($field, $fieldTypes);

        $fieldTypes[$key]['rules'] = 'required|email|unique:User,email,'.$userId;
        //$fieldTypes['name']['rules'] = 'required|unique:User,name,' . $userId;
        $fieldTypes['name']['rules'] = 'required|max:255';

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['dateUpdated'] = date('j-M-Y');

        $userModel = User::find($userId);

        /*	  if (
                  !empty($fields['name']) &&
                  !empty($fields['Country']) &&
                  !empty($fields['memberCategory']) &&
                  empty($userModel->getAttribute('shareBox'))
                )
              {
                  $fields['shareBox'] = 1;
              }*/

        $element->setModel($userModel);

        if (empty($userModel->getAttribute('slug'))) {
            $userService = \App::make('userService');
            $fields['slug'] = $userService->generateUserSlug($fields['name'], $userModel->id);
        } else {
            $fields['slug'] = $userModel->getAttribute('slug');
        }

        $requestFields = ['fields' => $fields];

        $request->merge($requestFields);

        //$element->findModel($userId);

        \Field::setElement($element);

        $message = 'Your profile has been updated.';

        $result = \Field::processFields($request, $message);

        return $result;
    }

    public function subFields()
    {
        $id = $this->request->get('id');

        $elementId = $this->request->get('elementId');
        $categoryModelName = $this->request->get('modelName');

        $namespace = '\Gigtrooper\\Models\\'.$categoryModelName;
        $categoryModel = new $namespace;

        $this->element->findModel($elementId);

        $memberCategoryModel = $categoryModel::find($id);

        $subsHtmlDisplay = '';

        if (!empty($memberCategoryModel)) {
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel, $this->element);

            $subsHtmlDisplay = $subsHtml->getDisplaySubsHtml();
        }

        echo $subsHtmlDisplay;
        exit;
    }

    public function deleteAccount(Request $request)
    {
        $confirm = $request->get('confirm');

        if ($confirm == 'yes') {
            $user = \Auth::user();

            $userId = (int)$user->id;

            $result = $this->element->deletes([$userId]);

            if ($result) {
                return redirect('/')->with('status', 'Your account has been deleted.');
            } else {
                return redirect()->back();
            }
        } else {
            return redirect('/account/profile');
        }
    }

    public function shareBox(Request $request)
    {
        $userId = $request->get('id');

        $userModel = User::find($userId);
        $userModel->shareBox = 1;
        $userModel->save();
    }
}
