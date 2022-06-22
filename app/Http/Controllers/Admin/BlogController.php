<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Facades\FieldFacade;
use Gigtrooper\Models\Post;
use Illuminate\Http\Request;

use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\PostElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];
    private $extraFields = [];

    public function __construct(
        PostElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'title', 'slug', 'subTitle', 'excerpt', 'body', 'Image', 'imageThumbnail',
            'DateTimePublished', 'DateExpiry', 'blogAuthor', 'Category', 'Tag', 'popularPost'
        ];

        $this->extraFields['Status'] = $fieldTypes->getBlogStatus();

        $this->fieldTypes = $fieldTypes;
    }

    private function isBlogger()
    {
        $user = \Auth::user();

        $roles = $user->roles;

        return in_array('blogger', $roles);
    }

    /**
     * @param int $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($page = 1)
    {
        $baseUrl = \Config::get('app.cp').'/blog/';
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
        $user = \Auth::user();

        $roles = $user->roles;

        if (in_array('blogger', $roles)) {
            $fields[0]['handles'][0]['handle'] = "blogAuthor";
            $fields[0]['handles'][0]['value'] = [$user->id];
        }

        $options['fields'] = $fields;

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        $posts = \Criteria::find()->all();

        $postElements = $this->elementsService->getModelsWithFields($posts, $fieldTypes);

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

        $filters = \Criteria::getSearchesHtml(['dateCreated']);

        return view('admin.post',
            [
                'postElements' => $postElements,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl,
                'filters' => $filters
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

        if ($this->isBlogger()) {
            unset($fieldTypes['blogAuthor']);
        }

        $element->setFieldTypes($fieldTypes);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.post-create', [
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
        array_push($this->handles, 'dateCreated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['dateCreated'] = date('j-M-Y');


        if ($this->isBlogger()) {
            $user = \Auth::user();

            $fields['blogAuthor'] = $user->id;
        }

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
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $postId = (int)$id;

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);


        //$element->findModel($postId);

        //$model = $element->getModel();

        $post = new Post();

        $model = $post->find($postId);

        if ($model == null) {
            return redirect(\Config::get('app.cp'));
        }

        $model->setFieldTypes($fieldTypes);

        $isAuthor = \App::make('userService')->isPostAuthor($model);

        $user = \Auth::user();

        $roles = $user->roles;

        if (
            !$isAuthor &&
            !in_array('administrator', $roles) &&
            !in_array('superAdmin', $roles)
        ) {
            return redirect(\Config::get('app.cp').'/blog');
        }

        if ($this->isBlogger()) {
            unset($fieldTypes['blogAuthor']);
        }

        $element->setFieldTypes($fieldTypes);

        $element->setModel($model);

        \Field::setElement($element);

        $scripts = \Field::addFooterJsScripts();

        return view('admin.post-edit', [
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
        array_push($this->handles, 'dateUpdated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $element = $this->element;

        $fieldTypes = array_merge($fieldTypes, $this->extraFields);

        $element->setFieldTypes($fieldTypes);

        $fields = $request->input('fields');

        $fields['dateUpdated'] = date('j-M-Y');


        $requestFields = ['fields' => $fields];

        if (empty($fields['slug'])) {
            $requestFields['fields']['slug'] = Str::slug($fields['title']);
        }
        if ($this->isBlogger()) {
            $user = \Auth::user();

            $requestFields['fields']['blogAuthor'] = $user->id;
        }

        $request->merge($requestFields);

        $userId = (int)$id;
        $element->findModel($userId);

        \Field::setElement($element);

        $message = 'Post has been updated.';

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
}
