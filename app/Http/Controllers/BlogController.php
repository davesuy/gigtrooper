<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Facades\FieldFacade;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\Category;
use Gigtrooper\Models\Post;
use Gigtrooper\Models\Tag;
use Illuminate\Http\Request;

use Gigtrooper\Http\Requests;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Elements\PostElement;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Support\Str;

class BlogController extends Controller
{

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $fieldTypesInfo;
    private $handles = [];
    private $extraFields = [];

    public function __construct(
        PostElement $element, Request $request,
        ElementsService $elementService, FieldTypes $fieldTypes = null
    ) {
        $this->element = $element;
        $this->request = $request;
        $this->elementsService = $elementService;
        $this->handles = [
            'title', 'slug', 'subTitle', 'excerpt', 'body', 'Image', 'imageThumbnail', 'DatePublished', 'DateTimePublished',
            'DateExpiry', 'blogAuthor', 'Category', 'Tag'
        ];

        $this->extraFields['Status'] = $fieldTypes->getBlogStatus();

        $this->fieldTypes = $fieldTypes;

        $fieldTypesInfo = $this->fieldTypes->getFieldsByHandles($this->handles);

        $this->fieldTypesInfo = array_merge($fieldTypesInfo, $this->extraFields);;
    }

    public function blog($page = 1)
    {
        $postElement = $this->element;
        $request = $this->request;

        $baseUrl = '/blog/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 25;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($request->input('sort') != null) {
            $options['order'] = $request->input('sort');
        } else {
            $options['order'] = ['DateTimePublished-desc'];
        }

        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';

        $model = $postElement->initModel();

        \Criteria::setOptions($model, $options, $this->fieldTypesInfo);

        $posts = \Criteria::find()->all();

        $posts = $this->elementsService->getModelsWithFields($posts, $this->fieldTypesInfo);

        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => $baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        return view('blog/index',
            [
                'posts' => $posts,
                'categories' => [],
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl
            ]
        );
    }

    public function single($single)
    {
        $model = new Post();
        $post = $model->findByAttribute('slug', $single);

        $post->setFieldTypes($this->fieldTypesInfo);

        if ($post == null) {
            return redirect('blog');
        }

        $status = $post->getFieldValue('Status');

        $authorModels = $post->getFieldValue('blogAuthor');

        $author = null;

        if (!empty($authorModels)) {
            $author = $authorModels[0];
        }

        $user = \Auth::user();

        if ($user AND $status != 'live') {
            $roles = $user->roles;

            $preview = $this->request->input('preview');

            $redirect = true;

            if (
            ($status != 'live' AND $preview == true AND
                (\App::make('userService')->isPostAuthor($post) OR
                    in_array('administrator', $roles)))
            ) {
                $redirect = false;
            }

            if ($redirect) {
                return redirect('blog');
            }
        }

        $fieldCategories = $post->getFieldValue('Category');

        $categoryService = \App::make('categoryService');

        $categories = $categoryService->getRelatedCategories($fieldCategories);

        $nextPost = $this->getAdjacentPost($post);
        $prevPost = $this->getAdjacentPost($post, false);

        $url = config('app.url').'/blog/'.$post->slug;

        $shareBoxCookie = \Cookie::get('gigtrooper-blog-sharebox');

        $popularPosts = \App::make('postService')->getPopularPosts();

        return view('blog.single', [
            'url' => $url,
            'post' => $post,
            'categories' => $categories,
            'author' => $author,
            'nextPost' => $nextPost,
            'prevPost' => $prevPost,
            'shareBoxCookie' => $shareBoxCookie,
            'popularPosts' => $popularPosts
        ]);
    }

    public function categories($categorySlug, $page = 1)
    {
        $postElement = $this->element;
        $request = $this->request;

        $baseUrl = '/blog/categories/'.$categorySlug.'/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 10;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($request->input('sort') != null) {
            $options['order'] = $request->input('sort');
        } else {
            $options['order'] = ['DateTimePublished-desc'];
        }

        $category = Category::findByAttribute('slug', $categorySlug);

        if ($category == null) {
            redirect('login');
        }

        $categoryService = \App::make('categoryService');

        $categories = $categoryService->getRelatedCategories([$category]);

        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';
        $options['fields'][0]['relation'] = 'AND';

        $options['fields'][1]['handles'][0]['handle'] = "Category";
        $options['fields'][1]['handles'][0]['value'] = [$category->id];

        $model = $postElement->initModel();

        \Criteria::setOptions($model, $options, $this->fieldTypesInfo);

        $posts = \Criteria::find()->all();

        $posts = $this->elementsService->getModelsWithFields($posts, $this->fieldTypesInfo);

        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => $baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        return view('blog/index',
            [
                'posts' => $posts,
                'categories' => $categories,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl,
                'category' => $category
            ]
        );
    }


    public function tags($tagSlug, $page = 1)
    {
        $postElement = $this->element;
        $request = $this->request;

        $baseUrl = '/blog/tags/'.$tagSlug.'/';
        $currentUrl = $baseUrl.'page/'.$page;

        $limit = 10;

        $options = [];
        $options['limit'] = $limit;
        $options['page'] = $page;

        if ($request->input('sort') != null) {
            $options['order'] = $request->input('sort');
        } else {
            $options['order'] = ['DateTimePublished-desc'];
        }

        $tag = Tag::findByAttribute('value', $tagSlug);

        if ($tag == null) {
            redirect('login');
        }

        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';
        $options['fields'][0]['relation'] = 'AND';

        $options['fields'][1]['handles'][0]['handle'] = "Tag";
        $options['fields'][1]['handles'][0]['value'] = [$tag->value];

        $model = $postElement->initModel();

        \Criteria::setOptions($model, $options, $this->fieldTypesInfo);

        $posts = \Criteria::find()->all();

        $posts = $this->elementsService->getModelsWithFields($posts, $this->fieldTypesInfo);

        $query = \Criteria::getQuery();
        $total = \Criteria::getTotal();

        $paginationArgs = [
            'total' => $total,
            'limit' => $limit,
            'base' => $baseUrl,
            'currentPage' => $page
        ];

        \Pagination::setArgs($paginationArgs);

        $pagination = \Pagination::render();

        return view('blog/index',
            [
                'posts' => $posts,
                'page' => $page,
                'total' => $total,
                'pagination' => $pagination,
                'baseUrl' => $baseUrl,
                'query' => $query,
                'currentUrl' => $currentUrl,
                'categories' => [],
                'tag' => $tag
            ]
        );
    }

    private function getAdjacentPost(BaseModel $post, $next = true)
    {
        $operator = ($next) ? ">" : "<";

        $options = [];
        $options['order'] = 'id DESC';
        $options['LIMIT'] = 1;
        $options['fields'][0]['handles'][0]['handle'] = "Status";
        $options['fields'][0]['handles'][0]['value'] = 'live';
        $options['fields'][0]['relation'] = "AND";

        $options['fields'][1]['handles'][0]['handle'] = "id";
        $options['fields'][1]['handles'][0]['value'] = $post->id;
        $options['fields'][1]['handles'][0]['operator'] = $operator;

        \Criteria::setOptions($post, $options, $this->fieldTypesInfo);

        return \Criteria::find()->first();
    }
}
