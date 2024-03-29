<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\User\UserRequest;
use App\Mail\ResetCredentials;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        if ($customerId = $request->get("customer_id")) {
            $models = $models->customer($customerId);
        } else {
            $models = $models->moderator();
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

        if ($permission = $request->get("permission")) {
            $user->setPermission($permission);
        }

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

        if ($permission = $request->get("permission")) {
            $user->setPermission($permission);
        }

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

    public function reset(User $user): JsonResponse
    {
        $password = Str::random();
        $user->password = $password;
        $user->save();

        Mail::to($user)->queue(new ResetCredentials($user, $password));
        return $this->emptySuccessResponse();
    }
}
