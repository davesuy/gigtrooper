<?php

namespace Gigtrooper\Services;

use Gigtrooper\Models\Post;

class PostService
{
    public function getRecentPosts()
    {
        $handles = ['title', 'slug', 'Status', 'excerpt', 'Image', 'imageThumbnail', 'DateTimePublished'];

        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';

        $options['limit'] = 4;
        $options['order'] = ['DateTimePublished-desc'];

        $model = new Post();

        $fieldTypes = \App::make('fieldTypes')->getFieldsByHandles($handles);

        \Criteria::setOptions($model, $options, $fieldTypes);

        $posts = \Criteria::find()->all();

        $posts = \App::make('elementsService')->getModelsWithFields($posts, $fieldTypes);

        return $posts;
    }

    public function getPopularPosts()
    {
        $handles = ['title', 'slug', 'Status', 'excerpt', 'Image', 'imageThumbnail', 'DateTimePublished', 'popularPost'];

        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';
        $options['fields'][0]['relation'] = 'AND';

        $slug = \Request::segment(2);

        if ($slug) {
            $options['fields'][0]['handles'][1]['handle'] = "slug";
            $options['fields'][0]['handles'][1]['value'] = $slug;
            $options['fields'][0]['handles'][1]['operator'] = "<>";
        }

        $options['limit'] = 6;
        $options['order'] = ['popularPost-desc'];

        $model = new Post();

        $fieldTypes = \App::make('fieldTypes')->getFieldsByHandles($handles);

        \Criteria::setOptions($model, $options, $fieldTypes);

        $posts = \Criteria::find()->all();

        $posts = \App::make('elementsService')->getModelsWithFields($posts, $fieldTypes);

        return $posts;
    }
}