<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
| 
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function ()
{
    /*
     * Index page
     */
    Route::get('/', function ()
    {
        return view('welcome');
    });


    /*
     * Index page
     */
    Route::get('home', function ()
    {
        return view('welcome');

    });


    /*
    * login page
    */
    Route::resource('login', 'SessionController');
    Route::get('logout', 'SessionController@destroy');




    Route::group(['namespace' => 'Registration'], function()
    {
        /*
        * register page
        */
        Route::resource('register', 'RegisterController');

        /*
        * password page
        */
        Route::resource('password', 'PasswordController');
    });




    /*
     * Only auth user can access to /profile
     */
    Route::group(['namespace' => 'Profile', 'middleware' => 'auth'], function ()
    {
        Route::resource('/profile', 'ProfileController');
    });



    /*
     * Only auth user can access to /admin
     */
    Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function ()
    {

        Route::resource('/admin/ajax/members', 'AjaxMemberController');
        Route::resource('/admin/ajax/courts', 'AjaxCourtController');
        Route::resource('/admin/ajax/subscriptions', 'AjaxSubscriptionController');
        Route::resource('/admin/ajax/seasons', 'AjaxSeasonController');

        Route::resource('/admin/members', 'MemberController');

        Route::resource('/admin/config/courts', 'CourtController');
        Route::resource('/admin/config/seasons', 'SeasonController');
        Route::resource('/admin/config/subscriptions', 'SubscriptionController');

        Route::resource('/admin', 'AdminController');


    });

});
