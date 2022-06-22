<?php

    namespace Gigtrooper\Services;

    use Gigtrooper\Models\Field;

    class Neo4jRelationService
    {
    private $fromModel;
    private $toModel;
    private $relationship;
    private $generate;
    private $toModelIds;

    public function initRelation($fromModel, $toModel, $relationship = false, $generate = false)
    {
        $this->fromModel = $fromModel;
        $this->toModel = $toModel;

        $relationship = $this->setRelationshipLabel($relationship);
        $this->relationship = $relationship;

        $this->generate = $generate;
    }

    public function add($transaction = false)
    {

        if ($this->generate) {
            $this->toModel = $this->toModel->save();
        }

        $this->removeBetweenRelationship($transaction);

        return $this->createRelationship($transaction);
    }

    public function addOne($transaction = false)
    {
        $relationship = $this->relationship;

        // Only add if relation does not exist
        $isExist = $this->isRelationshipExist($this->fromModel, $this->toModel, $relationship);

        if ($isExist == true) {
            return true;
        }

        if ($this->generate) {
            $this->toModel = $this->toModel->save();
        }

        $this->removeFromRelationships($transaction);

        return $this->createRelationship($transaction);
    }

    public function addNewOne($transaction = false)
    {
        if ($this->generate) {
            $this->toModel = $this->toModel->save();
        }

        $this->removeToNode($transaction);

        return $this->createRelationship($transaction);
    }

    public function sync($transaction = false)
    {
        if ($this->relationship == false) {
            throw new \Exception('sync method must pass a relationship label');
        }

        $this->removeFromRelationships($transaction);

        return $this->createManyRelationship($transaction);
    }

    public function areSameLabel(array $tos)
    {
        if (!empty($tos)) {
            $firstLabel = $tos[0]->getLabel();
            foreach ($tos as $to) {
                $label = $to->getLabel();
                if ($firstLabel != $label) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function createManyRelationship($transaction = false)
    {
        $string = $this->createManyRelationshipQuery();

        $arrayIds = array_merge($this->modelFromParams(), $this->getToModelIds());
        if ($transaction) {
            \Neo4jQuery::addQuery($string, $arrayIds);

            return true;
        }

        $result = \Neo4jQuery::getResultSet(
            $string,
            $arrayIds);

        return $result;
    }

    public function createManyRelationshipQuery()
    {
        if (!is_array($this->toModel)) {
            throw new \Exception('To model must be an array');
        }
        $fromModelLabel = $this->fromModel->getLabel();
        $toModels = $this->toModel;

        if (!empty($toModels)) {
            $isSame = $this->areSameLabel($toModels);
            if (!$isSame) {
                throw new \Exception('Sync TO model must all have same label');
            }

            $toModelLabel = $toModels[0]->getLabel();

            $ids = [];
            foreach ($toModels as $toModel) {
                $ids['ids'][] = $toModel->id;
            }
            $this->toModelIds = $ids;
        } else {
            throw new \Exception('Empty to array');
        }
        $relationship = $this->relationship;

        return "MATCH (f:$fromModelLabel), (t:$toModelLabel)
            WHERE f.id = {fid} AND t.id IN {ids}
            CREATE (f)<-[r:$relationship]-(t)
            RETURN r";
    }

    public function getToModelIds()
    {
        return $this->toModelIds;
    }


    /**
     * @param string $rel
     *
     * @return string
     */
    public function setRelationshipLabel($relation)
    {
        if ($relation == false) {
            if (is_array($this->toModel)) {
                $toLabel = $this->toModel[0]->getLabel();
            } else {
                $toLabel = $this->toModel->getLabel();
            }

            $toLabel = strtoupper($toLabel);
            $relation = $toLabel.'_OF';
        }
        return $relation;
    }

    public function createRelationshipQuery()
    {

        $fromModelLabel = $this->fromModel->getLabel();
        $toModelLabel = $this->toModel->getLabel();

        $fid = $this->fromModel->id;
        $tid = $this->toModel->id;
        $relationship = $this->relationship;

        $query = "
            MATCH (f:$fromModelLabel),(t:$toModelLabel)
            WHERE f.id = {fid} AND t.id = {tid}
            CREATE (f)<-[r:$relationship]-(t)
            RETURN r
            ";

        return $query;
    }

    /**
     * Create Relationship between 2 nodes
     *
     * @return bool
     */
    public function createRelationship($transaction = false)
    {

        if ($transaction) {
            \Neo4jQuery::addQuery($this->createRelationshipQuery(), $this->modelParams());
            return;
        }

        $result = \Neo4jQuery::getResultSet(
            $this->createRelationshipQuery(),
            $this->modelParams());

        return $result;
    }

    public function isRemoveUpdateQuery()
    {
        $relationship = $this->relationship;

        $fromModelLabel = $this->fromModel->getLabel();
        $toModelLabel = $this->toModel->getLabel();

        return "
                MATCH (f:$fromModelLabel)<-[r:$relationship]-(t:$toModelLabel)
                WHERE f.id = {fid} AND t.id = {tid}
                DELETE r
            ";
    }

    public function removeToNodeQuery()
    {
        $relationship = $this->relationship;

        $fromModelLabel = $this->fromModel->getLabel();

        return "
                MATCH (f:$fromModelLabel)<-[r:$relationship]-(t)
                WHERE f.id = {fid}
                DELETE r, t
            ";
    }

    public function removeToNode($transaction = false)
    {
        if ($transaction) {
            \Neo4jQuery::addQuery($this->removeToNodeQuery(), $this->modelFromParams());
            return;
        }

        $result = \Neo4jQuery::getResultSet(
            $this->removeToNodeQuery(),
            $this->modelFromParams());

        return $result;
    }

    public function modelParams()
    {
        $fid = $this->fromModel->id;
        $tid = $this->toModel->id;

        return ['fid' => $fid, 'tid' => $tid];
    }

    public function modelFromParams()
    {
        $fid = $this->fromModel->id;


        return ['fid' => $fid];
    }


    /**
     * Remove the relationship between two nodes only if it exist
     */
    public function removeBetweenRelationship($transaction = false)
    {
        $relationship = $this->relationship;

        if ($transaction) {
            \Neo4jQuery::addQuery($this->isRemoveUpdateQuery(), $this->modelParams());
        } else {

            $isUpdate = $this->isRelationshipExist($this->fromModel, $this->toModel, $relationship);

            if ($isUpdate) {
                $removeUpdate = \Neo4jQuery::getResultSet(
                    $this->isRemoveUpdateQuery(),
                    $this->modelParams()
                );
            }
        }
    }

    protected function getRelationshipString($relationship = false, $direction = 'in')
    {
        $relationshipString = ($direction == 'in') ? "<-[:$relationship]-" : "-[:$relationship]->";

        return $relationshipString;
    }

    public function isRelationshipExist($fromModel, $toModel, $relationship = false, $direction = 'in')
    {

        $fromModelLabel = $fromModel->getLabel();
        $toModelLabel = $toModel->getLabel();
        $fid = $fromModel->id;
        $tid = $toModel->id;

        $relationshipString = $this->getRelationshipString($relationship);

        $queryString = "
            MATCH (f:$fromModelLabel)$relationshipString(t:$toModelLabel)
            WHERE f.id = {fid} AND t.id = {tid}
            RETURN f
            ";

        $isUpdate = \Neo4jQuery::getResultSet($queryString, ['fid' => $fid, 'tid' => $tid]);

        if ($isUpdate->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function hasRelationship($fromModel, $toLabel, $relationship = false, $direction = 'in')
    {
        $fromModelLabel = $fromModel->getLabel();
        $fid = $fromModel->id;

        $relationshipString = $this->getRelationshipString($relationship);

        $queryString = "
            MATCH (f:$fromModelLabel)$relationshipString(t:$toLabel)
            WHERE f.id = {fid}
            RETURN f
            ";

        $isUpdate = \Neo4jQuery::getResultSet($queryString, ['fid' => $fid]);

        if ($isUpdate->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function removeFromRelationshipsQuery()
    {
        $relationship = $this->relationship;

        $fromModelLabel = $this->fromModel->getLabel();

        return "
                MATCH (f:$fromModelLabel)<-[r:$relationship]-(t)
                WHERE f.id = {fid}
                DELETE r
            ";
    }

    public function removeFromRelationships($transaction = false)
    {
        if ($transaction) {
            \Neo4jQuery::addQuery($this->removeFromRelationshipsQuery(), $this->modelFromParams());
            return true;
        }

        $result = \Neo4jQuery::getResultSet(
            $this->removeFromRelationshipsQuery(),
            $this->modelFromParams());

        return $result;
    }

    public function deleteMultipleModelsQuery(array $models)
    {

        $isSame = $this->areSameLabel($models);
        if (!$isSame) {
            throw new \Exception('Delete multiple models must all have same label');
        }
        $label = $models[0]->getLabel();
        if (!empty($models)) {
            $ids = [];
            foreach ($models as $model) {
                $ids['ids'][] = $model->id;
            }
        }
        $result = [];
        $result['query'] = "MATCH (n:$label)
                            WHERE n.id IN {ids}
                            OPTIONAL MATCH (n)-[r]-()
                            DELETE n, r";

        $result['ids'] = $ids;

        return $result;
    }

    public function deleteMultipleModels(array $models, $transaction = false)
    {
        $resultQuery = $this->deleteMultipleModelsQuery($models);

        if ($transaction) {
            \Neo4jQuery::addQuery($resultQuery['query'], $resultQuery['ids']);
            return;
        }

        $result = \Neo4jQuery::getResultSet(
            $resultQuery['query'],
            $resultQuery['ids']);

        return $result;
    }

    private function getEndModelQuery()
    {
        $fromModel = $this->fromModel;
        $fromModelLabel = $fromModel->getLabel();
        $relationship = $this->relationship;
        $relationshipString = $this->getRelationshipString($relationship);
        $queryString = "
            MATCH (f:$fromModelLabel)$relationshipString(t)
            WHERE f.id = {fid}
            RETURN t
            ";
        return $queryString;
    }

    public function getEndModel($fromModel, $relationship = null)
    {
        $this->fromModel = $fromModel;
        $this->relationship = $relationship;
        $fid = $fromModel->id;

        $queryString = $this->getEndModelQuery();

        $result = \Neo4jQuery::getResultSet(
            $queryString,
            ['fid' => $fid]
        );

        if ($result->count()) {
            $node = $result[0]['t'];


            $labelArray = $node->getLabels();
            $label = $labelArray[0]->getName();
            $fieldModel = new Field;
            $fieldModel->defineLabel($label);

            $properties = $node->getProperties();

            $model = $fieldModel::populateModel($properties);
            $model->defineLabel($label);
            $model->setExists(true);
            //$model = $this->getModelByNode($node);

            return $model;
        } else {
            return false;
        }
    }

    public function getModelByNode($node)
    {
        $id = $node->id;
        $labelArray = $node->getLabels();
        $label = $labelArray[0]->getName();
        $modelNamespace = "\Gigtrooper\Models\\".$label;
        $model = $modelNamespace::find($id);
        return $model;
    }
 }