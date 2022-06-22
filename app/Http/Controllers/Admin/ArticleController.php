<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Gigtrooper\Http\Requests;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\ArticleElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Models\Article;

class ArticleController extends Controller
{
	protected $criteria;
	private $baseUrl = '';

	public function __construct(ArticleElement $element, Request $request,
	                            ElementsService $elementService)
	{
		$this->element  = $element;
		$this->request  = $request;
		$this->elementsService = $elementService;
	}

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index($page = 1)
  {
		$category = array();
		$elementKeys = array();

		$total = 50;
		$order = '';

	  $this->baseUrl =  \Config::get('app.cp') . '/article/';

	  $baseUrl = $this->baseUrl;

	  $currentPageUrl = $baseUrl . 'page/' . $page;
	  $filterPageUrl  = $baseUrl . 'page/1';



		$options = array();
	  $limit = 25;
	//	$options['limit']  = $limit;
		$options['page']   = $page;

	  if ($this->request->input('sortBy') != null)
	  {
		  $options['order'] = $this->request->input('sortBy');
	  }
	  else
	  {
		  $options['order']  = 'id';
	  }

	  if ($this->request->input('filterBy') != null)
	  {
		  $filterBy = $this->request->input('filterBy');

		  $options['fields'] = array();

		  if (is_array($filterBy))
		  {
			  $options['fields'][0]['relation'] = "AND";

				$counter = 0;
				foreach ($filterBy as $handle => $value)
				{
					$options['fields'][0]['handles'][$counter]['handle'] = $handle;
					$options['fields'][0]['handles'][$counter]['value']  = $value;

					$counter++;
				}
		  }
	  }

	  //$options['with'][0] = 'CATEGORY';
	  //$options['withDates'] = array('CREATED', 'UPDATED');
	  //$options['withDates'] = array('CREATED');

	  $fieldTypes = $this->element->getFieldTypes();

	  $model = $this->element->initModel();
	  \Criteria::setOptions($model, $options, $fieldTypes);

		$elements = \Criteria::find();

		$query = \Criteria::getQuery();
		$total = \Criteria::getTotal();

	  $paginationArgs = array(
		  'total'       => $total,
		  'limit'       => $limit,
		  'base'        => '\\' . $baseUrl,
		  'currentPage' => $page
	  );

	  \Pagination::setArgs($paginationArgs);

	  $pagination = \Pagination::render();

	  $sortFields = array();
	  $sortFields['id']       = "ID";
	  $sortFields['name']     = "Title";

	  $sortFields['createdxdate']['asc']  = "Date Created Oldest";
	  $sortFields['createdxdate']['desc'] = "Date Created Newest";

	  $sortFields['updatedxdate']['asc']  = "Date Updated Oldest";
	  $sortFields['updatedxdate']['desc'] = "Date Updated Newest";


	  $sorts = \Criteria::prepareSorts($sortFields);
		$sortBy = ($this->request->input('sortBy') != null) ? $this->request->input('sortBy') : '';

	  $currentPageUrl = $this->elementsService->getQueryUrl(null, false, $currentPageUrl);
	  $filterPageUrl  = $this->elementsService->getQueryUrl(null, false, $filterPageUrl);

	  $filterFields = array();

	  $filterFields['category']['label']  = "Category";
	  $filterFields['category']['options'][0]['value'] = 'catone';
	  $filterFields['category']['options'][0]['label'] = 'Cat One';

	  $filterFields['category']['options'][1]['value'] = 'cat two';
	  $filterFields['category']['options'][1]['label'] = 'Cat Two';

		//dd($filterFields);

	  $filterBy = ($this->request->input('filterBy') != null) ? $this->request->input('filterBy') : null;


		return view('admin.article',
										array(
											'elements' => $elements,
											'elementKeys'   => $elementKeys,
											'page'       => $page,
											'total'      => $total,
											'pagination' => $pagination,
											'sorts'      => $sorts,
											'order'      => $order,
											'baseUrl'    => $baseUrl,
											'query'		   => $query,
											'sortBy'     => $sortBy,
											'filterBy'   => $filterBy,
											'currentUrl' => $currentPageUrl,
											'filterUrl' => $filterPageUrl,
											'filterFields' => $filterFields
										)
								);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
		$element = $this->element;

		\Field::setIsFresh(true);
		\Field::setElement($element);
		\Field::setSection('default');
		\Field::setCategory('all');

	  $scripts = \Field::addFooterJsScripts();

    return view('admin.article-create',[
			'element' => $element,
			'jsScripts' => $scripts
		]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  Request  $request
   * @return Response
   */
  public function store(Request $request)
  {
		$fieldTypes = $this->element->fieldTypes();

		$element = $this->element;

		$element->setFieldTypes($fieldTypes);

		\Field::setElement($element);

		$result = \Field::processFields($request);

		return $result;
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {

  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    $elementId = (int) $id;

    $fieldTypes = $this->element->fieldTypes();
    $element = $this->element;

    $element->setFieldTypes($fieldTypes);

    $element->findModel($elementId);

    $model = $element->getModel();

    \Field::setElement($element);
    \Field::setSection('default');
    \Field::setCategory('all');

    $currentUrl = $this->request->url();

    $scripts = \Field::addFooterJsScripts();

    return view('admin.article-edit',[
        'element' => $element,
        'model'   => $model,
        'jsScripts' => $scripts
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  Request  $request
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request, $id)
  {
		$fieldTypes = $this->element->fieldTypes();
		$element = $this->element;

		$element->setFieldTypes($fieldTypes);

		$modelId = (int) $id;
		$element->findModel($modelId);

		\Field::setElement($element);

	  $message = 'Article has been updated.';

		$result = \Field::processFields($request, $message);

		return $result;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
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
		if(!empty($ids))
		{
			$result = $this->element->deletes($ids);
		}

		if(!empty($result))
		{
			return redirect()->back()->with('status', implode(', ', $result) . ' has been deleted');
		}
		else
		{
			return redirect()->back()->with('status', 'Nothing has been deleted');
		}
	}

	public function sortElements()
	{
		if ($this->request->input('sortBy') != null)
		{
			$sortBy = $this->request->input('sortBy');

			$currentUrl = $this->request->input('currentUrl');

			$urlSegment = explode("?", $currentUrl);

			$baseUrl = $urlSegment[0];

			$queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

			$url = $this->elementsService->getQueryUrl('sortBy', $sortBy, $baseUrl, $queryString);

			return redirect($url);
		}
	}

	public function filterElements()
	{
		if ($this->request->input('filters') != null)
		{
			$postFilterBy = $this->request->input('filters');

			$currentUrl = $this->request->input('currentUrl');

			$urlSegment = explode("?", $currentUrl);

			$baseUrl = $urlSegment[0];

			$queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

			$url = $this->elementsService->getQueryUrl('filterBy', $postFilterBy, $baseUrl, $queryString);

			return redirect($url);
		}
	}
}
