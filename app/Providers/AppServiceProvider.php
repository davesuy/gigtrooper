<?php

namespace Gigtrooper\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Auth::provider('neo4jAuthGig', function($app, array $config) {
            $label = $config['model'];;
            return new \Gigtrooper\Extensions\Neo4jAuthProvider($app['hash'], $label);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind("neo4jRelation", function() {
            return new \Gigtrooper\Services\Neo4jRelationService;
        });

        $this->app->bind("neo4jCriteria", function() {
            return new \Gigtrooper\Services\Neo4jCriteriaService;
        });

        $this->app->bind("field", function() {
            return new \Gigtrooper\Services\FieldService;
        });

        $this->app->bind("criteria", function() {
            return new \Gigtrooper\Services\CriteriaService;
        });

        $this->app->bind("pagination", function() {
            return new \Gigtrooper\Services\PaginationService;
        });

        $this->app->bind("templateHelper", function() {
            return new \Gigtrooper\Helpers\TemplateHelper();
        });

        $this->app->bind("categoryService", function() {
            return new \Gigtrooper\Services\CategoryService();
        });

        $this->app->bind("subFieldService", function() {
            return new \Gigtrooper\Services\SubField();
        });

        $this->app->bind("fileUploadService", function() {
            return new \Gigtrooper\Services\FileUploadService();
        });

        $this->app->bind("dateService", function() {
            return new \Gigtrooper\Services\DateService();
        });

        $this->app->bind("dateTimeService", function() {
            return new \Gigtrooper\Services\DateTimeService();
        });

        $this->app->bind("userService", function() {
            return new \Gigtrooper\Services\UserService();
        });

        $this->app->bind("elementsService", function() {
            return new \Gigtrooper\Services\ElementsService();
        });

        $this->app->bind("fieldTypes", function() {
            return new \Gigtrooper\Services\FieldTypes();
        });

        $this->app->bind("countryService", function() {
            return new \Gigtrooper\Services\CountryService();
        });

        $this->app->bind("postService", function() {
            return new \Gigtrooper\Services\PostService();
        });

        $this->app->bind("messageChainService", function() {
            return new \Gigtrooper\Services\MessageChainService();
        });

        $this->app->bind("messageFormService", function() {
            return new \Gigtrooper\Services\MessageForm();
        });
    }
}
