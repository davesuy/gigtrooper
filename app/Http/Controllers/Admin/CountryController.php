<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Elements\CountryElement;
use Gigtrooper\Models\Country;
use Illuminate\Http\Request;

use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;

class CountryController extends Controller
{

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];
    private $extraFields = [];
    private $model;

    public function __construct(
        CountryElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'title', 'countryCode', 'currency', 'SubField'
        ];

        $this->model = new Country();

        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @param int $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($page = 1)
    {
        $baseUrl = \Config::get('app.cp').'/countries/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 50;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($this->request->input('sort') != null) {
            $options['order'] = $this->request->input('sort');
        } else {
            $options['order'] = ['id-desc'];
        }

        $fields = [];

        $options['fields'] = $fields;

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        $elements = \Criteria::find()->all();

        $elements = $this->elementsService->getModelsWithFields($elements, $fieldTypes);

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

        $currentUrl = $this->elementsService->getQueryUrl(null, false, $currentUrl);

        return view('admin.countries',
            [
                'elements' => $elements,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $element->setFieldTypes($fieldTypes);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.countries-create', [
            'element' => $element,
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
        array_push($this->handles, 'Created');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['Created'] = date('j-M-Y');
        $fields['currency'] = $this->getCurrency($fields['countryCode']);

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
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $elemenId = (int)$id;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $model = $this->model->find($elemenId);

        if ($model == null) {
            return redirect(\Config::get('app.cp'));
        }

        $model->setFieldTypes($fieldTypes);

        $user = \Auth::user();

        $roles = $user->roles;

        if (!in_array('administrator', $roles)) {
            return redirect(\Config::get('app.cp').'/countries');
        }

        $element->setFieldTypes($fieldTypes);

        $element->setModel($model);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.countries-edit', [
            'element' => $element,
            'model' => $model,
            'jsScripts' => $scripts
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
        array_push($this->handles, 'Updated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['Updated'] = date('j-M-Y');
        $fields['currency'] = $this->getCurrency($fields['countryCode']);

        $requestFields = ['fields' => $fields];

        $request->merge($requestFields);

        $userId = (int)$id;
        $element->findModel($userId);

        \Field::setElement($element);

        $message = 'Element has been updated.';

        $result = \Field::processFields($request, $message);

        return $result;
    }

    private function getCurrency($code)
    {
        return \App::make('countryService')->getCountryCurrency($code);
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
        if ($this->request->input('filters') != null) {
            $elementFilterBy = $this->request->input('filters');

            $currentUrl = $this->request->input('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $url = $this->elementsService->getQueryUrl('f', $elementFilterBy, $baseUrl, $queryString);

            $url = rawurldecode($url);

            return redirect($url);
        }

        return true;
    }
}
