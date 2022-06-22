<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Elements\PostElement;
use Gigtrooper\Models\Page;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\FieldTypes;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * @var FieldTypes
     */
    private $fieldTypes;

    private $elementsService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FieldTypes $fieldTypes = null, ElementsService $elementsService)
    {
        $this->fieldTypes = $fieldTypes;
        $this->elementsService = $elementsService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function display($slug)
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles(['title', 'slug', 'subTitle', 'excerpt', 'body']);
        $page = Page::findByAttribute('slug', $slug);

        if ($page == null) {
            throw new \Exception('Page was not created.');
        }

        $page->setFieldTypes($fieldTypes);

        return view('pages', [
            'page' => $page
        ]);
    }

    public function howItWorks()
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles(['title', 'slug', 'subTitle', 'excerpt', 'body']);
        $page = Page::findByAttribute('slug', 'how-it-works');

        if ($page == null) {
            throw new \Exception('Page was not created.');
        }

        $page->setFieldTypes($fieldTypes);

        return view('how-it-works', [
            'page' => $page
        ]);
    }

    public function aboutUs()
    {
        $fieldTypes = $this->fieldTypes->getFieldsByHandles(['title', 'slug', 'subTitle', 'excerpt', 'body']);
        $page = Page::findByAttribute('slug', 'about-us');

        if ($page == null) {
            throw new \Exception('Page was not created.');
        }

        $page->setFieldTypes($fieldTypes);

        return view('about-us', [
            'page' => $page
        ]);
    }

    public function contact()
    {
        return view('contact');
    }

    public function shareBox()
    {
        // 30 Days
        $minutes = (60 * 24) * 30;

        return response('Cookie Share Box Set')->cookie(
            'gigtrooper-blog-sharebox', 'here box', $minutes
        );
    }
}
