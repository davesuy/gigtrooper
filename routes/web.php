<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');
Route::post('/homeSearch', 'HomeController@homeSearch');


Route::get('testing', 'DebugController@testing');

Route::get('testing-blank', 'DebugController@blank');

Route::get('testing-phpinfo', 'DebugController@phpinfo');
//Route::post('blank', 'DebugController@blankPost');

Route::get('fileupload', 'FileUploadController@upload');
Route::post('fileupload', 'FileUploadController@upload');
Route::delete('fileupload', 'FileUploadController@upload');

Route::get('richtextimages', 'RichTextImageController@images');

Route::get('taghandler', 'TagHandlerController@getTags');

$prefix = config('filesystems.prefix');

Route::get("$prefix/{label}/{id}/{handle}/{thumb?}/{filename?}", function(
    $label, $id, $handle, $thumb = null,
    $filename = null
) {
    $prefix = config('filesystems.prefix');

    $filename = urldecode($filename);

    $path = storage_path("app/public/$prefix/")."$label/$id/$handle/$thumb/$filename";

    if ($filename == null) {
        $path = storage_path("app/public/$prefix/")."$label/$id/$handle/$thumb";
    }

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "administrator"], function() {
   // Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin"], function() {
    Route::resource('users', 'UserController');

    // For pagination
    Route::get('users/page/{page}', 'UserController@index');

    //Route::post("users/deletes", "UserController@deletes");
    Route::post("users/actions", "UserController@actions");

    Route::post("users/sortElements", "UserController@sortElements");

    Route::post("users/filterElements", "UserController@filterElements");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "administrator"], function() {
    Route::resource('pages', 'PageController');
    // For pagination
    Route::get('pages/page/{page}', 'PageController@index');

    Route::post("pages/deletes", "PageController@deletes");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "web"], function() {
    Route::resource('dashboard', 'DashboardController');
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "administrator"], function() {

    Route::resource('article', 'ArticleController');

    // For pagination
    Route::get('article/page/{page}', 'ArticleController@index');

    Route::post("article/deletes", "ArticleController@deletes");

    Route::post("article/sortElements", "ArticleController@sortElements");

    Route::post("article/filterElements", "ArticleController@filterElements");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "blogger"], function() {
    Route::resource('categories', 'CategoryController');

    // For pagination
    Route::get('categories/page/{page}', 'CategoryController@index');

    Route::post("categories/deletes", "CategoryController@deletes");

    Route::post("categories/sortElements", "CategoryController@sortElements");

    Route::post("categories/filterElements", "CategoryController@filterElements");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "administrator"], function() {
    Route::resource('member-categories', 'MemberCategoryController');

    // For pagination
    Route::get('member-categories/page/{page}', 'MemberCategoryController@index');

    Route::post("member-categories/deletes", "MemberCategoryController@deletes");

    Route::post("member-categories/sortElements", "MemberCategoryController@sortElements");

    Route::post("member-categories/filterElements", "MemberCategoryController@filterElements");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "blogger"], function() {
    Route::resource('blog', 'BlogController');

    // For pagination
    Route::get('blog/page/{page}', 'BlogController@index');

    Route::post("blog/deletes", "BlogController@deletes");

    Route::post("blog/sortElements", "BlogController@sortElements");

    Route::post("blog/filterElements", "BlogController@filterElements");
});

Route::group(["prefix" => \Config::get('app.cp'), "namespace" => "Admin", "middleware" => "administrator"], function() {
    Route::resource('countries', 'CountryController');

    // For pagination
    Route::get('countries/page/{page}', 'CountryController@index');

    Route::post("countries/deletes", "CountryController@deletes");

    Route::post("countries/sortElements", "CountryController@sortElements");

    Route::post("countries/filterElements", "CountryController@filterElements");

    Route::get("quotes", "QuotesController@index");
    // For pagination
    Route::get('quotes/page/{page}', 'QuotesController@index');

    Route::post("quotes/actions", "QuotesController@actions");
    Route::post("quotes/store/{id}", "QuotesController@store");
    Route::post("quotes/filter", "QuotesController@filter");
    Route::get("quotes/{id}", "QuotesController@view");
});

Route::group(["prefix" => "account", "namespace" => "Admin", "middleware" => "member"], function() {

    Route::get('profile', 'ProfileController@profile');
    Route::get('profile/delete', 'ProfileController@delete');
    Route::post('profile/delete', 'ProfileController@deleteAccount');

    Route::put("profile/update", "ProfileController@update");
    Route::get("messages", "MessagesController@index");
    Route::get("messages/{id}", "MessagesController@view");

    Route::post("messages/send/{id}", "MessagesController@send");

    Route::get("login-as-user/{userId}", "UserController@loginAsUser");
});

Route::post("profile/subfields", '\Gigtrooper\Http\Controllers\Admin\ProfileController@subFields');
Route::post("profile/sharebox", '\Gigtrooper\Http\Controllers\Admin\ProfileController@shareBox');
Route::post("blog/single", '\Gigtrooper\Http\Controllers\PagesController@shareBox');

Route::get("register/verify/{token}", "Auth\RegisterController@verify");

Route::group(['middleware' => 'web'], function() {
    Route::get('contact-us', ['as' => 'contact.get', 'uses' => '\Gigtrooper\Http\Controllers\ContactFormController@index']);
    Route::post('contact-us', ['as' => 'contact.post', 'uses' => '\Gigtrooper\Http\Controllers\ContactFormController@post']);
});

Route::post('login-quote', ['as' => 'loginQuote', 'uses' => 'Auth\LoginController@loginQuote']);

Route::get('login/facebook', 'Auth\LoginController@redirectToProvider');
Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallback');

Route::get("blog", "BlogController@blog");
Route::get("blog/page/{page}", "BlogController@blog");
Route::get("blog/{slug}", "BlogController@single");


Route::get("blog/categories/{categorySlug}", "BlogController@categories");
Route::get("blog/categories/{categorySlug}/page/{page}", "BlogController@categories");

Route::get("blog/tags/{tagSlug}", "BlogController@tags");
Route::get("blog/tags/{tagSlug}/page/{page}", "BlogController@tags");

Route::get("search/members", function() {
    return redirect('/search/members/all/all');
});

//Route::get("search/members/{country}/{category}", "SearchMembersController@index");
//Route::get("search/members/{country}/{category}/page/{page}", "SearchMembersController@index");

Route::get("search/members/{country}/{category}/{region?}", "SearchMembersController@index");
Route::get("search/members/{country}/{category}/{region}/page/{page}", "SearchMembersController@index");

Route::post('search/members/changeCountry', "SearchMembersController@changeCountry");
Route::post("search/members/filterElements", "SearchMembersController@filterElements");
Route::post("search/members/filterRegion", "SearchMembersController@filterRegion");
Route::post("search/members/sortElements", "SearchMembersController@sortElements");
Route::post("request-quote/quote", "RequestQuoteController@submit");
Route::post("request-quote/removeProvider", "RequestQuoteController@removeProvider");

Route::get("/request-quote/", "RequestQuoteController@requestQuote");
Route::get("/add-provider/{memberId}", "RequestQuoteController@addProvider");

Route::get("/{categorySlug}/{memberSlug}", "SearchMembersController@view");

Route::get("how-it-works", "PagesController@howItWorks");
Route::get("about-us", "PagesController@aboutUs");
Route::get("{slug}", "PagesController@display");
