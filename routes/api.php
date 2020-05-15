<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', 'UserController@index')->name("users.index");
    Route::get('users/{user}', 'UserController@show')->name("users.show");
    Route::post('users/{user}/image', 'UserController@storeImage')->name("users.storeImage");
    Route::delete('users/{user}/image', 'UserController@destroyImage')->name("users.destroyImage");

    Route::post('/logout', function () {
        return Auth::user()->currentAccessToken()->delete();
    });
});
Route::post('users', 'UserController@store');


Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required'
    ]);

    $user = User::where(['email'=> $request->email, "type" => "mobile"])->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
    $newToken = $user->createToken($request->device_name);
    dump($newToken->accessToken);
    dump($newToken->plainTextToken);

    return $newToken->plainTextToken;
});

//Route::apiResource('users', 'UserController');
