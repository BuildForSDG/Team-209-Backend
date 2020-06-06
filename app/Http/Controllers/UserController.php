<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Http\Resources\ReportCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        /** @phpstan-ignore-next-line */
        $users = QueryBuilder::for(User::class)->allowedSorts([
            "name",
            "email",
            "type",
            "created_at",
            "updated_at"
        ])->allowedIncludes('reports')->jsonPaginate();

        return (new UserCollection($users))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUser $request
     * @return JsonResponse
     */
    public function store(StoreUser $request)
    {
        $processed_request = User::preProcess($request->validated());

        $user = User::create($processed_request);

        return (new UserResource($user->refresh()))
            ->response()
            ->header("Content-Type", "application/vnd.api+json")
            ->header("Location", route("users.show", ["user" => $user]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $user
     * @return UserResource|JsonResponse
     */
    public function show($user)
    {
        $query = QueryBuilder::for(User::where('id', $user))
            ->allowedIncludes('reports')
            ->firstOrFail();

        return (new UserResource($query))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUser $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUser $request, User $user)
    {
        $user->update(User::preProcess($request->validated()));

        return (new UserResource($user->refresh()))
            ->response()
            ->header("Content-Type", "application/vnd.api+json");
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
        $image_name = Str::afterLast($user->image, "/");

        if ($image_name != "default.png") {
            $this->destroyImage($user);
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

        $image_name = Str::afterLast($user->image, "/");

        if ($image_name != "default.png") {
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

    public function relatedReports(User $user)
    {
        return new ReportCollection($user->reports);
    }
}
