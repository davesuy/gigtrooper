<?php

    namespace Gigtrooper\Fields;

    use Gigtrooper\Models\BaseModel;
    use Gigtrooper\Services\DateService;
    use Carbon\Carbon;

    class DateField extends BaseField
    {
    protected $matchString = '';

    public function getName()
    {
        return "Date";
    }

    public function getInputHtml($handle)
    {
        $value = $this->getValue();

        return view('fields.date', [
            'handle' => $handle,
            'value' => $value,
            'title' => $this->getTitle()
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
            $dateService = \App::make('dateService');
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
        $relationship = strtoupper($handle);

        // Ignore invalid date format
        try {
            if (!empty($fieldValue)) {
                $dateService = \App::make('dateService');

                $datesEx = explode('-', $fieldValue);
                $day = $datesEx[0];
                $month = $dateService->getNumberShortMonth($datesEx[1]);
                $year = $datesEx[2];

                $dates = [
                    'year' => $year,
                    'month' => $month,
                    'day' => $day
                ];

                $dateService->initDate($fromModel, $dates, $relationship);
                $dateService->createOne();
            } else {
                \Neo4jRelation::initRelation($fromModel, [], $relationship);

                \Neo4jRelation::removeFromRelationships();
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

        $this->matchString = "(element)<-[:$upRelationship]-({$smallRelationship}xdate:Date)<--({$smallRelationship}xtime)";

        return $this->matchString;
    }

    public function prepareWhereValue($handle, $value)
    {
        $yearKey = $handle.'year';
        $monthKey = $handle.'month';
        $dayKey = $handle.'day';

        $dateWheres = [];

        if (empty($this->settings['hideDay'])) {
            $dateWheres[$dayKey] = (int)$value['day'];
        }

        if (empty($this->settings['hideMonth'])) {
            $dateWheres[$monthKey] = (int)$value['month'];
        }

        if (empty($this->settings['hideYear'])) {
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

        if (isset($date['year']) && empty($this->settings['hideYear'])) {
            $year = $date['year'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:YEAR_OF]-({value: $year})";
        }
        if (isset($date['month']) && empty($this->settings['hideMonth'])) {
            $month = $date['month'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:MONTH_OF]-({value: $month})";
        }

        if (isset($date['day']) && empty($this->settings['hideDay'])) {
            $day = $date['day'];
            $whereDates[] = "({$smallRelationship}xdate)<-[:DAY_OF]-({value: $day})";
        }

        return implode(" AND ", $whereDates);
    }

    public function getReturnTax()
    {
        $handle = $this->settings['handle'];

        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : $handle;
        $smallRelationship = strtolower($relationship);

        $match = "COLLECT(DISTINCT({$smallRelationship}xtime.value)) AS {$smallRelationship}xtime, 
                            {$smallRelationship}xdate.value AS {$smallRelationship}xtimestamp";

        return $match;
    }

    public function getOrderTax()
    {
        $handle = $this->settings['handle'];
        $returnHandle = strtolower($handle).'xdate.value AS orderx'.$handle;

        return $returnHandle;
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        return $model->getDate($handle);
    }
    }