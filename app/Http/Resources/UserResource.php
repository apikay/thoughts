<?php

namespace Thoughts\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

/**
 * User resource.
 *
 * @package Thoughts\Http\Resources
 */
class UserResource extends Resource
{

    /**
     * @var boolean
     */
    protected $withPseudonym = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request = null)
    {

        return [

            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'username' => $this->username,
                'followers' => $this->followers->count(),
                'avatar' => $this->avatar,
            ],
            'meta' => [
                'is_authenticated' => $this->isAuthenticatedUser(),
                'following' => $this->isFollowing(),
            ],
            'pseudonym' => $this->when($this->withPseudonym, function () {

                return [
                    'id' => $this->pseudonym->id,
                    'name' => $this->pseudonym->name,
                    'username' => $this->pseudonym->username,
                    'avatar' => $this->pseudonym->avatar,
                ];

            })

        ];

    }

    /**
     * @return $this
     */
    public function withPseudonym()
    {

        $this->withPseudonym = true;

        return $this;

    }

    /**
     * Checks if it is the authenticated user or its pseudonym.
     *
     * @return bool
     */
    protected function isAuthenticatedUser()
    {

        if (!Auth::check())
            return false;

        $isAuthenticatedUser = Auth::user()->id == $this->id;
        $isAuthenticatedUserPseudonym = Auth::user()->id == $this->real_id;
        $isAuthenticatedUserRealSelf = Auth::user()->real_id == $this->id;

        return $isAuthenticatedUser || $isAuthenticatedUserPseudonym || $isAuthenticatedUserRealSelf;

    }

    /**
     * Checks if the authenticated user is following this user.
     *
     * @return bool
     */
    protected function isFollowing()
    {

        if (!Auth::check())
            return false;

        return Auth::user()->following->where('id', $this->id)->first() !== null;

    }

}
