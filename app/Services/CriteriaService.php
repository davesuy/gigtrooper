<?php

namespace Gigtrooper\Services;

use Gigtrooper\Traits\Convertable;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Traits\Defautltable;
use Illuminate\Support\Str;

class CriteriaService
{
    use Convertable, Defautltable;

    protected $model;
    protected $modelLabel;
    protected $options = [];
    private $query = '';
    private $match = '';
    private $skip = 0;
    private $limit = null;
    private $return = '';
    public $currentPage;
    private $where = '';
    private $order = '';
    private $matchesTax = [];
    private $returnTax = [];
    private $returnOrder = [];
    private $orderBy = [];

    private $dateMatchesTax = [];
    private $dateReturnTax = [];

    private $wheresCql = '';
    private $wheresCqlDate = '';
    private $whereValues = [];
    private $total = 0;
    private $withCql = '';
    private $fieldTypes;
    private $elementModels;

    public function setOptions(BaseModel $model, $options = [], $fieldTypes = [])
    {
        $this->initPropertyValues();

        $this->model = $model;
        $this->modelLabel = $model->getLabel();
        $this->options = $options;
        $this->fieldTypes = array_merge($fieldTypes, $this->initDefaultfieldTypes());

        $this->initQuery();

        if (isset($options['limit'])) {
            $this->limit = $options['limit'];
        }

        if (isset($options['page'])) {
            $page = $options['page'];
            $offset = $page - 1;
            $start = $offset * $this->limit;
            $this->skip = $start;
            $this->currentPage = $page;
        }

        if (isset($options['skip'])) {
            $this->skip = $options['skip'];
        }

        if (isset($options['order']) && is_array($options['order'])) {
            foreach ($options['order'] as $order) {
                $parts = explode('-', $order);

                if (count($parts) > 2) {
                    $order = $parts;
                }

                $this->getOrderQuery($order);
            }
        }

        if (!empty($options['fields'])) {
            $fieldCounter = 1;

            foreach ($options['fields'] as $field) {
                $this->getTaxesQuery($field);

                if (count($options['fields']) != $fieldCounter) {
                    $this->wheresCql .= " ".$field['relation']." ";
                }
                $fieldCounter++;
            }
        }

        if (!empty($options['with'])) {
            foreach ($options['with'] as $handle) {
                $field = $this->getFieldByHandle($handle, $this->fieldTypes);

                $fieldClass = \Field::getFieldClass($field);

                $matchesTaxString = $fieldClass->getMatchesTax();

                if (!empty($matchesTaxString)) {
                    $this->matchesTax[$handle] = $matchesTaxString;
                }

                $returnTaxString = $fieldClass->getReturnTax();

                if (!empty($returnTaxString)) {
                    $this->returnTax[$handle] = $returnTaxString;
                }
            }
        }

        if (!empty($options['withDates'])) {
            foreach ($options['withDates'] as $relationship) {
                $smallRelationship = strtolower($relationship);
                $upRelationship = strtoupper($relationship);
                $this->dateMatchesTax[$smallRelationship] = "(element)<-[:$upRelationship]-({$smallRelationship}xdate:Date)<--
                ({$smallRelationship}xtime)";

                $this->dateReturnTax[$smallRelationship] = "COLLECT(DISTINCT({$smallRelationship}xtime.value)) AS {$smallRelationship}xtime,
                {$smallRelationship}xdate.value AS {$smallRelationship}xtimestamp";
            }
        }

        $matchString = (!empty($this->matchesTax)) ? implode(', ', $this->matchesTax) : $this->match;

        $dateMatchString = (!empty($this->dateMatchesTax)) ? ', '.implode(', ', $this->dateMatchesTax) : '';

        $this->match = $matchString.$dateMatchString;
        $this->where .= $this->wheresCql;

        $returnString = (!empty($this->returnTax)) ? ", ".implode(', ', $this->returnTax) : '';

        $returnOrderString = (!empty($this->returnOrder)) ? ", ".implode(', ', $this->returnOrder) : '';

        $this->order = (!empty($this->orderBy)) ? implode(', ', $this->orderBy) : '';

        $dateReturnString = (!empty($this->dateReturnTax)) ? ", ".implode(', ', $this->dateReturnTax) : '';

        $this->return .= $returnString.$returnOrderString.$dateReturnString;

        if (!empty($options['date'])) {
            $this->filterByDate($options['date']);
        }
    }

    private function initPropertyValues()
    {
        $this->matchesTax = [];
        $this->returnTax = [];
        $this->returnOrder = [];
        $this->orderBy = [];
        $this->dateMatchesTax = [];
        $this->dateMatchesTax = [];
        $this->dateReturnTax = [];
        $this->query = '';
        $this->match = '';
        $this->where = '';
        $this->wheresCql = '';
        $this->wheresCqlDate = '';
        $this->whereValues = [];
        $this->withCql = '';
        $this->return = '';
        $this->total = 0;
        $this->skip = 0;
        $this->order = '';
    }

    public function getModelLabel()
    {
        return $this->modelLabel;
    }

    private function getTaxesQuery($field = [])
    {
        $relation = (isset($field['relation'])) ? $field['relation'] : "";

        foreach ($field['handles'] as $handleKey => $handleField) {
            if (array_key_exists('relation', $handleField)) {
                $this->getTaxesQuery($handleField);

                $this->wheresCql .= " ) ";
            } else {
                $endKey = count($field['handles']) - 1;

                $open = ($handleKey == 0) ? " ( " : "";

                $close = ($handleKey == $endKey) ? " ) " : "";

                $relationWhere = ($handleKey != $endKey) ? $relation : "";

                $operatorValues = $this->getOperatorValuesByHandle($handleField);

                $this->wheresCql .= "$open $operatorValues $relationWhere $close";
            }
        }
    }

    public function getOrderQuery($handle)
    {
        if (is_array($handle)) {
            $orderValue = $handle;

            $order = $handle[0];
        } else {
            $orderValue = 'ASC';

            if ($order = $this->isDesc($handle)) {
                $orderValue = 'DESC';
            } else {
                $order = $handle;
            }
        }

        if (!empty($this->fieldTypes)) {
            $field = $this->getFieldByHandle($order, $this->fieldTypes);

            $fieldClass = \Field::getFieldClass($field);

            $matchesTaxString = $fieldClass->getMatchesTax();

            if (!empty($matchesTaxString)) {
                $this->matchesTax[$order] = $matchesTaxString;
            }

            $orderTaxString = $fieldClass->getOrderTax();

            if (!empty($orderTaxString)) {
                $this->returnOrder[] = $orderTaxString;
            }
        }

        $orderBy = $fieldClass->getOrderQuery($orderValue);

        if (!empty($orderBy)) {
            $this->orderBy[] = $orderBy;
        }
    }

    public function initQuery()
    {
        $label = $this->modelLabel;

        $this->match = "(element:".$label.")";

        $this->return = "element, COUNT(element) AS number";
    }

    public function isDesc($order)
    {
        $arr = explode('-', $order);
        $lastKey = count($arr) - 1;
        $lastArr = $arr[$lastKey];

        if ($lastArr == 'desc') {
            return $arr[0];
        } else {
            return false;
        }
    }

    public function theQuery()
    {
        $return = (!empty($this->return)) ? "RETURN ".$this->return : "";

        $limit = ($this->limit != null) ? "LIMIT ".$this->limit."\n" : '';

        $order = (!empty($this->order)) ? "ORDER BY ".$this->order."\n" : '';

        $this->query =
            $this->matchWhereQuery().
            $this->withCql.
            $return."\n".
            $order."\n".
            "SKIP ".$this->skip."\n".
            $limit;
         //echo nl2br($this->query);

        return $this->query;
    }

    private function matchWhereQuery()
    {
        $where = (!empty($this->where)) ? "\n WHERE  ".$this->where : '';

        $label = $this->modelLabel;

        $element = "(element:".$label.")";

        $matches = array_merge($this->matchesTax, $this->dateMatchesTax);

        $optionalMatches = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $optionalMatches[] = ",".$match;
            }
        }

        $withDateKeys = [];

        if (!empty($this->dateMatchesTax)) {
            foreach (array_keys($this->dateMatchesTax) as $dateHandle) {
                $withDateKeys[] = $dateHandle.'xtime';
                $withDateKeys[] = $dateHandle.'xdate';
            }
        }

        //$withMatches = array_keys($this->matchesTax);

        //$withHandles = array_merge($withMatches, $withDateKeys);

        //$withWhere = (!empty($matches)) ? "\n WITH element, " . implode(',', $withHandles) : "";

        $query =
            "MATCH $element \n".
            implode(" \n", $optionalMatches).
            //$withWhere .
            "$where \n";

        return $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function find()
    {
        $nodes = $this->getNodesByQuery();

        $elementModels = [];
        $model = $this->model;
        if (!empty($nodes)) {
            foreach ($nodes as $key => $node) {
                $properties = $node['element']->getProperties();

                $elementModel = $model::populateModel($properties);
                //$elementModel = $model->setAttributes($properties);

                foreach ($node as $handle => $property) {
                    if (!$this->isHandleReserved($handle)) {
                        // Set from row data to array to avoid miscalculation
                        //$value = $this->convertRowToArray($property);

                        $elementModel->setField($handle, $property);
                    }
                }
                //dd($elementModel->getAllFields());
                $elementModels[] = $elementModel;
            }
        }

        $this->elementModels = $elementModels;

        return $this;
    }

    public function all()
    {
        return $this->elementModels;
    }

    public function first()
    {
        $first = [];

        if (!empty($this->elementModels)) {
            return $this->elementModels[0];
        }

        return $first;
    }

    public function reservedHandles()
    {
        return [
            'element', 'id', 'order', 'number', "time", "timestamp"
        ];
    }

    public function isHandleReserved($handle)
    {
        if (in_array($handle, $this->reservedHandles()) || strpos($handle, "xtimestamp") || strpos($handle, "xtime")) {
            return true;
        }

        return false;
    }

    public function getNodesByQuery()
    {
        $queryString = $this->theQuery();

        //echo $queryString .'<br />';
        //dd($this->whereValues);
        $results = \Neo4jQuery::getResultSet($queryString, $this->whereValues);

        $nodes = [];

        $columns = $results->getColumns();

        if (empty($columns)) {
            return;
        }

        if ($results->count()) {
            $counter = 0;

            foreach ($results as $key => $row) {
                foreach ($columns as $column) {
                    $nodes[$counter][$column] = $row[$column];
                }
                $counter++;
            }

            return $nodes;
        }

        return $nodes;
    }

    public function getTotal()
    {
        $queryString = $this->matchWhereQuery()." RETURN COUNT(DISTINCT element) as total";

        $results = \Neo4jQuery::getResultSet($queryString, $this->whereValues);

        if ($results) {
            $this->total = $results[0]['element'];
            return $this->total;
        } else {
            return "0";
        }
    }

    public function convertArrayCql($array)
    {
        $fieldValues = [];
        foreach ($array as $val) {
            $fieldValues[] = "'$val'";
        }

        return implode(", ", $fieldValues);
    }

    public function filterByDate($date)
    {
        if (!isset($date['relationship'])) {
            throw new \Exception("Filter Date relationship is required.");
        }

        $relationship = $date['relationship'];

        $smallRelationship = strtolower($relationship);
        $upRelationship = strtoupper($relationship);

        $whereDates = [];

        if (isset($date['year'])) {
            $year = $date['year'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:YEAR_OF]-({value: '$year'})";
        }
        if (isset($date['month'])) {
            $month = $date['month'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:MONTH_OF]-({value: '$month'})";
        }

        if (isset($date['day'])) {
            $day = $date['day'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:DAY_OF]-({value: '$day'})";
        }

        $this->dateMatchesTax[$smallRelationship] = "(element)<-[:$upRelationship]-({$smallRelationship}xdate:Date)<--
                ({$smallRelationship}xtime)";

        $this->dateReturnTax[$relationship] = "COLLECT(DISTINCT({$smallRelationship}xtime.value)) AS {$smallRelationship}xtime,
                {$smallRelationship}xdate.value AS {$smallRelationship}xtimestamp";

        $dateAnd = (!empty($this->wheresCql)) ? "AND " : "";
        $this->wheresCqlDate .= $dateAnd.implode(" AND ", $whereDates);

        $this->where .= $this->wheresCqlDate;
    }

    public function getOperatorValuesByHandle($handleField)
    {
        $operatorValues = "";

        $handle = $handleField['handle'];
        $value = $handleField['value'];
        $operator = (!empty($handleField['operator'])) ? $handleField['operator'] : "=";

        $field = $this->getFieldByHandle($handle, $this->fieldTypes);

        $fieldClass = \Field::getFieldClass($field);

        $whereKey = $handle.'Where';
        $valueKey = "{{$whereKey}}";

        $whereValue = $fieldClass->prepareWhereValue($handle, $value);

        $this->whereValues = array_merge($this->whereValues, $whereValue);

        $value = $fieldClass->getOperatorValues($value, $valueKey, $operator);

        if (!empty($this->fieldTypes)) {
            $matchString = $fieldClass->getMatchesTax();

            if (!empty($matchString)) {
                $this->matchesTax[$handle] = $matchString;
            }

            $whereCql = $fieldClass->getWhereCql($value);

            if (!empty($whereCql)) {
                $operatorValues .= $whereCql;
            }
        } else {
            $operatorValues .= "element.$handle $value";
        }

        return $operatorValues;
    }

    public function prepareFilters($filterBy, $relation = "AND")
    {
        $options = [];

        if (is_array($filterBy)) {
            $options['relation'] = $relation;

            $counter = 0;
            foreach ($filterBy as $handle => $value) {
                // Range slider and dropdown ignore all
                if ($value == '*' || $value == '0 - 0') {
                    continue;
                }

                // Do not include count holder for checkbox
                if (is_array($value)) {
                    unset($value['x']);
                }

                $options['handles'][$counter]['handle'] = $handle;
                $options['handles'][$counter]['value'] = $value;

                $counter++;
            }
        }

        if (empty($options['handles'])) {
            $options = [];
        }

        return $options;
    }


    public function prepareSorts(array $sorts)
    {
        $options = [];

        if (!empty($sorts)) {
            foreach ($sorts as $sort => $label) {
                if (is_array($label)) {
                    foreach ($label as $key => $value) {
                        if ($key == "desc") {
                            $sort = $sort."-desc";
                        }
                        $options[$sort]['value'] = $sort;
                        $options[$sort]['label'] = $value;
                    }
                } else {
                    $sortDesc = $sort."-desc";
                    $options[$sort]['value'] = $sort;
                    $options[$sort]['label'] = $label;

                    $options[$sortDesc]['value'] = $sortDesc;
                    $options[$sortDesc]['label'] = $label." DESC";
                }
            }
        }

        return $options;
    }

    public function prepareRangeSorts(array $sorts)
    {
        $options = [];

        if (!empty($sorts)) {
            foreach ($sorts as $prefix => $values) {
                if (is_array($values)) {
                    foreach ($values as $key => $val) {
                        foreach ($val as $k => $v) {
                            $sort = $prefix."-$key-$k";

                            $options[$sort]['value'] = $sort;
                            $options[$sort]['label'] = $v;
                        }
                    }
                }
            }
        }

        return $options;
    }

    public function getSortHtml(array $handles, $sortValue)
    {
        $sorts = $this->prepareSorts($handles);

        $default = [];
        $default['---']['value'] = null;
        $default['---']['label'] = '---';

        $sorts = array_merge($default, $sorts);

        return view('partials.sort', [
            'sorts' => $sorts,
            'sortValue' => $sortValue
        ]);
    }

    public function getSortRangeHtml(array $handles, $sortValue)
    {
        $sorts = $this->prepareRangeSorts($handles);

        $default = [];
        $default['---']['value'] = null;
        $default['---']['label'] = '---';

        $sorts = array_merge($default, $sorts);

        return view('partials.sort', [
            'sorts' => $sorts,
            'sortValue' => $sortValue
        ]);
    }

    public function getSearchesHtml($handles = [])
    {
        $fieldTypes = $this->fieldTypes;

        $contents = [];

        if (!empty($handles)) {
            foreach ($handles as $handle) {
                if (isset($fieldTypes[$handle])) {
                    $field = $fieldTypes[$handle];

                    $fieldClass = \Field::getFieldClass($field);
                    $contents[$handle] = $fieldClass->getSearchHtml();
                }
            }
        }

        return $contents;
    }

    public function getFilterUrl($regionUrl = null, $handle = null)
    {
        $url = '/';

        if (\Request::input('filters') != null) {
            $postFilterBy = \Request::input('filters');

            if ($handle) {
                unset($postFilterBy[$handle]);
            }

            $currentUrl = \Request::input('currentUrl');

            $urlSegment = explode("?", $currentUrl);

            $baseUrl = $urlSegment[0];

            if ($regionUrl) {
                $baseUrl = $regionUrl;
            }

            $queryString = (isset($urlSegment[1])) ? $urlSegment[1] : null;

            $query = [];

            parse_str($queryString, $query);

            if (isset($query['f']) && !empty($query['f'])) {
                $postFilterBy = array_merge($query['f'], $postFilterBy);
            }
            foreach ($postFilterBy as $key => $value) {
                // Is array is for checkbox made sure to remove the none selected
                if (is_array($value)) {
                    if (in_array('*', $value) OR (count($value) == 1 AND isset($value['x']))) {
                        unset($postFilterBy[$key]);
                    }
                }

                if ($value == "*" OR $value == '0 - 0') {
                    unset($postFilterBy[$key]);
                }
            }

            $url = \App::make('elementsService')->getQueryUrl('f', $postFilterBy, $baseUrl, $queryString);

            $url = rawurldecode($url);
        }

        return $url;
    }

    public function findByAttributeQuery($key, $value, $element)
    {
        $label = $element->getLabel();

        $queryString = "MATCH (n:$label)
                        WHERE n.$key = {findValue}
                        RETURN n";

        $result = \Neo4jQuery::getResultSet($queryString, ['findValue' => $value], true);

        $populate = null;

        if ($result->count()) {
            $node = $result[0]['n'];
            $nameSpace = get_class($element);
            $model = new $nameSpace;

            $populate = $this->populate($node, $model);
        }

        return $populate;
    }

    public function populate($node, $element)
    {
        $properties = null;

        if ($node != null)
        {
            $properties = $node->getProperties();

            if (!empty($properties))
            {
                foreach ($properties as $propk => $prop)
                {
                    $element->setAttribute($propk, $prop);
                }
            }

            return $element;
        }
        else
        {
            throw new \Exception("Call the query method first.");
        }
    }
}