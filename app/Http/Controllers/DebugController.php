<?php

    namespace Gigtrooper\Http\Controllers;

    use Aws\AwsClient;
    use Aws\S3\S3Client;
    use Gigtrooper\Elements\PostElement;
    use Gigtrooper\Helpers\TemplateHelper;
    use Gigtrooper\Models\Message;
    use Gigtrooper\Models\Post;
    use Gigtrooper\Models\User;
    use Gigtrooper\Models\Role;
    use Gigtrooper\Models\Category;
    use Gigtrooper\Models\Date;
    use Gigtrooper\Models\Field;
    use Gigtrooper\Services\Neo4jRelationService;
    use Gigtrooper\Services\DateService;
    use Gigtrooper\Services\FieldTypes;
    use Gigtrooper\Services\CriteriaService;
    use Gigtrooper\Services\ElementsService;
    use Gigtrooper\Services\CategoryService;
    use Gigtrooper\Elements\UserElement;
    use Gigtrooper\Fields\ModelField;
    use Gigtrooper\Fields\BaseOptionsField;
    use Gigtrooper\Services\FileUploadService;
    use Illuminate\Support\Facades\Storage;
    use GraphAware\Neo4j\Client\ClientBuilder;

    class DebugController extends Controller
    {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('administrator');
    }

    public function transactions()
    {
        \Neo4jQuery::beginTransaction();
        $user = new User;
        $user->name = 'test one';
        $user->email = 'as w';
        \Neo4jQuery::addQuery($user->getSaveQuery(), $user->getAttributes());
        $user = new User;
        $user->name = 'test two';
        $user->email = 'test email@email.com';
        \Neo4jQuery::addQuery($user->getSaveQuery(), $user->getAttributes());

        $statements = \Neo4jQuery::addStatements();

        if (\Neo4jQuery::isError()) {
            \Neo4jQuery::rollback();
        } else {
            \Neo4jQuery::commit();
        }
    }

    public function testing(User $user, Neo4jRelationService $relation, Role $role, Category $category)
    {
        if (isset($_GET['delete'])) {
    //			$date = Date::find(2);
    //			$date->delete();

            $date = User::find(4);
            $date->delete();
        }

        if (isset($_GET['adminset'])) {
            /*			$user = new User;
                        $user->name = 'admin' . $_GET['adminset'];
                        $user->email = 'admin' . $_GET['adminset'] . '@admin.com';
                        $user->password = bcrypt('password');
                        $user->save();*/

            $userElement = new UserElement;
            $fieldTypes = \App::make('fieldTypes');

            $fieldData = $fieldTypes->getAllFieldTypes();
            $fieldData[] = [
                'handle' => 'password',
                'field' => 'ElementField'
            ];

            $fieldData[] = [
                'handle' => 'email',
                'field' => 'ElementField'
            ];

            $userElement->setFieldTypes($fieldData);

            \Field::setElement($userElement);

            $fields = [
                'name' => 'admin',
                'email' => 'admin@gigtrooper.com',
                'password' => bcrypt('password'),
                'Role' => 'administrator',
                'Status' => 'active'
            ];

            \Field::saveElementFields($fields);
            exit;
        }

        $firstMessageModel = new Message();
        //$firstMessageModel->setAttributes($firstMessage->getProperties());
        $firstMessageModel->title = 'asdfas';
        dd($firstMessageModel);
        //var_dump($queryString);
        /*if ($results->count())
        {
            foreach ($results as $result)
            {
                $node = $result['f'];
                $values = json_decode($node->value, true);

                if (!empty($values))
                {
                    foreach ($values as $key => $value)
                    {
                        $url = \TemplateHelper::imageUrl($value['url']);

                        try
                        {
                            $image = new \Imagick($url);
                        }
                        catch (\Exception $e)
                        {
                            continue;
                        }

                        $dimension = $image->getImageGeometry();

                        $value = array_merge($dimension, $value);
                        $values[$key] = $value;
                    }
                    //dd(json_encode($values, true));
                    //$node->value = $values;
                }
                else
                {
                    continue;
                }

                //$results = \Neo4jQuery::getResultSet($queryString);

                $res = $node->setProperty('value', json_encode($values))->save();
            }
            dd('done');
        }*/
    }

    public function blank()
    {
        $fieldTypes = \App::make('fieldTypes');
        if (isset($_GET['superAdmin'])) {
            $userElement = new UserElement();
            $fields = [];
            $fields['Role'] = [
                'handle' => 'Role',
                'generate' => true,
                'field' => 'DropdownField',
                'options' => [
                    [
                        'value' => 'superAdmin',
                        'label' => 'Super Admin'
                    ]
                ]
            ];
            $userElement->setFieldTypes($fields);

            $modelUser = User::findByAttribute('email', $_GET['superAdmin']);

            if (!$modelUser) return null;

            $userElement->setModel($modelUser);

            $request["Role"] = 'superAdmin';

            \Field::setElement($userElement);

           \Field::saveElementFields($request);
        }

        $element = new PostElement();

        $model = $element->initModel();

        $fieldTypes = $fieldTypes->getFieldsByHandles(['Category', 'dateCreated',
            'dateUpdated', 'Created', 'Updated']);

        $options = [];
        $options['limit'] = 60;
        $options['order'] = ['id-desc'];
//        $options['fields'][0]['handles'][0]['handle'] = "dateUpdated";
//        $options['fields'][0]['handles'][0]['value'] = [
//            'year' => null,
//            'month' => null,
//            'day' => 12
//        ];

        \Criteria::setOptions($model, $options, $fieldTypes);

        $posts = \Criteria::find()->all();
        $query = \Criteria::getQuery();
        echo $query . '<br />';
        foreach ($posts as $post) {
            $post->setFieldTypes($fieldTypes);
            $dateService = \App::make('dateTimeService');
            $time = $post->getFieldValue('dateUpdated');
            $format = $dateService->getDateByFormat($time);

            $time = $post->getFieldValue('Updated');
            $createFormat = $dateService->getDateByFormat($time);
            dump($post->title . ' - ' . $format . ' - ' . $createFormat);
        }
        dd('here');
        return view('debug.blank');
    }

    public function checkUser()
    {
        $element = new UserElement();

        $model = $element->initModel();

        $fieldTypes = \App::make('fieldTypes');

        $fieldTypes = $fieldTypes->getFieldsByHandles(['dateCreated',
            'dateUpdated', 'Created', 'Updated']);
        $skip = \Request::get('skip') ?: 0;
        $limit = \Request::get('limit') ?: 50;

        $options = [];
        $options['limit'] = $limit;
        $options['skip'] = $skip;
        $options['order'] = ['id-desc'];
//        $options['fields'][0]['handles'][0]['handle'] = "dateUpdated";
//        $options['fields'][0]['handles'][0]['value'] = [
//            'year' => null,
//            'month' => null,
//            'day' => 12
//        ];

        \Criteria::setOptions($model, $options, $fieldTypes);

        $users = \Criteria::find()->all();
        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();
        echo $query . '<br />';
        echo 'Count: ' . count($users)  . '<br />';
        foreach ($users as $user) {
            $user->setFieldTypes($fieldTypes);
            $dateService = \App::make('dateTimeService');
            $time = $user->getFieldValue('dateCreated');
            $format = $dateService->getDateByFormat($time);

            $time = $user->getFieldValue('dateUpdated');
            $updateFormat = $dateService->getDateByFormat($time);

            $time = $user->getFieldValue('Created');
            $createFormat = $dateService->getDateByFormat($time);

            dump($user->name . ' - ' . $format . ' - ' . $createFormat . ' - ' . $updateFormat);
        }
        dd('here');
        return view('debug.blank');
    }

    public function updateDateTime()
    {
        $skip = \Request::get('skip') ?: 0;
        $limit = \Request::get('limit') ?: 50;

        $fieldTypes = \App::make('fieldTypes');
        $fieldTypes = $fieldTypes->getFieldsByHandles(['Created', 'Updated', 'DatePublished']);

        $element = new PostElement();

        $model = $element->initModel();

        \Criteria::setOptions($model, ['limit' => $limit, 'skip' => $skip], $fieldTypes);

        $posts = \Criteria::find()->all();

        $postElements = \App::make('elementsService')->getModelsWithFields($posts, $fieldTypes);

        foreach ($postElements as $element) {
            $this->updateDate($element, 'Created', 'dateCreated');
            $this->updateDate($element, 'Updated', 'dateUpdated');
            $this->updateDate($element, 'DatePublished', 'DateTimePublished');
        }

        dd('Updated limit: ' . $limit . ' skip: ' . $skip);
    }

    public function updateDateTimeUser()
    {
        $skip = \Request::get('skip') ?: 0;
        $limit = \Request::get('limit') ?: 50;

        $fieldTypes = \App::make('fieldTypes');
        $fieldTypes = $fieldTypes->getFieldsByHandles(['Created', 'Updated', 'DatePublished']);

        $element = new UserElement();

        $model = $element->initModel();

        \Criteria::setOptions($model, ['order' => ['id-desc'], 'limit' => $limit, 'skip' => $skip], $fieldTypes);

        $posts = \Criteria::find()->all();

        $postElements = \App::make('elementsService')->getModelsWithFields($posts, $fieldTypes);

        foreach ($postElements as $element) {
            $user = new UserElement();
            $model = User::find($element->id);
            $this->updateDate($element, 'Created', 'dateCreated', $user, $model);
            $this->updateDate($element, 'Updated', 'dateUpdated', $user, $model);
        }

        dd('Updated limit: ' . $limit . ' skip: ' . $skip);
    }

    private function updateDate($element, $prevHandle, $handle, $post = null, $model = null)
    {
        if ($post == null) {
            $post = new PostElement();
        }

        if ($model == null) {
            $model = Post::find($element->id);
        }

        $created = $element->getFieldValue($prevHandle);

        if (!$created) return;

        $fields[] = [
            'handle' => $handle,
            'title' => 'Date Created',
            'field' => 'DateTimeField',
            //'hideYear' => true,
            //'hideMonth' => true,
            'hideDay' => true
        ];

        $post->setFieldTypes($fields);
        $post->setModel($model);

        $request[$handle] = date('j-M-Y H:i:s', $created);
        //$request[$handle] = date('j-M-Y H:i:s', time());

        \Field::setElement($post);

        $result = \Field::saveElementFields($request);
    }


    public function phpinfo()
    {
        echo phpinfo();
    }

    public function blankPost()
    {
        dd($_POST);
    }

    public function seedUsers()
    {
        for ($i = 1; $i <= 50; $i++) {
            $faker = \Faker\Factory::create();
            $createRand = $faker->dateTimeBetween('-5 years', '+5 years');
            $timestamp = $createRand->getTimestamp();
            $createYear = date('Y', $timestamp);
            $createMonth = date('n', $timestamp);
            $createDay = date('j', $timestamp);


            $updateRand = $faker->dateTimeBetween('-5 years', '+5 years');
            $timestamp = $updateRand->getTimestamp();
            $updateYear = date('Y', $timestamp);
            $updateMonth = date('n', $timestamp);
            $updateDay = date('j', $timestamp);


            $roles = ['administrator', 'one', 'two', 'three'];
            $rolesRand = array_rand($roles);

            $dropdowns = ['drop one', 'drop two', 'drop three'];
            $dropdownsRand = array_rand($dropdowns);

            $regions = ['cebu', 'bohol', 'manila'];
            $regionsRand = array_rand($regions);

            $skills = ['skill1', 'skill2', 'skill3', 'skill4'];

            $randomSkills = $this->getRandomArrayValues($skills);

            $talents = ['talent1', 'talent2', 'talent3'];
            $randomTalents = $this->getRandomArrayValues($talents);

            $categories = [1, 2, 3, 4, 5, 6];
            $categoriesRand = array_rand($categories);

            $fields = [
                "ROLE" => $roles[$rolesRand],
                "Dropdown" => $dropdowns[$dropdownsRand],
                "SKILLS" => $randomSkills,
                "Region" => $regions[$regionsRand],
                "talent" => $randomTalents,
                "SUBHEAD" => $faker->sentence,
                "name" => $faker->name,
                "email" => $faker->email,
                "fee" => $faker->numberBetween(300, 10000),
                "BODY" => $faker->paragraph,
                "categoryModel" => $categories[$categoriesRand]
            ];

            $userElement = new UserElement;
            $fieldData = $userElement->fieldTypes();
            $fieldData[] = [
                'handle' => 'email',
                'field' => 'ElementField',
                'element' => true
            ];

            $userElement->setFieldTypes($fieldData);

            \Field::setElement($userElement);

            $user = \Field::saveElementFields($fields);


            $dates = [
                'year' => $createYear,
                'month' => $createMonth,
                'day' => $createDay
            ];

            $dateService = new DateService;
            $dateService->initDate($user, $dates, 'CREATED');
            $dateService->createOne();

            $dates = [
                'year' => $updateYear,
                'month' => $updateMonth,
                'day' => $updateDay
            ];

            $dateService->initDate($user, $dates, 'UPDATED');
            $dateService->createOne();
        }
    }

    public function getRandomArrayValues($options)
    {
        $length = count($options);
        $number = rand(1, $length);

        $randomArrayItems = $this->getRandomArrayItems($options, $number);
        $randomValues = $this->getOptionValuesByKeys($randomArrayItems, $options);

        return $randomValues;
    }

    public function getRandomArrayItems($values, $number)
    {
        $randomItems = array_rand($values, $number);

        if (!is_array($randomItems)) {
            return [$randomItems];
        }

        return $randomItems;
    }

    public function getOptionValuesByKeys($keys, $options)
    {
        $values = [];

        foreach ($keys as $key) {
            $values[] = $options[$key];
        }

        return $values;
    }

    public function updateUsers($skip = 0)
    {
        $userModel = new User;
        $userElement = new UserElement;

        $options = [];
        $options['skip'] = $skip;
        $options['limit'] = 200;
        $options['order'] = 'id';

        \Criteria::setOptions($userModel, $options, []);

        $users = \Criteria::find()->all();

        $fieldData = $userElement->fieldTypes();

        $fieldData[] = [
            'handle' => 'email',
            'field' => 'ElementField',
            'element' => true
        ];

        $userElement->setFieldTypes($fieldData);

        if (!empty($users)) {
            foreach ($users as $user) {
                $faker = \Faker\Factory::create();
                $createRand = $faker->dateTimeBetween('-5 years', '+5 years');
                $timestamp = $createRand->getTimestamp();
                $createYear = date('Y', $timestamp);
                $createMonth = date('n', $timestamp);
                $createDay = date('j', $timestamp);


                $updateRand = $faker->dateTimeBetween('-5 years', '+5 years');
                $timestamp = $updateRand->getTimestamp();
                $updateYear = date('Y', $timestamp);
                $updateMonth = date('n', $timestamp);
                $updateDay = date('j', $timestamp);


                $roles = ['administrator', 'one', 'two', 'three'];
                $rolesRand = array_rand($roles);

                $dropdowns = ['drop one', 'drop two', 'drop three'];
                $dropdownsRand = array_rand($dropdowns);

                $regions = ['cebu', 'bohol', 'manila'];
                $regionsRand = array_rand($regions);

                $skills = ['skill1', 'skill2', 'skill3', 'skill4'];

                $randomSkills = $this->getRandomArrayValues($skills);

                $talents = ['talent1', 'talent2', 'talent3'];
                $randomTalents = $this->getRandomArrayValues($talents);

                $categories = [1, 2, 3, 4, 5, 6];
                $categoriesRand = array_rand($categories);

                $fields = [
                    //"ROLE"     => $roles[$rolesRand],
                    //"Dropdown" => $dropdowns[$dropdownsRand],
                    //"SKILLS"   => $randomSkills,
                    //"Region"   => $regions[$regionsRand],
                    //"talent"   => $randomTalents,
                    //"SUBHEAD"  => $faker->sentence,
                    "name" => $faker->name,
                    //"email"    => $faker->email,
                    //"fee"      => $faker->numberBetween(300, 10000),
                    //"BODY"     => $faker->paragraph,
                    //"categoryModel" => $categories[$categoriesRand]
                ];


                $userElement->findModel($user->id);
                \Field::setElement($userElement);

                $user = \Field::saveElementFields($fields);
            }
        }
    }

    public function queueSeed()
    {
        return view('debug.queueseed');
    }
}