<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Models\Country;
use Gigtrooper\Models\MemberCategory;
use Gigtrooper\Models\User;
use Gigtrooper\Traits\SubField;
use Illuminate\Http\Request;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\UserElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;


class UserController extends Controller
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
            'name', 'slug', 'email', 'password', 'introduction', 'aboutMe', 'Avatar', 'Status', 'Role',
            'contactNumber', 'fee', 'imageGallery', 'facebookUrl', 'youtubeUrl', 'twitterUrl', 'instagramUrl', 'linkedInUrl',
            'memberCategory', 'Country', 'points', 'adminPoints'
        ];

        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @param int $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($page = 1)
    {
        $baseUrl = \Config::get('app.cp').'/users/';
        $currentUrl = $baseUrl.'page/'.$page;
        $filterUrl = $baseUrl.'page/1';

        $limit = 50;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($this->request->input('sort') != null) {
            $options['order'] = $this->request->input('sort');
        } else {
            $options['order'] = ['id-desc', 'adminPoints-desc', 'points-desc'];
        }

        $options['fields'] = [];
        /*
              $options['fields'][0]['handles'][0]['handle'] = "Role";
              $options['fields'][0]['handles'][0]['value']  = "member";
              $options['fields'][0]['relation']             = "AND";

              $options['fields'][0]['handles'][1]['handle'] = "Status";
              $options['fields'][0]['handles'][1]['value']  = ['verified', 'active'];

                $options['fields'][0]['handles'][2]['handle'] = "Country";
                $options['fields'][0]['handles'][2]['value']  = ["PH"];

              $options['fields'][0]['handles'][3]['handle'] = "memberCategory";
              $options['fields'][0]['handles'][3]['value']  = ["singers"];*/


        if ($this->request->input('f') != null) {
            $filterBy = $this->request->input('f');

            $filterByPrepare = \Criteria::prepareFilters($filterBy);

            if (!empty($filterByPrepare)) {
                $options['fields'][0] = $filterByPrepare;
            }
        }

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        $users = \Criteria::find()->all();

        $users = $this->elementsService->getModelsWithFields($users, $fieldTypes);

        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => '\\'.$baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        $sortFields = [];
        $sortFields['id'] = "ID";
        $sortFields['name'] = "Name";
        $sortFields['email'] = "Email";
        $sortFields['Role'] = "Role";
        $sortFields['Status'] = "Status";

        //$sortFields['CREATED']['asc']  = "Date Created Oldest";
        //$sortFields['CREATED']['desc'] = "Date Created Newest";

        //$sortFields['UPDATED']['asc']  = "Date Updated Oldest";
        //$sortFields['UPDATED']['desc'] = "Date Updated Newest";

        $sortBy = ($this->request->input('sort') != null) ? $this->request->input('sort') : [];

        $sorts = [];
        $sortCatValue = (isset($sortBy[0])) ? $sortBy[0] : '';
        $sortElValue = (isset($sortBy[1])) ? $sortBy[1] : '';

        $sorts['category'] = \Criteria::getSortHtml($sortFields, $sortCatValue);
        $sorts['element'] = \Criteria::getSortHtml($sortFields, $sortElValue);

        $filters = \Criteria::getSearchesHtml(['Role', 'Status', 'memberCategory', 'name']);

        $currentUrl = $this->elementsService->getQueryUrl(null, false, $currentUrl);
        $filterUrl = $this->elementsService->getQueryUrl(null, false, $filterUrl);

        $memberCategoryModel = new MemberCategory;

        if ($requestFilter = $this->request->input('f')) {
            if (isset($requestFilter['memberCategory']) AND !empty($memberCategories = $requestFilter['memberCategory'])) {
                $memberCategoryId = (int)$memberCategories[0];

                $memberCategoryModel = MemberCategory::find($memberCategoryId);
            }
        }

        $subsHtmlDisplay = '';

        if ($memberCategoryModel != null) {
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel, $this->element);

            $subsHtmlDisplay = $subsHtml->getSearchSubsHtml();
        }

        return view('admin.user',
            [
                'users' => $users,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'sorts' => $sorts,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'sortBy' => $sortBy,
                'currentUrl' => $currentUrl,
                'filterUrl' => $filterUrl,
                'filters' => $filters,
                'subsHtmlDisplay' => $subsHtmlDisplay
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = \Auth::getUser();

        if (!in_array('superAdmin', $user->roles)) {
            $url = \Config::get('app.cp');

            return redirect($url.'/users')->with('status', 'Super Admin Only');
        }

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        $subsHtmlDisplay = '';

        $memberCategoryModel = new MemberCategory;

        if ($memberCategoryModel != null) {
            if (\Request::old('fields') != null) {
                $oldFields = \Request::old('fields');

                if (isset($oldFields['memberCategory'])) {
                    $memberCategoryModel = MemberCategory::find($oldFields['memberCategory']);
                }
            }
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel, $this->element);

            $subsHtmlDisplay = $subsHtml->getSearchSubsHtml();
        }

        $subsHtmlDisplayRegion = '';

        $countryModel = new Country();

        if (!empty($countryModel)) {
            if (\Request::old('fields') != null) {
                $oldFields = \Request::old('fields');

                if (isset($oldFields['Country'])) {
                    $countryModel = Country::find($oldFields['Country']);
                }
            }

            if ($countryModel) {
                $subFieldService = \App::make('subFieldService');

                $subsHtml = $subFieldService->getSubFieldsHtml($countryModel, $this->element);

                $subsHtmlDisplayRegion = $subsHtml->getDisplaySubsHtml();
            }
        }

        $model = $element->getModel();

        return view('admin.user-create', [
            'element' => $element,
            'model' => $model,
            'countryId' => null,
            'memberCategoryId' => null,
            'subsHtmlDisplay' => $subsHtmlDisplay,
            'subsHtmlDisplayRegion' => $subsHtmlDisplayRegion,
            'jsScripts' => $scripts
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = \Auth::getUser();

        if (!in_array('superAdmin', $user->roles)) {
            $url = \Config::get('app.cp');

            return redirect($url.'/users')->with('status', 'Super Admin Only');
        }

        $subFieldHandles = $this->getCategorySubFieldHandles($request);

        $fieldHandles = array_merge($this->handles, $subFieldHandles);

        array_push($fieldHandles, 'dateCreated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($fieldHandles);

        $fieldTypes['email']['disabled'] = false;

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['dateCreated'] = date('j-M-Y');

        $requestFields = ['fields' => $fields];

        $request->merge($requestFields);

        \Field::setElement($element);

        $result = \Field::processFields($request);

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userId = (int)$id;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        unset($fieldTypes['SubField']);

        $element = $this->element;

        $user = \Auth::getUser();

        if (!in_array('superAdmin', $user->roles)) {

            $roles = $fieldTypes['Role'];
            // Remove admin role
            unset($roles['options'][2]);

            $fieldTypes['Role'] = $roles;
        }

        $element->setFieldTypes($fieldTypes);

       // $element->findModel($userId);

        //$model = $element->getModel();
        $model = User::find($userId);

        $element->setModel($model);
        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        $model->setFieldTypes($fieldTypes);

        $memberCategories = $model->getFieldValue('memberCategory');

        $subsHtmlDisplay = '';

        $memberCategoryId = null;

        if (!empty($memberCategories)) {
            $subFieldService = \App::make('subFieldService');

            $memberCategoryModel = $memberCategories[0];

            $memberCategoryId = $memberCategoryModel->id;

            $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel, $element);

            $subsHtmlDisplay = $subsHtml->getDisplaySubsHtml();
        }

        $subsHtmlDisplayRegion = '';

        $countryModel = $model->getFieldValue('Country');

        if (!empty($countryModel)) {
            $subFieldService = \App::make('subFieldService');

            $subsHtml = $subFieldService->getSubFieldsHtml($countryModel[0], $this->element);

            $subsHtmlDisplayRegion = $subsHtml->getDisplaySubsHtml();
        }

        return view('admin.user-edit', [
            'element' => $element,
            'model' => $model,
            'jsScripts' => $scripts,
            'memberCategoryId' => $memberCategoryId,
            'countryId' => (!empty($countryModel)) ? $countryModel[0]->id : null,
            'subsHtmlDisplay' => $subsHtmlDisplay,
            'subsHtmlDisplayRegion' => $subsHtmlDisplayRegion
        ]);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $userId = (int)$id;

        $subFieldHandles = $this->getCategorySubFieldHandles($request);
        $subFieldHandles[] = 'Avatar';

        $fieldHandles = array_merge($this->handles, $subFieldHandles);

        array_push($fieldHandles, 'dateUpdated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($fieldHandles);

        $element = $this->element;

        $field = $element::getTheFieldByKey('handle', 'email', $fieldTypes);

        $key = array_search($field, $fieldTypes);

        $fieldTypes[$key]['rules'] = 'required|email|unique:User,email,'.$userId;
        //$fieldTypes['name']['rules'] = 'required|unique:User,name,' . $userId;
        $fieldTypes['slug']['rules'] = 'unique:User,slug,'.$userId;

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['dateUpdated'] = date('j-M-Y');

        $userId = (int)$id;

        //$element->findModel($userId);
        $userModel = User::find($userId);

        //$userModel->setFieldTypes($fieldTypes);

        if (empty($fields['slug'])) {
            $userService = \App::make('userService');
            $fields['slug'] = $userService->generateUserSlug($fields['name'], $userId);
        }
        //$fields['slug']    = Str::slug($fields['name']);

        $requestFields = ['fields' => $fields];

        $request->merge($requestFields);

        $element->setModel($userModel);

        \Field::setElement($element);

        $message = 'User has been updated.';

        $result = \Field::processFields($request, $message);

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function actions(Request $request)
    {
        $delete = $request->input('delete');
        if ($delete != null && $delete == 'delete') {
            return $this->deletes($request);
        }
    }

    public function deletes(Request $request)
    {
        $ids = $request->input('ids');

        $result = false;
        if (!empty($ids)) {
            $result = $this->element->deletes($ids);
        }

        if (!empty($result)) {
            return redirect()->back()->with('status', implode(', ', $result).' has been deleted');
        } else {
            return redirect()->back();
        }
    }

    public function sortElements()
    {
        if ($this->request->input('sortBy') != null) {
            $sortBy = $this->request->input('sortBy');

            $currentUrl = $this->request->input('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $url = $this->elementsService->getQueryUrl('sort', $sortBy, $baseUrl, $queryString);
            $url = rawurldecode($url);

            return redirect($url);
        }

        return true;
    }

    public function filterElements()
    {
        $url = \App::make('criteria')->getFilterUrl();

        if ($url) {
            return redirect($url);
        }

        return redirect()->back();
    }

    public function loginAsUser($userId, Request $request)
    {
        if ($userId) {
            \Auth::logout();
            \Auth::loginUsingId($userId);

            return redirect('/account/profile');
        }

        return redirect()->back();
    }

    /**
     * Documentation on how to handle "where options parameters"
     */
    private function testWhereOptions($options)
    {
        $options['fields'][0]['handles'][0]['handle'] = "ROLE";
        $options['fields'][0]['handles'][0]['value'] = "One Level 1";
        $options['fields'][0]['relation'] = "AND";

        $options['fields'][1]['handles'][0]['handle'] = "ROLE";
        $options['fields'][1]['handles'][0]['value'] = "One Level 2";
        $options['fields'][1]['relation'] = "OR";

        $options['fields'][1]['handles'][1]['handles'][0]['handle'] = "SUBHEAD";
        $options['fields'][1]['handles'][1]['handles'][0]['value'] = "Second Level 1";
        $options['fields'][1]['handles'][1]['relation'] = "OR";

        $options['fields'][1]['handles'][1]['handles'][1]['handle'] = "SUBHEAD";
        $options['fields'][1]['handles'][1]['handles'][1]['value'] = "Second Level 2";

        $options['fields'][1]['handles'][1]['handles'][2]['handle'] = "SUBHEAD";
        $options['fields'][1]['handles'][1]['handles'][2]['value'] = "Second Level 3";

        $options['fields'][1]['handles'][1]['handles'][3]['handles'][0]['handle'] = "SKILLS";
        $options['fields'][1]['handles'][1]['handles'][3]['handles'][0]['value'] = "Third Level 1";
        $options['fields'][1]['handles'][1]['handles'][3]['relation'] = "AND";

        $options['fields'][1]['handles'][1]['handles'][3]['handles'][1]['handle'] = "SKILLS";
        $options['fields'][1]['handles'][1]['handles'][3]['handles'][1]['value'] = "Third Level 2";

        return $options;
    }
}
