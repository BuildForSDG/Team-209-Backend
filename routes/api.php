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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', 'UserController@index')->name("users.index");
    Route::get('users/{user}', 'UserController@show')->name("users.show");
    Route::patch('users/{user}', 'UserController@update')->name("users.update");
    Route::delete('users/{user}', 'UserController@destroy')->name("users.delete");
    Route::post('users/{user}/image', 'UserController@storeImage')->name("users.storeImage");
    Route::delete('users/{user}/image', 'UserController@destroyImage')->name("users.deleteImage");
    Route::get('users/{user}/relationships/reports', "UserReportsRelationshipController@index")
        ->name('users.relationships.reports');
    Route::get('users/{user}/reports', "UserController@relatedReports")->name('users.reports');

//    Route::get('tokens/{token}/relationships/users', '')->name("tokes.relationships.users");
//    Route::get('tokens/{token}/users', 'UserController@show')->name("tokes.users);

    Route::apiResource("incidents", "IncidentController");
    Route::get('incidents/{incident}/relationships/reports', "IncidentReportsRelationshipController@index")
        ->name('incidents.relationships.reports');
    Route::get('incidents/{incident}/reports', "IncidentController@relatedReports")
        ->name('incidents.reports');

    Route::apiResource("reports", "ReportController");
    Route::get('reports/{report}/users', "ReportController@relatedUser")
        ->name('reports.users');
    Route::get('reports/{report}/incidents', "ReportController@relatedIncident")
        ->name('reports.incidents');
    Route::get('reports/{report}/relationships/attachments', "ReportAttachmentsRelationshipController@index")
        ->name('reports.relationships.attachments');
    Route::get('reports/{report}/attachments', "ReportController@relatedAttachments")
        ->name('reports.attachments');

    Route::apiResource("attachments", "ReportAttachmentsController");
    Route::get('attachments/{attachment}/reports', "ReportAttachmentsController@relatedReport")
        ->name('attachments.reports');

    Route::post('/logout', function () {
        Auth::user()->currentAccessToken()->delete();
        return response(null, 204);
    })->name("api.logout");
});

//No Auth Region

Route::post('users', 'UserController@store')->name("users.store");


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

    return (new TokenResource($newToken))
        ->response()
        ->header("Content-Type", "application/vnd.api+json");
})->name("api.login");
