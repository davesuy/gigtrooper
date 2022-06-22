<?php

namespace Gigtrooper\Services;

use Gigtrooper\Fields\BaseField;
use Gigtrooper\Models\BaseModel as Model;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\Date;
use Carbon\Carbon;

class DateTimeService
{
    private $dates;

    public function createDateTime($fromModel, $dates = [], BaseField $fieldObj)
    {
        $settings = $fieldObj->getSettings();
        $handle = $settings['handle'];

        $isMultiple = (empty($settings['multiple']))? false : true;

        $relationship = strtoupper($handle);

        $this->dates = $dates;

        $year = (int) $dates['year'];
        $month = (int) $dates['month'];
        $day = (int) $dates['day'];

        $queryArgs = ['id' => $fromModel->id];

        $label = $fromModel->getLabel();

        if (!$isMultiple) {
            $queryString = "
            MATCH (u:$label {id: {id}})-[r:$relationship]-(t:DateTime)
            WITH r, t
            MATCH (t)-[tr]-()
            DELETE r,tr,t";

            \Neo4jQuery::getResultSet($queryString, $queryArgs);
        }

        $time = $this->generateTime();

        $queryString = "
            MATCH (u:$label {id: {id}})
            MERGE (y:DateYear {value: $year})
            MERGE (y)-[:CONTAINS]->(m:DateMonth {value: $month})
            MERGE (m)-[:CONTAINS]->(d:DateDay {value: $day})
            
            CREATE (t:DateTime {value: $time})
            MERGE (d)-[:CONTAINS]->(t)
            MERGE (t)-[:$relationship]->(u)
            RETURN u
            ";

        \Neo4jQuery::getResultSet($queryString, $queryArgs);
    }

    private function generateTime()
    {
        $dates = $this->dates;

        if (empty($dates)) {
            $time = time();
        } else {
            $year = $dates['year'];
            $month = $dates['month'];
            $day = $dates['day'];
            $hour = !empty($dates['hour']) ? $dates['hour'] : date('H');
            $min = !empty($dates['minute']) ? $dates['minute'] : date('i');
            $sec = !empty($dates['second']) ? $dates['second'] : date('s');

            $time = mktime($hour, $min, $sec, $month, $day, $year);
        }

        return $time;
    }

    public function getDate(BaseModel $model, $relationship)
    {
        $label = $model->getLabel();
        $queryArgs = ['id' => $model->id];

        $queryString = "
            MATCH (u:$label {id: {id}})-[:$relationship]-(time:DateTime)

            RETURN time
            ";

        $results = \Neo4jQuery::getResultSet($queryString, $queryArgs);
        $time = 0;
        if ($results->count()) {
            foreach ($results as $result) {
                $time = $result['time']->getProperty('value');
            }
        }

        return $time;
    }

    public function getDateByFormat($timeStamp, $format = 'd-M-Y H:i:s')
    {
        $date = new \DateTime();
        $date->setTimestamp($timeStamp);
        $date->setTimezone(new \DateTimeZone('Asia/Singapore'));

        return $date->format($format);
    }
}