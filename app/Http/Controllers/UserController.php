<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * @return JsonResponse
     */
    public function store(StoreUser $request)
    {
        $validated_request = User::preProcess($request)["data"]["attributes"];
        unset($validated_request["password_confirmation"]);

        $user = User::create($validated_request);
        return (new UserResource($user->refresh()))
            ->response()
            ->header("Location", route("users.show", ["user" => $user]));
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
     * @return Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if ($user->image != "default.png") {
            Storage::delete("public/images/uploads/profile/$user->image");
        }
        $user->delete();
        return response(null, 204);
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

        $image_name = Str::random() . '.' . $request->file('image')->clientExtension();
        $request->file('image')->storeAs('public/images/uploads/profile', $image_name, ["visibility" => "public"]);


        $user->update(["image" => $image_name]);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function destroyImage(User $user)
    {
        $image_name = Str::afterLast($user->image, "/");

        if ($image_name == "default.png") {
            return response(null, 204);
        }

        Storage::delete("public/images/uploads/profile/".$image_name);
        $user->update(["image" => "default.png"]);

        return response(null, 204);
    }
}
