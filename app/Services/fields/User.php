<?php

namespace Gigtrooper\Services\fields;


class User
{
    public static function getData()
    {
        $fields = [];


        $fields[] = [
            'handle' => 'id',
            'field' => 'ElementField'
        ];

        $fields[] = [
            'handle' => 'email_token',
            'field' => 'ElementField'
        ];

        $fields[] = [
            'handle' => 'Subhead',
            'field' => 'PlaintextField',
            'rules' => 'required',
            'element' => true
        ];

        $fields[] = [
            'handle' => 'loginMethod',
            'field' => 'ElementField'
        ];

        $fields[] = [
            'title' => 'Introduction',
            'handle' => 'introduction',
            'field' => 'RichTextField',
            'options' => ['height' => 100, 'basic' => true, 'maxChar' => 120]
        ];


        $fields[] = [
            'handle' => 'email',
            'field' => 'PlaintextField',
            'rules' => 'required|email|unique:User,email',
            'disabled' => true,
            'params' => ['disabled' => true]
        ];

        $fields[] = [
            'handle' => 'name',
            'field' => 'PlaintextField',
            'operator' => 'like',
            //	'rules'   => 'required|unique:User,name'
        ];

        $fields[] = [
            'handle' => 'shareBox',
            'field' => 'PlaintextField',
            //	'rules'   => 'required|unique:User,name'
        ];

        $fields[] = [
            'handle' => 'aboutMe',
            'field' => 'RichTextField',
            'options' => ['basic' => true, 'maxChar' => 2000]
        ];

        $fields[] = [
            'handle' => 'Role',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'blogger',
                    'label' => 'Blogger'
                ],
                [
                    'value' => 'member',
                    'label' => 'Member'
                ],
                [
                    'value' => 'administrator',
                    'label' => 'Administrator'
                ]
            ]
        ];

        $fields[] = [
            'title' => 'Profile Picture',
            'handle' => 'Avatar',
            'field' => 'AssetField',
            'limit' => 1
        ];


        $fields[] = [
            'title' => 'Author',
            'handle' => 'blogAuthor',
            'field' => 'ModelField',
            'model' => 'User',
            'relationship' => 'AUTHOR_OF',
            'multiple' => false,
            'filter' => ['handle' => 'Role', 'value' => ['administrator', 'blogger']]
        ];


        $fields[] = [
            'handle' => 'email_token',
            'field' => 'ElementField'
        ];

        $fields[] = [
            'handle' => 'password',
            'field' => 'PasswordField',
            'rules' => 'nullable|min:6|same:password_confirm'
        ];


        $fields[] = [
            'title' => 'Contact Number',
            'handle' => 'contactNumber',
            'field' => 'PlaintextField',
            'rules' => 'nullable|numeric'
        ];

        $fields[] = [
            'title' => 'Facebook URL',
            'handle' => 'facebookUrl',
            'field' => 'PlaintextField',
            'rules' => 'nullable|url'
        ];

        $fields[] = [
            'title' => 'Youtube URL',
            'handle' => 'youtubeUrl',
            'field' => 'PlaintextField',
            'rules' => 'nullable|url'
        ];

        $fields[] = [
            'title' => 'Twitter URL',
            'handle' => 'twitterUrl',
            'field' => 'PlaintextField',
            'rules' => 'nullable|url'
        ];

        $fields[] = [
            'title' => 'Instagram URL',
            'handle' => 'instagramUrl',
            'field' => 'PlaintextField',
            'rules' => 'nullable|url'
        ];

        $fields[] = [
            'title' => 'LinkedIn URL',
            'handle' => 'linkedInUrl',
            'field' => 'PlaintextField',
            'rules' => 'nullable|url'
        ];

        $fields[] = [
            'title' => 'Country',
            'handle' => 'Country',
            'field' => 'ModelField',
            'property' => 'title',
            'model' => 'Country',
            'whereKey' => 'countryCode',
            'subfield' => "#subfields-content-Country"
        ];

        $fields[] = [
            'title' => 'Another Dropdown',
            'handle' => 'anotherDropdown',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'first',
                    'label' => 'First value'
                ],
                [
                    'value' => 'second',
                    'label' => 'Second Val'
                ],
                [
                    'value' => 'third',
                    'label' => 'Third v'
                ]
            ]
        ];
        $fields[] = [
            'handle' => 'anotherDate',
            'title' => 'Another Date',
            'field' => 'DateTimeField',
            //'hideYear' => true,
            //'hideMonth' => true,
            'hideDay' => true
        ];
        $fields[] = [
            'title' => 'Another Picture',
            'handle' => 'anotherPicture',
            'field' => 'AssetField',
            'limit' => 1
        ];

        return $fields;
    }
}