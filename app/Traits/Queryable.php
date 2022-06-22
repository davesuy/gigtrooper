<?php

namespace Gigtrooper\Traits;

trait Queryable
{

    public function gigConvertWoJson($array, $start = ",")
    {
        // Change comma to curly bracket on first to accommodate "uid"
        $str = $start;
        $comma = ",";
        $i = 1;
        $count = count($array);
        foreach ($array as $arrayk => $arrayv) {
            if ($i == $count) {
                $comma = "";
            }
            $str .= $arrayk.":{".$arrayk."}".$comma;

            $i++;
        }
        $str .= "}";

        return $str;
    }

    public function getSaveModelQuery($attributes, $label)
    {
        unset($attributes['id']);

        $inputString = $this->gigConvertWoJson($attributes);

        $queryString = "
            MERGE (id:UniqueId{name:'$label'})
            ON CREATE SET id.count = 1
            ON MATCH SET id.count = id.count + 1
            WITH id.count AS uid
            CREATE (n:$label{id:uid".$inputString.")
            RETURN n";

        return $queryString;
    }

    protected function addQueryString($string, $args = [])
    {
        $this->queryString = $string;
        $this->queryArgs = $args;
    }

}