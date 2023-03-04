<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Account\AccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Account::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->where("name", "like", "%$search%");
        }
        $totalCount = $models->count();

        $models = $models->orderBy('name');
        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }
        $models = $models->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('accounts', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param AccountRequest $request
     * @return JsonResponse
     */
    public function store(AccountRequest $request): JsonResponse
    {
        $account = new Account($request->all());
        $account->save();

        return $this->resourceItemResponse('account', $account);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * @param AccountRequest $request
     * @param Account $account
     * @return JsonResponse
     */
    public function update(AccountRequest $request, Account $account): JsonResponse
    {
        $account->fill($request->all());
        $account->save();

        return $this->resourceItemResponse('account', $account);
    }

    /**
     * @param Account $account
     * @return JsonResponse
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();
        return $this->emptySuccessResponse();
    }
}
