<?php

use App\Http\Resources\TokenResource;
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

//    Route::get('tokens/{token}/relationships/users', '')->name("tokes.relationships.users");
//    Route::get('tokens/{token}/users', 'UserController@show')->name("tokes.users);

    Route::post('/logout', function () {
        return Auth::user()->currentAccessToken()->delete();
    });
});
Route::post('users', 'UserController@store');


Route::post('/login', function (Request $request) {
    $validated_inputs = $request->validate([
        'data.attributes.email' => 'required|email',
        'data.attributes.password' => 'required',
        'data.attributes.device_name' => 'required'
    ])["data"]["attributes"];

    $user = User::where(['email'=> $validated_inputs["email"]])->first();
//    $user = User::where(['email'=> $validate_inputs["email"], "type" => "mobile"])->first();

    if (! $user || ! Hash::check($validated_inputs["password"], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
    $newToken = $user->createToken($validated_inputs["device_name"]);

    return new TokenResource($newToken);
});

//Route::apiResource('users', 'UserController');
