<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::all());
//            ->header("Accept", "application/vnd.api+json");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUser $request
     * @return UserResource
     */
    public function store(StoreUser $request)
    {
        $user = User::create(User::preProcess($request));
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource|JsonResponse
     */
    public function show(User $user)
    {
        //Mobile Users Should only have access to their accounts
        if (auth()->user()->type == "mobile" && $user->id != auth()->user()->id) {
            return response()->json(['error' => 'Not authorized.'], 403);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUser $request
     * @param User $user
     * @return Response
     */
    public function update(StoreUser $request, User $user)
    {
        $user->update(User::preProcess($request));
        return response($user, 201, ["Accept" => "application/vnd.api+json"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if ($user->image != "default.png") {
            Storage::delete("public/images/uploads/$user->image");
        }
        $user->delete();
    }
}
