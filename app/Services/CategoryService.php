<?php

namespace Gigtrooper\Services;

use Gigtrooper\Fields\OptionData;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\Category;
use Gigtrooper\Models\MemberCategory;

class CategoryService
{
    private $depth = 1;
    private $levels = 5;
    private $element = null;
    private $showTotal = false;
    private $filterHandles = [];
    private $categoryModel;
    private $status = [];

    public function getTree($model)
    {
        $levels = $this->levels;
        // --(element) only get categories with connected element
        $elementLabel = ($this->element != null) ? '-->(element:'.$this->element->getLabel().')' : '';

        $filterQuery = '';
        $matchQuery = '';

        if (!empty($status = $this->status)) {
            $status = $this->status;
            $matchQuery .= "<-[:STATUS_OF]-(status)";

            array_walk($status, [$this, "addApos"]);

            $filterQuery .= " AND status.value IN [".implode(',', $status).']';
        }

        $label = $model->getLabel();
        $queryString = "
    MATCH path=(root:$label)-[:PARENT_OF*0..$levels]->(descendent:$label)$elementLabel".$matchQuery."
    WHERE NOT(()-[:PARENT_OF]->(root)) $filterQuery
    RETURN path
    ";
        //dump($queryString);
        $totals = [];
        if ($this->element != null && $this->showTotal == true) {
            $queryStringTotal = "MATCH (root:$label)-[:PARENT_OF*0..5]->(descendent:$label)$elementLabel
        RETURN	root, COUNT(element) AS total";

            $resultTotals = \Neo4jQuery::getResultSet($queryStringTotal);

            if ($resultTotals->count()) {
                foreach ($resultTotals as $resultTotal) {
                    $totals[$resultTotal['root']->id] = $resultTotal['total'];
                }
            }
        }

        $results = \Neo4jQuery::getResultSet($queryString);
        //echo $queryString; exit;
        $info = [];
        $paths = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $names = [];

                foreach ($result['path'] as $node) {
                    $nodeLabels = [];

                    foreach ($node->getLabels() as $nodeLabel) {
                        $nodeLabels[] = $nodeLabel->getName();
                    }

                    if (!in_array($label, $nodeLabels)) {
                        continue;
                    }

                    $names[] = $node->id;

                    $properties = $node->getProperties();

                    if (!empty($properties)) {
                        foreach ($properties as $key => $value) {
                            $info[$node->id][$key] = $value;
                        }
                        if ($this->element != null && !empty($totals) && $this->showTotal == true) {
                            $info[$node->id]['total'] = $totals[$node->id];
                        }
                    }
                }

                $segment = implode("/", $names);
                $paths[$segment] = $segment;
            }
        }

        $tree = $this->explodeTree($paths, "/", true);

        return $this->plotTree($tree, $info);
    }

    public function addApos(&$value)
    {
        $value = "'".$value."'";
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setLevels($levels)
    {
        $this->levels = $levels;
    }

    public function setElement(BaseModel $model)
    {
        $this->element = $model;
    }

    public function showTotal($bool)
    {
        $this->showTotal = $bool;
    }

    public function explodeTree($array, $delimiter = '_', $baseval = false)
    {
        if (!is_array($array)) {
            return false;
        }
        $splitRE = '/'.preg_quote($delimiter, '/').'/';
        $returnArr = [];
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $parentArr = &$returnArr;

            foreach ($parts as $part) {

                if (!isset($parentArr[$part])) {
                    $parentArr[$part] = [];
                    if ($baseval) {
                        $parentArr[$part]['__base_val'] = $part;
                    } else {
                        $parentArr[$part] = [];
                    }
                } elseif (!is_array($parentArr[$part])) {

                    if ($baseval) {

                        $parentArr[$part] = ['__base_val' => $parentArr[$part]];
                    } else {
                        $parentArr[$part] = [];
                    }
                }

                $parentArr = &$parentArr[$part];
            }

            // Add the final part to the structure
            if (empty($parentArr[$leafPart])) {
                $parentArr[$leafPart] = $val;
            } elseif ($baseval && is_array($parentArr[$leafPart])) {
                $parentArr[$leafPart]['__base_val'] = $val;
            }
        }
        return $returnArr;
    }

    public function plotTree($arr, array $info)
    {
        $branch = [];

        foreach ($arr as $k => $v) {
            // skip the baseval thingy. Not a real node.
            if ($k == "__base_val") {
                continue;
            }

            $b = [];

            $b = $info[$k];
            if (is_array($v)) {
                // this is what makes it recursive, rerun for childs
                $this->plotTree($v, $info);

                $children = $this->plotTree($v, $info);

                if ($children) {
                    $b['children'] = $children;
                }
            }

            $branch[$k] = $b;
        }

        return $branch;
    }

    public function menu($branch, $base = '')
    {
        $html = "<ul class='multi'>";
        foreach ($branch as $val) {
            $id = $val['id'];
            $url = "/$base"."$id/edit";
            if (!empty($val['children'])) {
                $html .= "<li>
            <div> 
            <input type='checkbox' name='ids[]' class='categoryelect elementCheckbox no-space' value='$id' />
            
             <a class='s-title' href='$url'>".$val['title']."</a></div>";
                $html .= $this->menu($val['children'], $base);
                $html .= "</li>";
            } else {
                $html .= "<li>
                    <div>  
                    <input type='checkbox' name='ids[]' class='categoryelect elementCheckbox no-space' value='$id' />
                    
                     <a class='s-title' href='$url'>".$val['title']."</a>
                     </div>
                    </li>";
            }
        }

        $html .= "</ul>";

        return $html;
    }

    public function searchMenu($branch, $params)
    {
        $html = "<ul class='multi'>";
        foreach ($branch as $val) {
            $id = $val['id'];
            $url = "/".$params['baseUrl']."$id/edit";

            $selected = (in_array($id, $params['requestValues'])) ? 'checked' : '';
            $title = (!empty($val['title'])) ? $val['title'] : '';
            if (!empty($val['children'])) {
                $html .= "<li>
            <div>
            <input type='checkbox' $selected name='filters[".$params['handle']."][]' class='categorySelect elementCheckbox no-space' value='$id' />
             <a href='$url'>".$title."</a></div>";
                $html .= $this->searchMenu($val['children'], $params);
                $html .= "</li>";
            } else {
                $html .= "<li>
                    <div>
                    <input type='checkbox' $selected name='filters[".$params['handle']."][]' class='categorySelect elementCheckbox no-space' value='$id' />
                     <a href='$url'>".$title."</a></div>
                    </li>";
            }
        }

        $html .= "</ul>";

        return $html;
    }

    public function getModelsNoChild(BaseModel $model)
    {
        $label = $model->getLabel();

        $nodes = \Neo4jCriteria::getNodesNoChild($label);

        $models = [];

        if (!empty($nodes)) {
            $models = $model::populateModels($nodes);
        }

        return $models;
    }

    public function getChildCategories($label, $id)
    {
        $queryString = "
        MATCH (f:$label)<-[:PARENT_OF*1..5]-(t:$label)
        WHERE t.id = $id
        RETURN f
    ";

        $results = \Neo4jQuery::getResultSet($queryString);

        $ids = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $ids[] = $result['f']->id;
            }
        }

        return $ids;
    }

    public function getParentCategories($label, $id)
    {
        $queryString = "
        MATCH (f:$label)<-[:PARENT_OF*1..5]-(t:$label)
        WHERE f.id = $id
        RETURN t
    ";

        $results = \Neo4jQuery::getResultSet($queryString);

        $cats = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $model = Category::populateModel($result['t']->getProperties());

                $cats[] = $model;
            }
        }

        return $cats;
    }

    public function getBranch(BaseModel $model = null)
    {
        if ($model == null) {
            $model = new Category();
        }

        $this->categoryModel = $model;

        $branch = $this->getTree($model);

        return $branch;
    }

    public function getCategoryMenu($branch, $base = '', $cssClass = '')
    {
        $html = "<ul class='category-menu $cssClass'>";
        foreach ($branch as $val) {
            $slug = (isset($val['slug'])) ? $val['slug'] : '';

            $url = $base.$slug;

            if (!empty($val['children'])) {
                $html .= "<li>				
             <a class='s-title' href='$url'>".$val['title']."</a>";
                $html .= $this->getCategoryMenu($val['children'], $base);
                $html .= "</li>";
            } else {
                $html .= "<li>						
                     <a class='s-title' href='$url'>".$val['title']."</a>
                    </li>";
            }
        }

        $html .= "</ul>";

        return $html;
    }

    public function getSelectDropdown($branch)
    {
        $html = "<div class='menu'>";
        foreach ($branch as $val) {
            if (!empty($val['children'])) {
                $html .= "<div class='item' data-value='".$val['id']."'><i class='pull-right glyphicon glyphicon-chevron-right'></i>"
                    .'<span class="text">'.$val['title'].'</span>';
                $html .= $this->getSelectDropdown($val['children']);
                $html .= "</div>";
            } else {
                $html .= "<div class='item' data-value='".$val['id']."'><span class='text'>".$val['title']."</span></div>";
            }
        }

        $html .= "</div>";

        return $html;
    }

    public function getCategorySideMenu($branch, $base = '', $categories = [])
    {
        $class = '';
        if ($this->depth == 1) {
            $class = 'check side-category-menu categories-filter ';
        }

        $html = "<ul class='category-menu $class'>";

        foreach ($branch as $val) {
            $slug = (isset($val['slug'])) ? $val['slug'] : '';

            $url = $base.$slug;
            $active = (in_array($val['slug'], $categories)) ? "class='active'" : "";

            if (!empty($val['children'])) {
                $html .= "<li $active>				
             <a href='$url'>".$val['title']."</a>";
                $html .= $this->getCategorySideMenu($val['children'], $base, $categories);
                $html .= "</li>";
            } else {
                $html .= "<li $active>						
                     <a href='$url'>".$val['title']."</a>
                    </li>";
            }
            $this->depth++;
        }

        $html .= "</ul>";

        return $html;
    }

    public function getCategoryNestedMenu($branch, $base = '', $categories = [], $slug = false)
    {
        $class = '';
        if ($this->depth == 1) {
            $class = 'check side-category-menu categories-filter ';
        }

        $open = ($slug == true) ? "style='display: block'" : '';

        $html = "<ul class='inner category-menu $class' $open>";

        foreach ($branch as $val) {
            $slug = (isset($val['slug'])) ? $val['slug'] : '';

            $queryString = $this->getFilterUrl($slug);

            $region = (\Request::segment(5)) ? '/'.\Request::segment(5) : '';

            $url = $base.$slug.$region.$queryString;

            $active = '';

            if (in_array($val['slug'], $categories)) {
                $active = "class='active'";
                $slug = true;
                $icon = 'minus';
            } else {
                $slug = false;
                $icon = 'plus';
            }

            $total = (isset($val['total'])) ? '<span>('.$val['total'].')</span>' : '';

            if (!empty($val['children'])) {
                $html .= "<li $active>				
             <a href='$url'>".$val['title']."</a> $total <a class='toggle'><i class=\"glyphicon 
             glyphicon-$icon\"></i></a>";
                $html .= $this->getCategoryNestedMenu($val['children'], $base, $categories, $slug);
                $html .= "</li>";
            } else {
                $html .= "<li $active>						
                     <a href='$url'>".$val['title']."</a>  $total
                    </li>";
            }
            $this->depth++;
        }

        $html .= "</ul>";

        return $html;
    }

    public function getFilterUrl($slug)
    {
        $requestQuery = \Request::getQueryString();

        $query = [];

        parse_str($requestQuery, $query);

        if (!empty($query['f'])) {
            $subFieldService = \App::make('subFieldService');

            $categoryModel = MemberCategory::findByAttribute('slug', $slug);

            $subHandles = [];
            $filters = [];

            if ($categoryModel) {
                $subHandles = $subFieldService->getSubFieldsHandles($categoryModel);

                if (!empty($subHandles)) {
                    $filterHandles = array_merge($this->filterHandles, array_flip($subHandles));

                    $filters = array_intersect_key($query['f'], $filterHandles);
                }
            }

            if ($categoryModel == null OR empty($subHandles)) {
                $filters = array_intersect_key($query['f'], $this->filterHandles);
            }

            $query['f'] = $filters;
        }

        $queryString = '';

        if (!empty($query)) {
            $queryString = '?'.http_build_query($query);

            $queryString = rawurldecode($queryString);
        }

        return $queryString;
    }

    public function setFilterHandles($handles)
    {
        $this->filterHandles = $handles;
    }

    public function getRelatedCategories($fieldCategories)
    {
        $categoryService = \App::make('categoryService');

        $categories = [];

        if ($fieldCategories) {
            foreach ($fieldCategories as $fieldCategory) {
                $label = $fieldCategory->getLabel();

                $id = $fieldCategory->id;

                $parentCategories = $categoryService->getParentCategories($label, $id);

                if (!empty($parentCategories)) {
                    foreach ($parentCategories as $parentCategory) {
                        $categories[] = $parentCategory->slug;
                    }
                }

                $categories[] = $fieldCategory->slug;
            }
        }

        return $categories;
    }

    public function getCategorySearchDropdown($initModel)
    {
        $fieldTypes = \App::make('fieldTypes')->getFieldsByHandles(['title']);

        \Criteria::setOptions($initModel, ['limit' => 0, 'order' => ['title']], $fieldTypes);

        $models = \Criteria::find()->all();

        $options = $this->getOptionData($models);

        return view('fields.search-dropdown', [
            'options' => $options
        ]);
    }

    protected function getOptionData($models)
    {
        $options = [];

        if (!empty($models)) {
            foreach ($models as $model) {
                $selected = false;

                $val = new OptionData($model->title, $model->id, $selected);

                $options[] = $val;
            }
        }

        return $options;
    }

    public function getHomeMemberCategories()
    {
        $handles = ['title', 'slug', 'memberCategoryImage', 'points'];

        $options['limit'] = 8;
        $options['order'] = ['points'];

        $model = new MemberCategory();

        $fieldTypes = \App::make('fieldTypes')->getFieldsByHandles($handles);

        \Criteria::setOptions($model, $options, $fieldTypes);

        $cats = \Criteria::find()->all();

        return \App::make('elementsService')->getModelsWithFields($cats, $fieldTypes);
    }
}