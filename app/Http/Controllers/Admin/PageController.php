<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Facades\FieldFacade;
use Illuminate\Http\Request;

use Gigtrooper\Http\Requests;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\PageElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;

class PageController extends Controller
{

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];

    public function __construct(
        PageElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'title', 'slug', 'subTitle', 'excerpt', 'body', 'Image', 'eventLength'
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
        $baseUrl = \Config::get('app.cp').'/pages/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 50;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($this->request->input('sort') != null) {
            $options['order'] = $this->request->input('sort');
        } else {
            $options['order'] = 'id';
        }

        $options['fields'] = [];

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        $pages = \Criteria::find()->all();

        $pageElements = $this->elementsService->getModelsWithFields($pages, $fieldTypes);

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

        return view('admin.page',
            [
                'pageElements' => $pageElements,
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

        return view('admin.page-create', [
            'element' => $element,
            'jsScripts' => $scripts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return \Field
     */
    public function store(Request $request)
    {
        array_push($this->handles, 'Created');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['Created'] = date('j-M-Y');

        $requestFields = ['fields' => $fields];

        if (empty($fields['slug'])) {
            $requestFields['fields']['slug'] = Str::slug($fields['title']);
        }

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

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);
        $element->findModel($userId);
        $model = $element->getModel();

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.page-edit', [
            'element' => $element,
            'model' => $model,
            'jsScripts' => $scripts
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int     $id
     *
     * @return FieldFacade
     */
    public function update(Request $request, $id)
    {
        $userId = (int)$id;

        array_push($this->handles, 'Updated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['Updated'] = date('j-M-Y');

        $requestFields = ['fields' => $fields];

        if (empty($fields['slug'])) {
            $requestFields['fields']['slug'] = Str::slug($fields['title']);
        }

        $request->merge($requestFields);

        $userId = (int)$id;
        $element->findModel($userId);

        \Field::setElement($element);

        $message = 'Page has been updated.';

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
            $postFilterBy = $this->request->input('filters');

            $currentUrl = $this->request->input('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $url = $this->elementsService->getQueryUrl('f', $postFilterBy, $baseUrl, $queryString);

            $url = rawurldecode($url);

            return redirect($url);
        }

        return true;
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
