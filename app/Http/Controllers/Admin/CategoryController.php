<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Services\SubField;
use Illuminate\Http\Request;

use Gigtrooper\Http\Requests;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\CategoryElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\CategoryService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $criteria;
    private $fieldTypes;
    private $handles = [];

    public function __construct(
        CategoryElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypesService = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = ['title', 'slug', 'Category'];
        $this->fieldTypes = $fieldTypesService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($page = 1)
    {
        $baseUrl = \Config::get('app.cp').'/categories/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 50;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;


        $order = '';

        $options = [];
        $limit = 25;
        $options['page'] = $page;

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        \Criteria::find()->all();

        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => $baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        $currentUrl = $this->elementsService->getQueryUrl(null, false, $currentUrl);

        $service = \App::make('categoryService');
        $categories = $service->getModelsNoChild($model);

        $tree = $service->getTree($model);

        return view('admin.category',
            [
                'categories' => $categories,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'order' => $order,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl,
                'menuTree' => $service->menu($tree, $baseUrl)
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;
        $element->setFieldTypes($fieldTypes);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.category-create', [
            'element' => $element,
            'jsScripts' => $scripts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $handles = array_keys($request->input('fields'));

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        \Field::setElement($element);

        $requestFields = $this->addSlugToFields();

        $request->merge($requestFields);

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
        $elementId = (int)$id;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $element->findModel($elementId);

        $model = $element->getModel();

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        $subFieldService = new SubField();
        $subsHtml = $subFieldService->getSubFieldsHtml($model, $element);

        return view('admin.category-edit', [
            'element' => $element,
            'model' => $model,
            'subsHtml' => $subsHtml,
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
        $handles = array_keys($request->input('fields'));

        //Put back Empty fields
        $handles = array_merge($handles, $this->handles);
        $fieldTypes = $this->fieldTypes->getFieldsByHandles($handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $categoryId = (int)$id;
        $element->findModel($categoryId);

        \Field::setElement($element);

        $message = 'Category has been updated.';

        $requestFields = $this->addSlugToFields();

        $request->merge($requestFields);

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
            return redirect()->back()->with('status', 'Nothing has been deleted');
        }
    }

    public function sortElements()
    {
        if ($this->request->input('sortBy') != null) {
            $sortBy = $this->request->input('sortBy');

            $currentUrl = \Session::get('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $url = $this->elementsService->getQueryUrl('sortBy', $sortBy, $baseUrl, $queryString);

            return redirect($url);
        }
    }

    public function filterElements()
    {
        if ($this->request->input('filters') != null) {
            $postFilterBy = $this->request->input('filters');

            $currentUrl = \Session::get('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $url = $this->elementsService->getQueryUrl('filterBy', $postFilterBy, $baseUrl, $queryString);

            return redirect($url);
        }
    }

    private function addSlugToFields()
    {
        $fields = $this->request->input('fields');

        $requestFields = ['fields' => $fields];

        if (empty($fields['slug'])) {
            $requestFields['fields']['slug'] = Str::slug($fields['title']);
        }

        return $requestFields;
    }
}
