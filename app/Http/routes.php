<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// home screen
$router->get('/', ['as' => 'home', 'uses' => 'FrontendController@getIndex']);
$router->get('slide', ['as' => 'slide', 'uses' => 'FrontendController@getSlide']);
$router->get('slide/image', ['as' => 'slide.image', 'uses' => 'FrontendController@getSlideImage']);
$router->get('slide/video', ['as' => 'slide.video', 'uses' => 'FrontendController@getSlideVideo']);
$router->get('slide/sidang', ['as' => 'slide.sidang', 'uses' => 'FrontendController@getSlideSidang']);
$router->get('slide5', ['as' => 'slide5', 'uses' => 'FrontendController@getSlide5']);

$router->get('frontend/post-by-case-type/{id?}', ['as'=>'frontend.post.byCaseType', 'uses'=>'FrontendController@getPost']);
$router->get('frontend/case/{id}', ['as' => 'cases.view', 'uses' => 'FrontendController@getCase']);

$router->controller('frontend', 'FrontendController', [
    'getSearch'  => 'frontend.search',
    'getOfficer'  => 'frontend.officer',
    'getSidang'  => 'frontend.sidang',
]);

// CMS a.k.a static page
$router->get('page/{category}/{id?}', ['as'=>'frontend.post', 'uses'=>'PostController@show']);

//BACKEND
$router->group(['prefix' => 'backend', 'namespace' => 'Backend', 'middleware' => 'auth'], function($router){

    $router->get('/', ['as' => 'admin.home', 'uses' => 'DefaultController@getIndex']);

    $router->resource('officers', 'OfficerController');

    $router->resource('cases', 'CaseController');
    $router->get('cases/edit/{id}', 'CaseController@edit');
    $router->get('cases/view/{id}', ['as' => 'backend.cases.view', 'uses' => 'CaseController@view']);
    $router->get('cases/{caseId}/check/{checklistId}', ['as' => 'backend.cases.checklist', 'uses' => 'CaseController@getChecklist']);
    $router->post('cases/{caseId}/check/{checklistId}', ['as' => 'backend.cases.checklist', 'uses' => 'CaseController@postChecklist']);
    $router->post('cases/{caseId}/uncheck/{checklistId}', ['as' => 'backend.cases.unchecklist', 'uses' => 'CaseController@postUnchecklist']);
    $router->get('cases/byJaksa/{id}', ['as'=>'backend.cases.byJaksa', 'uses' => 'CaseController@getByJaksa']);

    $router->post('cases/{caseId}/activities', ['as' => 'backend.cases.activity', 'uses' => 'CaseController@postActivity']);

    $router->delete('cases/{caseId}', ['as' => 'backend.cases.delete', 'uses' => 'CaseController@destroy']);

    $router->get('dashboard/index', ['as' => 'dashboard.index', 'uses' => 'DashboardController@getIndex']);
    $router->get('dashboard/byPhase', ['as' => 'dashboard.byPhase', 'uses' => 'DashboardController@getByPhase']);
    $router->get('dashboard/byStatus', ['as' => 'dashboard.byStatus', 'uses' => 'DashboardController@getByStatus']);
    $router->get('dashboard/byJaksa', ['as' => 'dashboard.byJaksa', 'uses' => 'DashboardController@getByJaksa']);

    $router->get('setting/index', ['as' => 'setting.index', 'uses' => 'SettingController@index']);
    $router->get('setting/sop', ['as' => 'setting.sop', 'uses' => 'SettingController@sop']);
    $router->post('setting', ['as' => 'setting.store', 'uses' => 'SettingController@store']);

    $router->resource('templates', 'TemplatesController');
    $router->resource('document', 'DocumentController');
    $router->resource('suspect', 'SuspectController');
    $router->resource('witness', 'WitnessController');

    $router->resource('user', 'UserController', ['only' => ['create', 'store', 'destroy']]);
    $router->get('user/reset-password', ['as' => 'backend.user.reset_password', 'uses' => 'UserController@resetPassword']);

    $router->resource('files', 'FilesController');
});

// GLOBAL ROUTE
// $router->get('login', 'SiteController@getLogin');
// $router->post('login', 'SiteController@postLogin');
// $router->get('logout', 'SiteController@getLogout');

// $router->get('kasus', 'HomeController@index');
// $router->get('statistik', 'HomeController@statistic');

/*
|--------------------------------------------------------------------------
| Authentication & Password Reset Controllers
|--------------------------------------------------------------------------
|
| These two controllers handle the authentication of the users of your
| application, as well as the functions necessary for resetting the
| passwords for your users. You may modify or remove these files.
|
*/

//$router->controller('auth', 'AuthController');

//$router->controller('password', 'PasswordController');
