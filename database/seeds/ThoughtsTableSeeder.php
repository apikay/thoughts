<?php

use Illuminate\Database\Seeder;
use Thoughts\Follower;
use Thoughts\Like;
use Thoughts\Searchable;
use Thoughts\Thought;
use Thoughts\User;

/**
 * Seeds thoughts in database.
 */
class ThoughtsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Known test facebook users
        $userA = factory(User::class)->create(['email' => 'open_lqwmgpj_user@tfbnw.net', 'facebook_id' => '111308292957826']);
        $userB = factory(User::class)->create(['email' => 'artenesama@gmail.com', 'facebook_id' => '1392574914180319']);

        factory(User::class, 4)->create()->each(function ($user) use ($userA, $userB) {

            factory(Thought::class, 30)->create(['user_id' => $user->id])->each(function ($thought) {

                factory(Like::class, random_int(1, 100))->create(['thought_id' => $thought->id]);

            });

            Follower::create($userA, $user);
            Follower::create($userB, $user);

        });

    }

}
