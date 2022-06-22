<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;
use Gigtrooper\Services\DateService;
use Carbon\Carbon;
use Gigtrooper\Services\DateTimeService;

class DateTimeField extends BaseField
{
    protected $matchString = '';
    protected $value = [];

    public function getName()
    {
        return "DateTime";
    }

    public function getInputHtml($handle)
    {
        $value = $this->getValue();

        return view('fields.date', [
            'handle' => $handle,
            'settings' => $this->settings,
            'value' => $value,
            'title' => $this->getTitle(),
            'class' => $this->settings['class'] ?? null
        ]);
    }

    public function getValue()
    {
        $value = null;

        if (\Request::old('fields') != null) {
            $oldFields = \Request::old('fields');
            $fieldHandle = $this->settings['handle'];

            if (isset($oldFields[$fieldHandle])) {
                $value = $oldFields[$fieldHandle];
            }
        } elseif ($this->element->getModel() != null) {
            $model = $this->element->getModel();
            $dateService = \App::make('dateTimeService');
            $relationship = strtoupper($this->settings['handle']);
            $value = $dateService->getDate($model, $relationship);

            $dt = Carbon::now();

            if ($value) {
                $dt->timestamp($value)->timezone('Europe/London');

                $value = $dt->format('d-M-Y');
            }
        }

        return $value ? $value : '';
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        // Ignore invalid date format
        try {
            if (!empty($fieldValue)) {
                $dateService = \App::make('dateService');

                /**
                 * @var $dateTimeService DateTimeService
                 */
                $dateTimeService = \App::make('dateTimeService');

                $dateTime = explode(' ', $fieldValue);

                $hour   = null;
                $minute = null;
                $second = null;
                if (isset($dateTime[1])) {
                    $fieldValue = $dateTime[0];
                    $time = explode(":", $dateTime[1]);
                    $hour   = $time[0];
                    $minute = $time[1];
                    $second = $time[2];
                }

                $datesEx = explode('-', $fieldValue);

                $day = $datesEx[0];
                $month = $dateService->getNumberShortMonth($datesEx[1]);
                $year = $datesEx[2];


                $dates = [
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'hour' => $hour,
                    'minute' => $minute,
                    'second' => $second,
                ];
                //dd($dates);
                $dateTimeService->createDateTime($fromModel, $dates, $this);
              //  $dateService->initDate($fromModel, $dates, $relationship);
               // $dateService->createOne();
            } else {
               // \Neo4jRelation::initRelation($fromModel, [], $relationship);

               // \Neo4jRelation::removeFromRelationships();
            }
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function getSearchHtml()
    {
        $handle = $this->settings['handle'];

        $value = null;

        if (\Request::get('f') !== null) {
            $filterFields = \Request::get('f');

            if (isset($filterFields[$handle])) {
                $value = $filterFields[$handle];
            }
        }

        $dateService = \App::make('dateService');

        $dateFormat = $dateService->getDateFormat($this->settings);

        $dateValues = [];
        $dateValues['day'] = '';
        $dateValues['month'] = '';
        $dateValues['year'] = '';

        if (!empty($value['day'])) {
            $dateValues['day'] = $value['day'];
        }

        if (!empty($value['month'])) {
            // Change input date format
            $months = $dateService->getMonths();
            $dateValues['month'] = array_search($value['month'], $months);
        }

        if (!empty($value['year'])) {
            $dateValues['year'] = $value['year'];
        }
        $inputValue = [];
        $inputValue['value'] = '';
        $inputValue['alt'] = '';
        if (!empty($dateValues)) {
            $inputDateValues = $dateValues;

            if (!empty($this->settings['hideDay'])) {
                unset($inputDateValues['day']);
            }
            if (!empty($this->settings['hideMonth'])) {
                unset($inputDateValues['month']);
            }
            if (!empty($this->settings['hideYear'])) {
                unset($inputDateValues['year']);
            }

            if (!empty($dateValues['day']) || !empty($dateValues['month']) || !empty($dateValues['year'])) {
                $inputValue['value'] = implode(' ', $dateValues);
                $inputValue['alt'] = implode(' ', $inputDateValues);
            }
        }

        // Put back number on month value
        $dateValues = array_merge($dateValues, ['month' => $value['month']]);

        return view('fields.datesearch', [
            'handle' => $handle,
            'inputValue' => $inputValue,
            'dateValues' => $dateValues,
            'title' => $this->getTitle(),
            'dateFormat' => $dateFormat['dateFormat'],
            'dateJs' => $dateFormat['dateJs'],
            'settings' => $this->settings
        ]);
    }

    public function getMatchesTax()
    {
        $handle = $this->settings['handle'];

        if (\Request::get('f') !== null) {
            $filterFields = \Request::get('f');

            if (isset($filterFields[$handle])) {
                $value = $filterFields[$handle];

                if (empty($value['day']) || empty($value['month']) || empty($value['year'])) {
                    $this->matchString = '';
                    return $this->matchString;
                }
            }
        }

        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : $handle;
        $smallRelationship = strtolower($relationship);
        $upRelationship = strtoupper($relationship);

        $this->matchString = "({$handle}xyear:DateYear)-[:CONTAINS]->({$handle}xmonth:DateMonth)
        -[:CONTAINS]->({$handle}xday:DateDay)-[:CONTAINS]->({$handle}xdatetime:DateTime)-[:$upRelationship]->(element)";

        return $this->matchString;
    }

    public function prepareWhereValue($handle, $value)
    {
        $yearKey = $handle.'year';
        $monthKey = $handle.'month';
        $dayKey = $handle.'day';

        $dateWheres = [];

        $this->value = $value;

        if (!empty($this->value['day'])) {
            $dateWheres[$dayKey] = (int)$value['day'];
        }

        if (!empty($this->value['month'])) {
            $dateWheres[$monthKey] = (int)$value['month'];
        }

        if (!empty($this->value['year'])) {
            $dateWheres[$yearKey] = (int)$value['year'];
        }

        return $dateWheres;
    }

    public function getWhereCql($value)
    {
        if (empty($this->matchString)) {
            return '1=1';
        }

        $handle = $this->settings['handle'];

        $date = [
            'year' => '{'.$handle.'year}',
            'month' => '{'.$handle.'month}',
            'day' => '{'.$handle.'day}'
        ];

        $handle = $this->settings['handle'];

        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : $handle;
        $smallRelationship = strtolower($relationship);

        $whereDates = [];

        if (isset($date['year']) && $this->value['year']) {
            $year = $date['year'];
            $whereDates[] = "{$handle}xyear.value = $year";
        }
        if (isset($date['month']) && $this->value['month']) {
            $month = $date['month'];
            $whereDates[] = "{$handle}xmonth.value = $month";
        }

        if (isset($date['day']) && $this->value['day']) {
            $day = $date['day'];
            $whereDates[] = "{$handle}xday.value = $day";
        }

        return (!empty($whereDates))? implode(" AND ", $whereDates) : "1=1";
    }

    public function getOrderTax()
    {
        $handle = $this->settings['handle'];
        $returnHandle = $handle.'xdatetime.value AS orderx'.$handle;

        return $returnHandle;
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $dateService = \App::make('dateTimeService');

        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : $handle;
        $relationship = strtoupper($relationship);

        return $dateService->getDate($model, $relationship);
    }
}