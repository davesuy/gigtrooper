<?php

namespace Gigtrooper\Helpers;

use Gigtrooper\Models\Category;
use Gigtrooper\Models\MemberCategory;
use Gigtrooper\Models\Post;
use Gigtrooper\Models\User;
use Gigtrooper\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateHelper
{
    public function setActive($end, $segment = 2)
    {
        return (\Request::segment($segment) == $end) ? 'class=active' : '';
    }

    public function getCategoryMenu($cssClass = '')
    {
        $categoryService = \App::make('categoryService');

        $post = new Post();
        $categoryService->setElement($post);

        $branch = $categoryService->getBranch();

        return $categoryService->getCategoryMenu($branch, '/blog/categories/', $cssClass);
    }

    public function getMemberCategoryMenu($cssClass = '')
    {
        $categoryService = \App::make('categoryService');

        $memberCategoryModel = new MemberCategory();
        //$user = new User();
        //$categoryService->setElement($user);

        $categoryService->setLevels(1);

        $branch = $categoryService->getBranch($memberCategoryModel);

        $countryCode = \App::make('countryService')->getSessionCountry();

        return $categoryService->getCategoryMenu($branch, "/search/members/$countryCode/", $cssClass);
    }

    public function getMetaText()
    {
        $countryCode = \Request::segment(3);

        $category = \Request::segment(4);

        $fields = \Request::get('f');

        $regionText = "";

        if (!empty(\Request::segment(5))) {
            $state = \Request::segment(5);

            $state = static::convertToTitle($state);

            $regionText = $state." ";
        }

        $memberCategory = MemberCategory::findByAttribute('slug', $category);


        $categoryText = 'Entertainers';

        if (!empty($memberCategory)) {
            $categoryText = $memberCategory->title;
        }

        $countryText = "";

        $currentCountry = \App::make('countryService')->getCountryNameByCode($countryCode);

        if (!empty($currentCountry)) {
            $countryText = " in ".$regionText.$currentCountry;
        }
        if ($memberCategory != null && !empty($memberCategory->getAttribute('metaTitle'))) {
            return $memberCategory->getAttribute('metaTitle').$countryText;
        } else {
            $metaTitle = "Hire ".$categoryText.$countryText;
        }

        return $metaTitle;
    }

    public function getCategorySideMenu($categories)
    {
        $categoryService = \App::make('categoryService');

        $post = new Post();

        $categoryService->setElement($post);
        $categoryService->setStatus(['live']);
        $branch = $categoryService->getBranch();

        return $categoryService->getCategorySideMenu($branch, '/blog/categories/', $categories);
    }

    public function getMemberCategorySideMenu($members)
    {
        $categoryService = \App::make('categoryService');

        $memberCategoryModel = new MemberCategory();

        $user = new User();

        $categoryService->setElement($user);

        $branch = $categoryService->getBranch($memberCategoryModel);

        $country = \Request::segment(3);

        return $categoryService->getCategorySideMenu($branch, "/search/members/$country/", $members);
    }

    public function getCategoryNestedMenu($memberCategory = [], $filterHandles = [])
    {
        $categoryService = \App::make('categoryService');

        $memberCategoryModel = new MemberCategory();

        //	$user = new User();
        //$categoryService->setElement($user);
        $categoryService->setFilterHandles($filterHandles);

        //	$categoryService->showTotal(true);

        $branch = $categoryService->getBranch($memberCategoryModel);

        $first = [];

        $first[0] = [
            'id' => 0,
            'title' => 'All',
            'slug' => 'all'
        ];

        if (empty($memberCategory)) {
            $memberCategory = ['ALL'];
        }

        $branch = array_merge($first, $branch);

        $country = \Request::segment(3);

        return $categoryService->getCategoryNestedMenu($branch, "/search/members/$country/", $memberCategory);
    }

    public function getMemberCategoryDropdown()
    {
        $categoryService = \App::make('categoryService');

        $memberCategoryModel = new MemberCategory();

        $branch = $categoryService->getBranch($memberCategoryModel);

        $categoryDisplay = $categoryService->getSelectDropdown($branch);

        $idValue = \Request::get('categoryId');

        return view('fields.categoryDropdown', [
            'name' => 'memberCategory',
            'key' => 'register',
            'idValue' => $idValue,
            'categoryDisplay' => $categoryDisplay,
            'title' => '',
            'text' => 'Choose a Category',
            'modelId' => null,
            'modelName' => null,
            'subfield' => false
        ]);
    }


    public function isUserRole($role)
    {
        return \App::make('userService')->isUserRole($role);
    }

    public function fbCommentCount($url)
    {
        $json = json_decode(file_get_contents('https://graph.facebook.com/?ids='.$url));

        return isset($json->$url->share->comment_count) ? $json->$url->share->comment_count : 0;
    }

    public function hideEmail($email)
    {
        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';

        $key = str_shuffle($character_set);
        $cipher_text = '';
        $id = 'e'.rand(1, 999999999);

        for ($i = 0; $i < strlen($email); $i += 1) {
            $cipher_text .= $key[strpos($character_set, $email[$i])];
        }

        $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';

        $script .= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';

        $script .= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';

        $script = "eval(\"".str_replace(["\\", '"'], ["\\\\", '\"'], $script)."\")";

        $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';

        return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
    }

    public function imageUrl($path)
    {
        /**
         * removes first forward slash
         */
        $path = substr($path, 1);

        $url = \Storage::url($path);

        return $url;
    }

    public function stripHtmlBreak($string)
    {
        $string = strip_tags($string);

        $string = trim(preg_replace('/\s\s+/', ' ', $string));

        return $string;
    }

    public function getFirstImage($post)
    {
        $firstImage = '';

        $imageObject = $this->getFirstImageObj($post);

        if (!empty($imageObject)) {
            $firstImage = TemplateHelper::imageUrl($imageObject->url);
        }

        return $firstImage;
    }

    public function getFirstImageObj($post)
    {
        $firstImage = null;

        if ($post->getFieldValue('Image')) {
            $image = $post->getFieldValue('Image');

            $firstImage = $image[0];

            $imageThumbnail = $post->getFieldValue('imageThumbnail');

            if (!empty($imageThumbnail)) {
                foreach ($image as $img) {
                    if ($imageThumbnail == $img->value) {
                        $firstImage = $img;
                    }
                }
            }
        }

        return $firstImage;
    }

    public static function convertToTitle($slug)
    {
        $separate = Str::slug($slug, " ");
        $title = Str::title($separate);

        return $title;
    }

    public function ampify($html='')
    {
        # Replace img, audio, and video elements with amp custom elements
        $html = str_ireplace(
            ['<img','<video','/video>','<audio','/audio>'],
            ['<amp-img','<amp-video','/amp-video>','<amp-audio','/amp-audio>'],
            $html
        );
        # Add closing tags to amp-img custom element
        $html = preg_replace('/<amp-img(.*?)>/', '<amp-img$1></amp-img>',$html);
        # Whitelist of HTML tags allowed by AMP
        $html = strip_tags($html,'<h1><h2><h3><h4><h5><h6><a><p><ul><ol><li><blockquote><q><cite><ins><del><strong><em><code><pre><svg><table><thead><tbody><tfoot><th><tr><td><dl><dt><dd><article><section><header><footer><aside><figure><time><abbr><div><span><hr><small><br><amp-img><amp-audio><amp-video><amp-ad><amp-anim><amp-carousel><amp-fit-rext><amp-image-lightbox><amp-instagram><amp-lightbox><amp-twitter><amp-youtube>');
        return $html;
    }

    public function removeInlineStyles($text)
    {
        $text = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $text);

        return $text;
    }
}