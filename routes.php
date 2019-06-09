<?php
// Peter: Passing empty css and js parameters so we can perform an empty check when rendering the pages.
// if we dont perform a check we get errors that files are not found (404)
// to be honest I don't understand why we pass more than just the name of the view we need
// Should be disccussed
session_start();
// ini_set('display_errors', 1);

// we check if the route exist or not. If doesn't we redirect to 404 page;
if(Route::check_route_exist() === false ){
    Controller::NotExistingPage();
    exit;
}
Route::set('index.php', function (){

    Home::CreateView('home','');

// MORTY: i left this for testing purpose
// $test = new Users;
// $test->read_users();
});

Route::set('category', function (){
    Category::getCategory();
});

Route::set('sign-up', function (){
    UserController::CreateView('sign_up','');
   // Sign_up::test();
});

Route::set('verify', function (){
    UserController::verify_user();
});

Route::set('create-user', function (){
    UserController::create_user();
});

Route::set('login', function(){
    UserController::login_user();
});

// In the get, we need to pass additional array with the keys
// that we are expecting
// Peter: changed topic.php to topic. Just to have nicer urls
Route::set('topic', function (){
    SingleTopic::get_topic();
});

// TODO: 404 and other error handling
Route::set('error', function (){
    Controller::NotExistingPage();
});

Route::set('logout', function(){
    UserController::logout();
});

Route::set('create-topic', function (){
    // execute the create topic controller method
    if(isset($_SESSION['User'])){
        SingleTopic::create_topic_view();
    }else{
        Controller::CreateView('error', '');
    }

});

Route::set('api-create-topic', function (){
    // execute the create topic controller method
    
    if(isset($_SESSION['User'])){
        SingleTopic::crete_topic();
    }else{
        // TODO: change it to the JSON object with error
        Controller::CreateView('error' , '');

    }
});

Route::set('edit-topic', function (){
   if(isset($_SESSION['User'])){
       SingleTopic::edit_topic_view();
   }else{
       echo '{"status":"0","message":"Permission denied"}';
       die();
   }
});

Route::set('api-edit-topic', function (){
   if(isset($_SESSION['User'])){
       SingleTopic::edit_topic();
   }else{
       echo '{"status":"0","message":"Permission denied"}';
       die();
   }
});

Route::set('reply', function (){
    if(isset($_SESSION['User'])){
        Controller::CreateView('reply', '');
    }else{
        echo '{"status":"0","message":"Permission denied"}';
        die();
    }
 });
 
 Route::set('api-reply', function (){
    if(isset($_SESSION['User'])){
        ReplyController::addReply();
    }else{
        echo '{"status":"0","message":"Permission denied"}';
        die();
    }
 });

 // AdminController routes start here

Route::set('admin-panel', function (){
    if(UserPrivilegesChecker::is_admin()){
        AdminController::CreateView('admin/admin-panel', '');
    } else {
        AdminController::CreateView('error', '');
        die();
    }
});

Route::set('admin-users', function (){
    if(UserPrivilegesChecker::is_admin()){
        AdminController::CreateView('admin/user-management', '');
    } else {
        AdminController::CreateView('error', '');
        die();
    }

});

Route::set('admin-users-api', function (){
    if(UserPrivilegesChecker::is_admin()){
        AdminController::get_user();
    } else {
        AdminController::CreateView('error', '');
        die();
    }
});

Route::set('admin-users-update-api', function (){
    if(UserPrivilegesChecker::is_admin()){
        AdminController::update_user_basics();
    } else {
        AdminController::CreateView('error', '');
        die();
    }
});