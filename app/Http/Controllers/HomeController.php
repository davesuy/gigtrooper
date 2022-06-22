<?php

namespace Gigtrooper\Http\Controllers;

use Gigtrooper\Http\Requests\HomeSearch;
use Gigtrooper\Models\MemberCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countryCode = \App::make('countryService')->getSessionCountry();

        $countriesDropdown = \App::make('countryService')->getCountriesDropDown($countryCode, 'homeSearch');

        $initModel = new MemberCategory();

        $categoryDropdown = \App::make('categoryService')->getCategorySearchDropdown($initModel);

        $recentPosts = \App::make('postService')->getRecentPosts();

        $memberCategories = \App::make('categoryService')->getHomeMemberCategories();

        //dd($memberCategories[0]->getFieldValue('memberCategoryImage'));
        return view('home', [
            'countriesDropdown' => $countriesDropdown,
            'categoryDropdown' => $categoryDropdown,
            'recentPosts' => $recentPosts,
            'memberCategories' => $memberCategories
        ]);
    }

    public function homeSearch(Request $request)
    {
        if (!empty($request->input('homeSearch'))) {
            $homeSearch = $request->input('homeSearch');

            $id = $homeSearch['memberCategory'];

            $countryCode = $homeSearch['countryCode'];

            $request->session()->put('countryCode', $countryCode);

            if ($id == null) {
                $slug = 'all';
            } else {
                $model = MemberCategory::find($id);
                $slug = $model->slug;
            }

            $url = "/search/members/$countryCode/$slug";

            return redirect($url);
        }
    }
}
