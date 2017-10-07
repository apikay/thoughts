<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Tests\TestCase;
use Thoughts\Like;
use Thoughts\Thought;
use Thoughts\User;

/**
 * Test to see if user can search for its likes and other users likes.
 *
 * @package Tests\Feature
 */
class UserCanSearchAUserLikesTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function user_dont_need_to_be_loged_in_to_search_an_user_likes()
    {

        $user = factory(User::class)->create();

        $this->getJson("v1/likes/user/{$user->id}")->assertStatus(Response::HTTP_OK);

    }

    /** @test */
    public function search_other_user_likes()
    {

        $user = factory(User::class)->create();
        $thought = factory(Thought::class)->create(['user_id' => $user->id, 'body' => 'Some random thought']);
        factory(Like::class)->create(['user_id' => $user->id, 'thought_id' => $thought->id]);
        factory(Like::class, 10)->create(['user_id' => $user->id]);
        factory(Like::class, 10)->create();

        $response = $this->getJson("v1/likes/user/{$user->id}?s=random thou");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json()['data']);
        $response->assertJson([
            'data' => [
                ['body' => $thought->body]
            ]
        ]);

        $response = $this->getJson("v1/likes/user/{$user->id}");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(11, $response->json()['data']);

    }

    /** @test */
    public function search_its_own_likes()
    {

        $user = factory(User::class)->create();
        $thought = factory(Thought::class)->create(['user_id' => $user->id, 'body' => 'Some random thought']);
        factory(Like::class)->create(['user_id' => $user->id, 'thought_id' => $thought->id]);
        factory(Like::class, 10)->create(['user_id' => $user->id]);
        factory(Like::class, 10)->create();

        $response = $this->actingAs($user)->getJson("v1/likes/user?s=random thou");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json()['data']);
        $response->assertJson([
            'data' => [
                ['body' => $thought->body]
            ]
        ]);

        $response = $this->actingAs($user)->getJson("v1/likes/user");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(11, $response->json()['data']);

    }

    /** @test */
    public function returns_404_when_user_is_not_loged_in_and_no_user_is_provided_in_route_parameter()
    {

        $this->withExceptionHandling()->getJson('v1/likes/user')->assertStatus(Response::HTTP_NOT_FOUND);

    }

}
