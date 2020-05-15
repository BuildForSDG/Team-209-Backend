<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
     * @return UserResource
     */
    public function update(StoreUser $request, User $user)
    {
        $user->update(User::preProcess($request));
        return new UserResource($user);
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
            Storage::delete("public/images/uploads/profile/$user->image");
        }
        $user->delete();
    }

    /**
     * Store a Profile image for user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     * @throws \Exception
     */
    public function storeImage(Request $request, User $user)
    {
        $request->validate([
            'image' => ['required','image','mimes:jpeg,png,jpg,svg','max:1024']
        ]);

        if ($user->image != "default.png") {
            $this->destroyImage($user);
        }

        $image_name = time() . '.' . $request->file('image')->clientExtension();
        $request->file('image')->storeAs('public/images/uploads/profile', $image_name, ["visibility" => "public"]);


        $user->update(["image" => $image_name]);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function destroyImage(User $user)
    {
        $image_name = explode("/", $user->image);
        $image_name = end($image_name);

        if ($image_name == "default.png") {
            return;
        }

        Storage::delete("public/images/uploads/profile/".$image_name);

        $user->update(["image" => "default.png"]);
    }
}

