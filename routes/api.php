<?php
// routes/api.php

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

require 'api/v1/users/auth.php';
require 'api/v1/users/homepage.php';
require 'api/v1/users/category.php';
require 'api/v1/users/product.php';
require 'api/v1/users/search.php';
require 'api/v1/users/cart.php';
require 'api/v1/users/profile.php';
require 'api/v1/users/checkout.php';     
require 'api/v1/stripe/webhook.php';     
require 'api/v1/users/order.php'; 



Route::get('/test',function (Request $reqeust){

    $id = $reqeust->query('cate');
    
    $category = Category::findOrFail($id);

    $parent=$category->parent ?? $category ;
    while($parent?->parent){
        $parent = $parent->parent;
    }

    return $parent;


});


// routes/web.php
Route::get('/test-mailtrap', function () {
    try {
        // Mail::raw('Mailtrap connectivity test - ' . now(), function ($message) {
        //     $message->to('test@example.com')
        //             ->subject('Mailtrap Test ' . date('Y-m-d H:i:s'));
        // });
        
        $randomText=Str::random(5);

        $user = User::create([
            'name' =>"test",
            'email' => "test{$randomText}@example.com",
            'password' => Hash::make('password'),
        ]);

        event(new Registered($user));

        return '✅ Test email sent! Check your Mailtrap inbox.';
    } catch (\Exception $e) {
        return '❌ Failed: ' . $e->getMessage();
    }
});