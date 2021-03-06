<?php

namespace Thoughts\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Thoughts\Http\Requests\StoreThoughtRequest;
use Thoughts\Http\Resources\ThoughtsCollection;
use Thoughts\Searchable;
use Thoughts\Thought;

/**
 * Thoughts controller.
 *
 * @package Thoughts\Http\Controllers
 */
class ThoughtsController extends Controller
{


    /**
     * Browse thoughts.
     *
     * @param string $filter
     * @return ThoughtsCollection
     */
    public function index($filter = 'latest')
    {

        if ($filter === 'popular') {

            $thoughts = Thought::popular()->paginate();

        } else {

            $thoughts = Thought::with('likes')->latest()->paginate();

        }

        return (new ThoughtsCollection($thoughts))->withUser();

    }

    /**
     * Finds a user thoughts.
     *
     * @param Request $request
     * @return ThoughtsCollection
     */
    public function find(Request $request, $id = null)
    {

        $likes = new Thought();

        $user = $this->resolveUser($id);

        $thoughts = $likes->findUserThoughts($user, $request->get('s'));

        return new ThoughtsCollection($thoughts);

    }

    /**
     * Store a new thought.
     *
     * @param StoreThoughtRequest $request
     * @return JsonResponse
     */
    public function store(StoreThoughtRequest $request)
    {

        $thought = new Thought(['body' => $request->get('body')]);

        $user = $request->get('as_pseudonym', false) ? Auth::user()->pseudonym : Auth::user();

        $thought->postBy($user);

        (new Searchable())->indexResource($thought);

        return response()->json($thought->toArray(), Response::HTTP_CREATED);

    }

}
