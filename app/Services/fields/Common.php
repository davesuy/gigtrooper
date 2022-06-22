<?php

namespace Gigtrooper\Services\fields;


class Common
{
    public static function getData()
    {
        $fields = [];

        $fields[] = [
            'handle' => 'currency',
            'field' => 'ElementField'
        ];

        $fields[] = [
            'handle' => 'excerpt',
            'field' => 'RichTextField',
            'options' => ['height' => 100, 'basic' => true]
        ];

        $fields[] = [
            'handle' => 'body',
            'field' => 'RichTextField'
        ];

        $fields[] = [
            'handle' => 'fee',
            'field' => 'MinMaxField',
            'title' => 'Fee Range',
            'step' => 500,
            'message' => 'Contact for quotation',
            'element' => true
        ];

        $fields[] = [
            'handle' => 'Status',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'unverified',
                    'label' => 'Unverified'
                ],
                [
                    'value' => 'verified',
                    'label' => 'Verified'
                ],
                [
                    'value' => 'disabled',
                    'label' => 'Disabled'
                ],
                [
                    'value' => 'active',
                    'label' => 'Active'
                ]
            ]
        ];


        $fields[] = [
            'handle' => 'Created',
            'title' => 'Date created',
            'field' => 'DateField',
            //'hideYear' => true,
            //'hideMonth' => true,
           // 'hideDay' => true
        ];

        $fields[] = [
            'handle' => 'dateCreated',
            'title' => 'Date Created',
            'field' => 'DateTimeField',
            //'hideYear' => true,
            //'hideMonth' => true,
            'hideDay' => true
        ];

        $fields[] = [
            'handle' => 'Updated',
            'title' => 'Date updated',
            'field' => 'DateField',
            //'hideYear' => true,
            //'hideMonth' => true,
            'hideDay' => true
        ];

        $fields[] = [
            'handle' => 'dateUpdated',
            'title' => 'Date Updated',
            'field' => 'DateTimeField',
            //'hideYear' => true,
            //'hideMonth' => true,
            'hideDay' => true
        ];

        $fields[] = [
            'handle' => 'DateExpiry',
            'title' => 'Date Expiry',
            'field' => 'DateTimeField'
        ];

        $fields[] = [
            'title' => 'Has Sub Field',
            'handle' => 'SubField',
            'field' => 'SubField',
            'propertyKey' => 'handle',
            'generate' => true
        ];

        $fields[] = [
            'handle' => 'title',
            'field' => 'PlaintextField',
            'rules' => 'required'
        ];

        $fields[] = [
            'handle' => 'subTitle',
            'field' => 'PlaintextField'
        ];

        $fields[] = [
            'handle' => 'metaTitle',
            'field' => 'PlaintextField'
        ];

        $fields[] = [
            'handle' => 'points',
            'field' => 'NumberField',
            'defaultValue' => 0
        ];

        $fields[] = [
            'handle' => 'adminPoints',
            'field' => 'NumberField',
            'defaultValue' => 0
        ];

        $fields[] = [
            'handle' => 'slug',
            'field' => 'SlugField'
        ];

        $fields[] = [
            'handle' => 'eventType',
            'generate' => true,
            'title' => 'Event Type',
            'class' => 'gray-bg',
            'field' => 'AutoDropdownField',
            'rules' => 'required',
            'options' => [
                [
                    'value' => 'Anniversary',
                    'label' => 'Anniversary'
                ],
                [
                    'value' => 'Birthday Party',
                    'label' => 'Birthday Party'
                ],
                [
                    'value' => 'Celebration',
                    'label' => 'Celebration'
                ],
                [
                    'value' => 'Club Event',
                    'label' => 'Club Event'
                ],
                [
                    'value' => 'Community Event',
                    'label' => 'Community Event'
                ],
                [
                    'value' => 'Corporate Function',
                    'label' => 'Corporate Function'
                ],
                [
                    'value' => 'Family Reunion',
                    'label' => 'Family Reunion'
                ],
                [
                    'value' => 'Private Party',
                    'label' => 'Private Party'
                ],
                [
                    'value' => 'Festival',
                    'label' => 'Festival'
                ],
                [
                    'value' => 'Wedding',
                    'label' => 'Wedding'
                ],
                [
                    'value' => 'Debut Party',
                    'label' => 'Debut Party'
                ],
                [
                    'value' => 'Private Party',
                    'label' => 'Private Party'
                ],
                [
                    'value' => 'Bachelor Party',
                    'label' => 'Bachelor Party'
                ],
                [
                    'value' => 'Bachelorette Party',
                    'label' => 'Bachelorette Party'
                ],
                [
                    'value' => 'Bridal Shower',
                    'label' => 'Bridal Shower'
                ],
                [
                    'value' => 'Christening',
                    'label' => 'Christening'
                ],
                [
                    'value' => 'Christmas Party',
                    'label' => 'Christmas Party'
                ],
                [
                    'value' => 'Cocktail Party',
                    'label' => 'Cocktail Party'
                ],
                [
                    'value' => 'Concert',
                    'label' => 'Concert'
                ],
                [
                    'value' => 'Convention',
                    'label' => 'Convention'
                ],
                [
                    'value' => 'Halloween Party',
                    'label' => 'Halloween Party'
                ],
                [
                    'value' => 'High School Reunion',
                    'label' => 'High School Reunion'
                ],
                [
                    'value' => 'Memorial Service',
                    'label' => 'Memorial Service'
                ],
                [
                    'value' => 'Prom',
                    'label' => 'Prom'
                ],
                [
                    'value' => "Valentine's Day Party",
                    'label' => "Valentine's Day Party"
                ],
            ]
        ];

        $fields[] = [
            'handle' => 'eventStatus',
            'generate' => true,
            'title' => 'Event Status',
            'class' => 'gray-bg',
            'field' => 'DropdownField',
            'rules' => 'required',
            'options' => [
                [
                    'value' => 'disabled',
                    'label' => 'Disabled'
                ],
                [
                    'value' => 'pending',
                    'label' => 'Pending'
                ],
                [
                    'value' => 'active',
                    'label' => 'Active'
                ],
                [
                    'value' => 'processing',
                    'label' => 'Processing'
                ],
                [
                    'value' => 'live',
                    'label' => 'Live'
                ],
                [
                    'value' => 'finished',
                    'label' => 'Finished'
                ]
            ]
        ];

        $fields[] = [
            'handle' => 'eventDate',
            'title' => 'Event Date',
            'rules' => 'required',
            'class' => 'gray-bg',
            'minDate' => 1,
            'field' => 'DateTimeField'
        ];

        $fields[] = [
            'handle' => 'eventLocation',
            'title' => 'Event Location',
            'rules' => 'required',
            'field' => 'PlaintextField'
        ];

        $fields[] = [
            'handle' => 'eventStartTime',
            'title' => 'Event Start Time',
            'rules' => 'required',
            'field' => 'DropdownElementField',
            'options' => static::getTimes()
        ];

        $fields[] = [
            'handle' => 'eventServiceLength',
            'title' => 'Service Length',
            'rules' => 'required',
            'field' => 'DropdownElementField',
            'options' => static::getTimeLengths()
        ];

        $fields[] = [
            'handle' => 'eventGuests',
            'title'  => 'Number of Guests? (estimate)',
            'field' => 'NumberField',
            'defaultValue' => 15
        ];

        $fields[] = [
            'handle' => 'eventDetails',
            'title'  => 'Additional Details',
            'field' => 'PlaintextField'
        ];

        return $fields;
    }

    static public function getTimeLengths()
    {
        $options = [];
        $options[".25"] = "15 minutes";
        $options[".50"] = "30 minutes";
        $options[".75"] = "45 minutes";
        $options["1"] = "1 hour";
        $options["1.5"] = "1 hour 30 minutes";
        $options["2"] = "2 hours";
        $options["2.5"] = "2 hours 30 minutes";
        $options["3"] = "3 hours";
        $options["3.5"] = "3 hours 30 minutes";
        $options["4"] = "4 hours";
        $options["4.5"] = "4 hours 30 minutes";
        $options["5"] = "5 hours";
        $options["5.5"] = "5 hours 30 minutes";
        $options["6"] = "6 hours";
        $options["6.5"] = "6 hours 30 minutes";
        $options["7"] = "7 hours";
        $options["7.5"] = "7 hours 30 minutes";
        $options["8"] = "8 hours";
        $options["8.5"] = "8 hours 30 minutes";
        $options["9"] = "9 hours";
        $options["9.5"] = "9 hours 30 minutes";
        $options["10"] = "10 hours";
        $options["10.5"] = "10 hours 30 minutes";
        $options["11"] = "11 hours";
        $options["11.5"] = "11 hours 30 minutes";
        $options["12"] = "12 hours";

        return $options;
    }

    static public function getTimes()
    {
        $first  = static::getTimeRange("8:00", "23:59");
        $second = static::getTimeRange("00:00", "7:59");

        return array_merge($first, $second);
    }

    static private function getTimeRange($start, $end)
    {
        $open_time = strtotime($start);
        $close_time = strtotime($end);

        $times = [];

        // 30 min interval
        for( $i=$open_time; $i<$close_time; $i+=1800) {
            $time = date("g:i A",$i);
            $times[$time] = $time;
        }

        return $times;
    }
}