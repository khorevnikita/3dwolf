<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = User::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->search($search);
        }
        $totalCount = $models->count();

        $models = $models->orderBy('name');
        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }
        $models = $models->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('users', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = new User($request->all());
        $user->save();

        return $this->resourceItemResponse('user', $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $user->fill($request->all());
        $user->save();

        return $this->resourceItemResponse('user', $user);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->emptySuccessResponse();
    }
}
