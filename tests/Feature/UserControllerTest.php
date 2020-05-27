<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * Test User Creation and Response.
     * @test
     * @return void
     */
    public function userCreation()
    {
        $test_data["type"] = "users";
        $test_data["attributes"] = [
                                    "name"  => $this->faker->name,
                                    "email" => $this->faker->unique()->email,
                                    "password" => "Test-password!",
                                    "password_confirmation" => "Test-password!"
                                ];

        $response = $this->postJson(route("users.store"), [
            "data" => [
                "type"=> $test_data["type"],
                "attributes"=> $test_data["attributes"]
            ]
        ]);

        $response->assertCreated()
            ->assertHeader("Content-Type", "application/vnd.api+json")
            ->assertHeader("Location")
            ->assertJsonPath("data.type", $test_data["type"])
            ->assertJsonPath("data.attributes.name", $test_data["attributes"]["name"])
            ->assertJsonPath("data.attributes.email", $test_data["attributes"]["email"]);
    }

    /**
     * @test
     */
    public function singleUserFetch()
    {
        $user = factory(User::class)->create()->refresh();
        $this->getJson(route("users.show", ["user" => $user]))->assertStatus(401);

        Sanctum::actingAs($user, ["*"]);
        $response = $this->getJson(route("users.show", ["user" => $user]));

        $response->assertOk()
            ->assertHeader("Content-Type", "application/vnd.api+json")
            ->assertJsonPath("data.type", "users")
            ->assertJsonPath("data.id", strval($user->id))
            ->assertJsonPath("data.attributes.name", $user->name)
            ->assertJsonPath("data.attributes.email", $user->email)
            ->assertJsonPath("data.attributes.image", $user->image)
            ->assertJsonPath("data.attributes.type", $user->type)
            ->assertJsonPath("data.attributes.created_at", $user->created_at->toJson())
            ->assertJsonPath("data.attributes.updated_at", $user->updated_at->toJson());
    }

    /**
     * @test
     */
    public function allUsersFetch()
    {
        $users = factory(User::class, 5)->create();
        $this->getJson(route("users.show", ["user" => $users[0]]))->assertStatus(401);

        Sanctum::actingAs($users[0], ["*"]);
        $response = $this->getJson(route("users.index"));

        $response->assertOk()
            ->assertHeader("Content-Type", "application/vnd.api+json");

        $response_array = json_decode($response->content(), true)["data"];

        foreach ($users as $index => $user) {
            $user->refresh();
            $this->assertEquals($user->id, $response_array[$index]["id"]);
            $this->assertEquals("users", $response_array[$index]["type"]);
            $this->assertEquals($user->name, $response_array[$index]["attributes"]["name"]);
            $this->assertEquals($user->email, $response_array[$index]["attributes"]["email"]);
            $this->assertEquals($user->type, $response_array[$index]["attributes"]["type"]);
            $this->assertEquals($user->image, $response_array[$index]["attributes"]["image"]);
            $this->assertEquals($user->created_at->toJson(), $response_array[$index]["attributes"]["created_at"]);
            $this->assertEquals($user->updated_at->toJson(), $response_array[$index]["attributes"]["updated_at"]);
        }
    }

    /**
     * @test
     */
    public function userUpdate()
    {
        $user = factory(User::class)->create(["type" => "web"])->refresh();
        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email
        ]);

        $name = $this->faker->name;
        $email = $this->faker->unique()->email;

        $this->patchJson(route("users.update", ["user" => $user]))->assertStatus(401);
        Sanctum::actingAs($user, ["*"]);

        $response = $this->patchJson(route("users.update", ["user" => $user]), [
            "data" => [
                "id"=> strval($user->id),
                "type"=> "users",
                "attributes"=>[
                    "name" => $name,
                    "email" => $email
                ]
            ]
        ]);

        $response->assertOk()
            ->assertHeader("Content-Type", "application/vnd.api+json")
            ->assertJsonPath("data.type", "users")
            ->assertJsonPath("data.id", strval($user->id))
            ->assertJsonPath("data.attributes.name", $name)
            ->assertJsonPath("data.attributes.email", $email)
            ->assertJsonPath("data.attributes.image", $user->image)
            ->assertJsonPath("data.attributes.type", $user->type)
            ->assertJsonPath("data.attributes.created_at", $user->created_at->toJson());
    }

    /**
     * @test
     */
    public function userLogin()
    {
        $password = $this->faker->password(6, 20);
        $email = $this->faker->companyEmail;

        $user = factory(User::class)->create(
            [
                "type" => "mobile",
                "email" => $email,
                "password" => $password
            ]
        )->refresh();

        $this->postJson(route("api.login"), [
                "data" => [
                    "type"=> "users",
                    "attributes"=>[
                        "type" => "web",
                        "email" => $email,
                        "password"=> "12345678"
                    ]
                ]
            ])->assertStatus(422);

        $response = $this->postJson(route("api.login"), [
            "data" => [
                "type"=> "users",
                "attributes"=>[
                    "email" => $email,
                    "password"=> $password,
                    "device_name"=> "Test Mobile Pro"
                ]
            ]
        ]);

        $response->assertOk()
            ->assertHeader("Content-Type", "application/vnd.api+json")
            ->assertJsonPath("data.type", "tokens")
            ->assertJsonPath("data.id", "1")
            ->assertJsonPath("data.attributes.device_name", "Test Mobile Pro")
            ->assertJsonPath("data.attributes.abilities", ["*"])
            ->assertJsonPath("data.attributes.last_used", null)
            ->assertJsonPath("data.included.id", strval($user->id))
            ->assertJsonPath("data.included.type", "users")
            ->assertJsonPath("data.included.attributes.name", $user->name)
            ->assertJsonPath("data.included.attributes.email", $user->email)
            ->assertJsonPath("data.included.attributes.image", $user->image);
    }

    /**
     * @test
     */
    public function userLogout()
    {
        $password = $this->faker->password(6, 20);
        $email = $this->faker->companyEmail;

        $user = factory(User::class)->create(
            [
                "type" => "mobile",
                "email" => $email,
                "password" => $password
            ]
        );
        $this->assertDatabaseCount("personal_access_tokens", 0);
        $this->postJson(route("api.login"), [
            "data" => [
                "type"=> "users",
                "attributes"=>[
                    "email" => $email,
                    "password"=> $password,
                    "device_name"=> "Test Mobile Pro"
                ]
            ]
        ]);

        $this->assertDatabaseCount("personal_access_tokens", 1);

        $this->postJson(route("api.logout"))->assertStatus(401);

        Sanctum::actingAs($user, ["*"]);
        $this->postJson(route("api.logout"))->assertStatus(204);
    }

    /**
     *@test
     */
    public function userDelete()
    {
        $user = factory(User::class)->create();
        $this->assertDatabaseCount("users", 1);

        $this->deleteJson(route("users.delete", ["user" => $user]))->assertStatus(401);

        Sanctum::actingAs($user, ["*"]);
        $this->deleteJson(route("users.delete", ["user" => $user]))->assertStatus(204);
        $this->assertDatabaseCount("users", 0);
    }
}
