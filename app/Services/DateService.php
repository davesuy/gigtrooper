<?php

namespace Gigtrooper\Services;

use Gigtrooper\Models\BaseModel as Model;
use Gigtrooper\Models\Date;
use Carbon\Carbon;

class DateService
{
    private $model;
    private $dateLabel;
    private $dateValue;
    private $year;
    private $month;
    private $day;
    private $dates = [];
    private $modelNamespace;

    private $yearModel;
    private $monthModel;
    private $dayModel;
    private $new;

    protected $transaction;

    public function initDate(Model $model = null, array $dates = [], $makeDateRelationship = 'HAS_DATE', $transaction = false)
    {
        $this->modelNamespace = "Gigtrooper\Models\\";
        $this->transaction = $transaction;
        $this->model = $model;

        $this->dates = $dates;
        $this->makeDateLabel = $makeDateRelationship;
    }

    public function createOne()
    {
        $this->new = false;
        return $this->create();
    }

    public function createMany()
    {
        $this->new = true;
        return $this->create();
    }

    private function create()
    {
        $dates = $this->dates;
        $makeDateRelationship = $this->makeDateLabel;

        if (empty($dates)) {
            $dateObj = Carbon::now();
            $year = $dateObj->year;
            $month = $dateObj->month;
            $day = $dateObj->day;
        } else {
            $year = (int)$dates['year'];
            $month = (int)$dates['month'];
            $day = (int)$dates['day'];
        }

        $this->yearModel = $this->createEachDate('Year', $year);
        $this->monthModel = $this->createEachDate('Month', $month);
        $this->dayModel = $this->createEachDate('Day', $day);

        $this->addRelation();

        return $this->makeDate($makeDateRelationship);
    }

    public function createEachDate($dateLabel = null, $dateValue = null)
    {

        $this->dateLabel = $dateLabel;
        $this->dateValue = $dateValue;


        $model = $this->modelNamespace.$this->dateLabel;

        $exist = $model::findByAttribute('value', $this->dateValue);

        if (!$exist) {
            return $this->generateDate();
        }
        return $exist;
    }

    public function generateDate()
    {
        $dateLabel = $this->dateLabel;
        $dateValue = $this->dateValue;

        $modelName = $this->modelNamespace.$dateLabel;
        $model = new $modelName;

        $model->value = $dateValue;

        return $model->save();
    }

    private function addRelation()
    {
        $year = $this->year;
        $month = $this->month;
        $day = $this->day;

        $yearModel = $this->yearModel;
        $monthModel = $this->monthModel;
        $dayModel = $this->dayModel;
        $transaction = $this->transaction;

        \Neo4jRelation::initRelation($monthModel, $yearModel, 'HAS_CHILD');
        \Neo4jRelation::addOne($transaction);

        \Neo4jRelation::initRelation($dayModel, $monthModel, 'HAS_CHILD');
        \Neo4jRelation::addOne($transaction);
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
            $hour = isset($dates['hour']) ? $dates['hour'] : date('H');
            $min = isset($dates['minute']) ? $dates['hour'] : date('i');
            $sec = isset($dates['second']) ? $dates['hour'] : date('s');

            $time = mktime($hour, $min, $sec, $month, $day, $year);
        }

        return $time;
    }

    private function generateConnectingDate()
    {
        $dateObj = new Date;

        $time = $this->generateTime();

        $dateObj->value = $time;
        $dateModel = $dateObj->save();
        return $dateModel;
    }

    private function makeDate($makeDateRelationship = 'HAS_DATE')
    {
        $model = $this->model;
        $transaction = $this->transaction;

        $yearModel = $this->yearModel;
        $monthModel = $this->monthModel;
        $dayModel = $this->dayModel;

        $new = $this->new;

        if ($new == true) {
            $dateModel = $this->generateConnectingDate();
        } else {
            $exist = \Neo4jRelation::hasRelationship($model, 'Date', $makeDateRelationship);

            if (!$exist) {
                $dateModel = $this->generateConnectingDate();
            } else {
                $dateModel = \Neo4jRelation::getEndModel($model, $makeDateRelationship);
            }

            $time = $this->generateTime();

            $dateModel->value = $time;
            $dateModel = $dateModel->save();
        }

        \Neo4jRelation::initRelation($model, $dateModel, $makeDateRelationship);
        if ($new == true) {
            \Neo4jRelation::add($transaction);
        } else {
            \Neo4jRelation::addOne($transaction);
        }


        \Neo4jRelation::initRelation($dateModel, $yearModel);
        \Neo4jRelation::addOne($transaction);

        \Neo4jRelation::initRelation($dateModel, $monthModel);
        \Neo4jRelation::addOne($transaction);

        \Neo4jRelation::initRelation($dateModel, $dayModel);
        \Neo4jRelation::addOne($transaction);

        return $dateModel;
    }

    public function getDate($model, $relationship, $time = true)
    {
        $id = $model->id;
        $label = $model->getLabel();

//        $queryString = "
//                                    MATCH (model:$label)<-[:$relationship]-(date:Date)<--(time)
//                                    WHERE model.id = $id
//                                    RETURN labels(time) as dateLabels, time, date";
        $queryString = "
                                    MATCH (model:$label)<-[:$relationship]-(date:Date)
                                    WHERE model.id = $id
                                    RETURN date";
       // echo $queryString;
        try {
            $results = \Neo4jQuery::getResultSet($queryString);

            $dates = [];
            $response = [];
            $dates['time'] = 0;

            if ($results->count()) {
                foreach ($results as $result) {
//                    $label = $result['dateLabels']->current();
//                    $label = strtolower($label);
//                    $dates[$label] = $result['time']->getProperty('value');
                    $dates['time'] = $result['date']->getProperty('value');
                }
            }

            // needed for the blade template bug not getting right result for array when key is dynamic
//            $response['day'] = $dates['day'];
//            $response['month'] = $dates['month'];
//            $response['year'] = $dates['year'];
            $response['time'] = $dates['time'];

            if ($time == true) {
                return $response['time'];
            } else {
                return $response;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function attachNowDate($model)
    {
        $exist = \Neo4jRelation::hasRelationship($model, 'Date', 'CREATED');

        $dates = [];

        if (!$exist) {
            $dates = [
                'year' => date('Y'),
                'month' => date('n'),
                'day' => date('j')
            ];
            // xxtempxx
            /*			$dates = array(
                            'year'  => '2018',
                            'month' => '3',
                            'day'   => '5'
                        );*/
            $this->initDate($model, $dates, 'CREATED');
            $this->createOne();
        } else {
            $this->initDate($model, $dates, 'UPDATED');
            $this->createOne();
        }
        return $model;
    }

    public function getMonths()
    {
        $months = [
            'Jan' => 1,
            'Feb' => 2,
            'Mar' => 3,
            'Apr' => 4,
            'May' => 5,
            'Jun' => 6,
            'Jul' => 7,
            'Aug' => 8,
            'Sep' => 9,
            'Oct' => 10,
            'Nov' => 11,
            'Dec' => 12
        ];

        return $months;
    }

    public function getNumberShortMonth($shortMonth = '')
    {
        $months = $this->getMonths();

        if (!isset($months[$shortMonth])) {
            throw new \Exception("Invalid short month $shortMonth");
        }

        return $months[$shortMonth];
    }

    public function getDateFormat($settings = [])
    {
        $result = [];

        $jsFormats['day'] = "d";
        $jsFormats['month'] = "M";
        $jsFormats['year'] = "yy";

        $jsDates['year'] = "inst.selectedYear";
        $jsDates['month'] = "inst.selectedMonth";
        $jsDates['day'] = "inst.selectedDay";

        if (!empty($settings['hideDay'])) {
            unset($jsFormats['day']);
            unset($jsDates['day']);
        }

        if (!empty($settings['hideMonth'])) {
            unset($jsFormats['month']);
            unset($jsDates['month']);
        }

        if (!empty($settings['hideYear'])) {
            unset($jsFormats['year']);
            unset($jsDates['year']);
        }

        $dateFormat = implode(' ', $jsFormats);
        $dateJs = implode(', ', $jsDates);

        $result['dateFormat'] = $dateFormat;
        $result['dateJs'] = $dateJs;

        return $result;
    }

    public function getDateByFormat($timeStamp, $format = 'd-M-Y H:i')
    {
        $date = new \DateTime();
        $date->setTimestamp($timeStamp);
        $date->setTimezone(new \DateTimeZone('Asia/Singapore'));

        return $date->format($format);
    }

    public function getTimeAgo($time)
    {
        $TIMEBEFORE_NOW =         'now';
        $TIMEBEFORE_MINUTE =      '{num} minute ago';
        $TIMEBEFORE_MINUTES =     '{num} minutes ago';
        $TIMEBEFORE_HOUR =        '{num} hour ago';
        $TIMEBEFORE_HOURS =       '{num} hours ago';
        $TIMEBEFORE_YESTERDAY =   'yesterday';
        $TIMEBEFORE_FORMAT =      '%e %b %Y';
        $TIMEBEFORE_FORMAT_YEAR = '%e %b, %Y';
        
        $out    = ''; // what we will print out
        $now    = time(); // current time
        $diff   = $now - $time; // difference between the current and the provided dates

        if( $diff < 60 ) // it happened now
            return $TIMEBEFORE_NOW;
        elseif( $diff < 3600 ) // it happened X minutes ago
            return str_replace( '{num}', ( $out = round( $diff / 60 ) ), $out == 1 ? $TIMEBEFORE_MINUTE : $TIMEBEFORE_MINUTES );
        elseif( $diff < 3600 * 24 ) // it happened X hours ago
            return str_replace( '{num}', ( $out = round( $diff / 3600 ) ), $out == 1 ? $TIMEBEFORE_HOUR : $TIMEBEFORE_HOURS );
        elseif( $diff < 3600 * 24 * 2 ) // it happened yesterday
            return $TIMEBEFORE_YESTERDAY;
        else // falling back on a usual date format as it happened later than yesterday
            return strftime( date( 'Y', $time ) == date( 'Y' ) ? $TIMEBEFORE_FORMAT : $TIMEBEFORE_FORMAT_YEAR, $time );
    }
}