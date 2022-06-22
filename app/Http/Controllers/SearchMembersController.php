<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Helpers\TemplateHelper;
use Gigtrooper\Models\Country;
use Gigtrooper\Models\MemberCategory;
use Gigtrooper\Models\User;
use Gigtrooper\Services\fields\Locality;
use Gigtrooper\Traits\SubField;
use Illuminate\Http\Request;
use Gigtrooper\Elements\UserElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;

class SearchMembersController extends Controller
{
    use SubField;

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];
    private $filterHandles = [];

    public function __construct(
        UserElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;

        $this->filterHandles = ['Country', 'phProvince', 'fee'];

        $this->handles = [
            'name', 'email', 'aboutMe', 'Avatar', 'contactNumber', 'Created', 'Updated',
            'facebookUrl', 'twitterUrl', 'instagramUrl', 'linkedInUrl', 'memberCategory'
        ];

        $this->handles = array_merge($this->handles, $this->filterHandles);

        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @param int $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($countryCode = null, $category = null, $region = null, $page = 1)
    {
        $countryCode = strtoupper($countryCode);

        $regionUrl = ($region) ? $region : 'all';

        $baseUrl = "search/members/$countryCode/$category/$regionUrl/";

        $currentCountry = \App::make('countryService')->getCountryNameByCode($countryCode);

        if ($currentCountry == null) {
            $currentCountry = "All";
        }

        $limit = 25;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        $sortParams = null;
        if ($this->request->input('sort') != null) {
            $sortParams = $this->request->input('sort');

            $options['order'] = $sortParams;
        } else {
            $options['order'] = ['adminPoints-desc', 'points-desc', 'id-desc'];
        }

        $options['fields'] = [];

        $handles = [];
        $handles[0]['handle'] = "Role";
        $handles[0]['value'] = "member";

        $options['fields'][0]['handles'] = $handles;
        $options['fields'][0]['relation'] = "AND";

        // When sorting fee the member with no price does not display
        if ($sortParams) {
            if (isset($sortParams[1])) {
                $minLow['min']['low'] = 1;
                $feeSort = [
                    'handle' => "fee",
                    'value' => $minLow
                ];

                array_push($handles, $feeSort);
            }
        }

        $statuses = [
            'handle' => "Status",
            'value' => ['verified', 'active']
        ];

        array_push($handles, $statuses);

        if ($countryCode != 'ALL') {
            $country = [
                'handle' => "Country",
                'value' => [$countryCode]
            ];

            array_push($handles, $country);
        }

        $memberCategories = [];

        $subsHtmlDisplay = '';
        $memberCategoryModel = null;
        $currentMemberCategory = 'All';
        if (strtoupper($category) != 'ALL') {
            $memberCategory = [
                'handle' => "memberCategory",
                'value' => [$category]
            ];

            array_push($handles, $memberCategory);

            $categoryService = \App::make('categoryService');

            $memberCategoryModel = MemberCategory::findByAttribute('slug', $category);

            $memberCategories = $categoryService->getRelatedCategories([$memberCategoryModel]);

            if ($memberCategoryModel != null) {
                $subFieldService = \App::make('subFieldService');

                $subsHtml = $subFieldService->getSubFieldsHtml($memberCategoryModel, $this->element);

                $subsHtmlDisplay = $subsHtml->getSearchSubsHtml();

                $currentMemberCategory = $memberCategoryModel->title;
            }
        } else {
            $memberCategory = [
                'handle' => "memberCategory",
                'value' => "*"
            ];

            array_push($handles, $memberCategory);
        }

        $countryModel = Country::findByAttribute('countryCode', $countryCode);

        $subsHtmlDisplayRegion = '';

        /**
         * @var $subFieldService \Gigtrooper\Services\SubField
         */
        $subFieldService = \App::make('subFieldService');

        $stateValues = [];

        if (!empty($countryModel)) {
            $subsHtml = $subFieldService->getSubFieldsHtml($countryModel, $this->element);

            $subsHtmlDisplayRegion = $subsHtml->getSearchSubsHtml();

            $stateValues = $subsHtml->getValues();

            $stateHandles = array_keys($stateValues);
            if (is_array($stateHandles)) {
                $stateHandleFirst = $stateHandles[0];

                if ($region && $region != 'all') {
                    $regionCriteria = [
                        'handle' => $stateHandleFirst,
                        'value' => $region
                    ];

                    array_push($handles, $regionCriteria);
                }
            }
        }

        $options['fields'][0]['handles'] = $handles;

        if ($this->request->input('f') != null) {
            $filterBy = $this->request->input('f');

            $filterByPrepare = \Criteria::prepareFilters($filterBy);

            if (!empty($filterByPrepare)) {
                array_push($options['fields'], $filterByPrepare);
            }
        }

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $model = $this->element->initModel();

        \Criteria::setOptions($model, $options, $fieldTypes);

        $members = \Criteria::find()->all();

        $members = $this->elementsService->getModelsWithFields($members, $fieldTypes);

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

        $sortFields['dateCreated']['asc'] = "Date Registered Oldest";
        $sortFields['dateCreated']['desc'] = "Date Registered Newest";

        $sortFieldsFee['fee']['min']['asc'] = "Min Fee From Low to High";
        $sortFieldsFee['fee']['min']['desc'] = "Min Fee From High to Low";

        $sortFieldsFee['fee']['max']['asc'] = "Max Fee From Low to High";
        $sortFieldsFee['fee']['max']['desc'] = "Max Fee From High to Low";


        $sortBy = ($this->request->input('sort') != null) ? $this->request->input('sort') : [];

        $sorts = [];
        $sortRegistered = (isset($sortBy[0])) ? $sortBy[0] : '';
        $sortFee = (isset($sortBy[1])) ? $sortBy[1] : '';

        $sorts['registered'] = \Criteria::getSortHtml($sortFields, $sortRegistered);
        $sorts['fee'] = \Criteria::getSortRangeHtml($sortFieldsFee, $sortFee);

        $filters = \Criteria::getSearchesHtml(['Country', 'fee']);

        if (!empty($members)) {
            foreach ($members as $member) {
                $country = $member->getFieldValueFirst('Country');

                if (!$country) {
                    continue;
                }

                $element = new UserElement();

                $element->setModel($member);

                $subFields = $subFieldService->getSubFieldsHtml($country, $element);

                $countrySubFields = $subFields->getValues();

                if (!empty($countrySubFields)) {
                    $member->countrySubFields = $countrySubFields;
                }
            }
        }

        $currentProvince = 'All';

        if (!empty($stateValues)) {
            $stateHandle = key($stateValues);

            $stateValues = $stateValues[$stateHandle];

            if ($this->request->segment(5) != null) {
                $segmentValue = $this->request->segment(5);

                $currentProvince = TemplateHelper::convertToTitle($segmentValue);

                if ($currentProvince == 'all') {
                    $currentProvince = 'All';
                }
            }
        }

        $currentFee = '';

        if ($this->request->input("f.fee")) {
            $fee = $this->request->input("f.fee");
            $currentFee = '<br/>';
            $minimum = [];
            if (isset($fee['min']['low'])) {
                $minimum[] = $fee['min']['low']."&darr;";
            }

            if (isset($fee['min']['high'])) {
                $minimum[] = $fee['min']['high']."&uarr;";
            }

            if (!empty($minimum)) {
                $currentFee .= "<strong>Min:</strong> ".implode(" - ", $minimum);
            }
            $max = [];
            if (isset($fee['max']['low'])) {
                $max[] = $fee['max']['low']."&darr;";
            }

            if (isset($fee['max']['high'])) {
                $max[] = $fee['max']['high']."&uarr;";
            }

            if (!empty($max) && !empty($minimum)) {
                $currentFee .= "<br />";
            }

            if (!empty($max)) {
                $currentFee .= "<strong>Max:</strong> ".implode(" - ", $max);
            }
        }

        return view('members.search-members',
            [
                'currentFee' => $currentFee,
                'currentCountry' => $currentCountry,
                'stateValues' => $stateValues,
                'currentProvince' => $currentProvince,
                'currentMemberCategory' => $currentMemberCategory,
                'countryCode' => $countryCode,
                'members' => $members,
                'memberCategories' => $memberCategories,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'sorts' => $sorts,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'sortBy' => $sortBy,
                'filters' => $filters,
                'filterHandles' => array_flip($this->filterHandles),
                'subsHtmlDisplay' => $subsHtmlDisplay,
                'subsHtmlDisplayRegion' => $subsHtmlDisplayRegion,
                'memberCategoryModel' => $memberCategoryModel
            ]
        );
    }

    /**
     * @param $categorySlug
     * @param $memberSlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($categorySlug, $memberSlug)
    {
        $member = User::findByAttribute('slug', $memberSlug);

        $this->element->setModel($member);

        $fieldTypes = $this->fieldTypes->indexByHandle();

        $member->setFieldTypes($fieldTypes);

        $country = $member->getFieldValueFirst('Country');

        $countrySubFields = [];

        $subFieldService = \App::make('subFieldService');

        if (!empty($country)) {
            $subFields = $subFieldService->getSubFieldsHtml($country, $this->element);

            $countrySubFields = $subFields->getValues();
        }

        if (!empty($countrySubFields)) {
            $member->countrySubFields = $countrySubFields;
        }

        $memberCategory = $member->getFieldValueFirst('memberCategory');

        $subFields = $subFieldService->getSubFieldsHtml($memberCategory, $this->element);

        $memberCategorySubFields = $subFields->getValues();

        if (!empty($memberCategorySubFields)) {
            $member->memberCategorySubFields = $memberCategorySubFields;
        }

        return view('members.view', [
            'member' => $member,
            'country' => ($country) ? $country : ''
        ]);
    }

    public function sortElements()
    {
        if ($this->request->input('sortBy') != null) {
            $sortBy = $this->request->input('sortBy');

            if (empty($sortBy)) {
                $currentUrl = $this->request->input('currentUrl');

                $urlSegment = explode("?", $currentUrl);

                $baseUrl = $urlSegment[0];

                $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

                $query = [];

                if ($queryString) {
                    parse_str($queryString, $query);

                    unset($query['sort']);

                    $queryStringUrl = '';

                    if (!empty($query)) {
                        $queryStringUrl = '?'.http_build_query($query);

                        $queryStringUrl = rawurldecode($queryStringUrl);
                    }

                    $url = $baseUrl.$queryStringUrl;

                    return redirect($url);
                }

                return redirect()->back();
            }

            $currentUrl = $this->request->input('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            if (!empty($sortBy)) {
                foreach ($sortBy as $sortByKey => $sortByValue) {
                    if ($sortByValue == null) {
                        unset($sortBy[$sortByKey]);
                    }
                }
            }

            $url = $this->elementsService->getQueryUrl('sort', $sortBy, $baseUrl, $queryString);

            $url = rawurldecode($url);

            return redirect($url);
        }

        return redirect()->back();
    }

    public function filterElements()
    {
        $url = \App::make('criteria')->getFilterUrl();

        if ($url) {
            return redirect($url);
        }

        return redirect()->back();
    }

    public function filterRegion()
    {
        $region = \Request::input('filters');

        $keys = array_keys($region);

        if ($keys) {
            $handle = $keys[0];

            $regionValue = $region[$handle];

            $value = '/'.$regionValue;

            if ($regionValue == '*') {
                $value = '';
            }

            $regionUrl = \Request::input('baseUrl').$value;

            $url = \App::make('criteria')->getFilterUrl($regionUrl, $handle);

            return redirect($url);
        }

        return redirect()->back();
    }

    public function changeCountry()
    {
        $currentUrl = $this->request->input('currentUrl');
        $code = $this->request->input('filters.countryCode');

        $parts = explode('/', $currentUrl);

        $parts[3] = $code;

        $currentUrl = implode('/', $parts);

        return redirect($currentUrl);
    }
}
