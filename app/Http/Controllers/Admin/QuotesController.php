<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Elements\QuoteElement;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Traits\QuoteAble;
use Illuminate\Http\Request;
use Gigtrooper\Services\FieldTypes;

class QuotesController extends Controller
{
    use QuoteAble;

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];

    public function __construct(
        QuoteElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'eventStatus'
        ];

        $this->fieldTypes = $fieldTypes;
    }

    public function index($page = 1)
    {
        $baseUrl = \Config::get('app.cp').'/quotes/';
        $limit = 25;
        $messageChain = \App::make('messageChainService');
        $eventStatus = null;

        $filter = \Request::get('f');
        if ($filter && $filter['eventStatus']) {
            $eventStatus = $filter['eventStatus'];
        }

        $messages = $messageChain->getQuotes($eventStatus, $page, $limit);
        $fieldTypesService = \App::make('fieldTypes');

        $eventStatusHandle = $fieldTypesService->getFieldByHandle('eventStatus');

        $fieldClass = \Field::getFieldClass($eventStatusHandle);

        $search = $fieldClass->getSearchHtml();

        $total = $messageChain->getQuotesTotal($eventStatus);

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => '\\'.$baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        return view('admin.quotes', [
            'messages' => $messages,
            'search' => $search,
            'page' => $page,
            'total' => $total,
            'pagination' => $pagination
        ]);
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

        $deletedIds = [];
        $result = false;
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $id = (int) $id;
                $query = "MATCH (n:Quote{id:{id}})--(m:Message)
                          DETACH DELETE n, m";
                $result = \Neo4jQuery::getResultSet($query, ['id' => $id]);

                if ($result) {
                    $deletedIds[] = $id;
                }
            }
        }

        if (!empty($result)) {
            return redirect()->back()
                ->with('status', 'Quotes ' . implode(', ', $deletedIds) . ' has been deleted');
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request, $id)
    {
        $element = $this->getElement($id);
        \Field::setElement($element);

        $result = \Field::processFields($request);

        return $result;
    }

    public function filter(Request $request)
    {
//        $fields = $request->input('filters');
//
//        $currentUrl = '/' . \Config::get('app.cp') . '/quotes';
//
//        $url = $currentUrl . '?'.http_build_query($fields);
//        if ($url) {
//            return redirect($url);
//        }
//
//        return redirect()->back();
        $url = \App::make('criteria')->getFilterUrl();

        if ($url) {
            return redirect($url);
        }

        return redirect()->back();
    }
}
