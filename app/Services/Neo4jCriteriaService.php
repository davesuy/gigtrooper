<?php

namespace Gigtrooper\Services;


class Neo4jCriteriaService
{
    public function getFieldNodes($fromLabel, $fid, $toLabel, $relationship)
    {
        $queryString = "
            MATCH (f:$fromLabel)<-[:$relationship]-(t:$toLabel)
            WHERE f.id = {fid}
            RETURN t
            ";

        $results = \Neo4jQuery::getResultSet($queryString, ['fid' => $fid]);

        $nodes = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $nodes[] = $result['t'];
            }
        }

        return $nodes;
    }

    public function getNodesByLabel($label)
    {
        $queryString = "
            MATCH (f:$label)
            return f
        ";

        $results = \Neo4jQuery::getResultSet($queryString);

        $nodes = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $nodes[] = $result['f'];
            }

            return $nodes;
        }

        return $nodes;
    }

    public function getEndNodes($label, $id, $relation = '')
    {
        if (!empty($relation)) {
            $relation = ':'.$relation;
        }

        $queryString = "
            MATCH (f:$label)-[r$relation]-(t)
            WHERE f.id = {id}
            return r, t
        ";
        //dd($queryString);
        $results = \Neo4jQuery::getResultSet($queryString, ['id' => $id]);

        $nodes = [];

        if ($results->count()) {
            $counter = 0;
            foreach ($results as $result) {
                $nodes[$counter]['relation'] = $result['r']->getType();
                $nodes[$counter]['node'] = $result['t'];
                $labels = $result['t']->getLabels();
                $nodes[$counter]['label'] = $labels[0]->getName();

                $counter++;
            }
        }

        return $nodes;
    }

    public function deleteNodesByIds($elementLabel, $ids)
    {
        $queryString = "MATCH (element:$elementLabel)
                            WHERE element.id IN [".implode(', ', $ids)."]
                                            OPTIONAL MATCH (element)-[relation]-()                     
                      DELETE element, relation";

        $results = \Neo4jQuery::getResultSet($queryString, [], true);

        if ($results) {
            return $ids;
        }
    }

    public function getNodesNoChild($label)
    {
        $nodes = [];

        $queryString = "
        MATCH (nodes:$label)
        WHERE NOT((:$label)-[:PARENT_OF]-(nodes))
        RETURN nodes";

        $results = \Neo4jQuery::getResultSet($queryString, [], true);

        if ($results->count()) {
            foreach ($results as $node) {
                $nodes[] = $node['nodes']->getProperties();
            }
        }

        return $nodes;
    }
}