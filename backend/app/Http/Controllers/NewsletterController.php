<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Newsletter\NewsletterRequest;
use App\Models\Customer;
use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $newsletters = Newsletter::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $newsletters = $newsletters->where(function ($q) use ($search) {
                $q->where("subject", "like", "%$search%")
                    ->orWhere("text", "like", "%$search%");
            });
        }

        switch ($request->get('status')) {
            case "draft":
                $newsletters = $newsletters->draft();
                break;
            case "process":
                $newsletters = $newsletters->inProccess();
                break;
            case "sent":
                $newsletters = $newsletters->sent();
                break;
        }

        $totalCount = $newsletters->count();

        list($sort, $sortDir) = Paginator::getSorting($request);
        $newsletters = $newsletters->orderBy($sort, $sortDir);

        list($page, $skip, $take) = Paginator::get($request);
        if ($take > 0) {
            $newsletters = $newsletters->skip($skip)->take($take);
        }

        $newsletters = $newsletters->withCount(['customers'])->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('newsletters', $newsletters, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsletterRequest $request): JsonResponse
    {
        $push = new Newsletter($request->all('subject', 'text'));
        $push->status = Newsletter::STATUSES[0];
        $push->save();

        $push->loadCount(['customers']);
        return $this->resourceItemResponse('newsletter', $push);
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter, Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);

        $newsletter->loadCount(['customers']);
        $newsletter->load(['customers' => function ($q) use ($skip, $take) {
            $q->orderBy("sent_at")->orderBy("customers.id")->skip($skip)->take($take);
        }]);

        $pagesCount = Paginator::pagesCount($take, $newsletter->customers_count);

        return $this->resourceItemResponse('newsletter', $newsletter, ['pagesCount' => $pagesCount]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsletterRequest $request, Newsletter $newsletter): JsonResponse
    {
        if (!$newsletter->editable) abort(401);
        $newsletter->fill($request->all('subject', 'text'));
        $newsletter->save();

        $newsletter->loadCount(['customers']);

        return $this->resourceItemResponse('newsletter', $newsletter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter): JsonResponse
    {
        if (!$newsletter->editable) abort(401);
        $newsletter->delete();
        return $this->emptySuccessResponse();
    }

    /**
     * @param Newsletter $newsletter
     * @param Request $request
     * @return JsonResponse
     */
    public function availableReceivers(Newsletter $newsletter, Request $request): JsonResponse
    {
        $customers = Customer::query()->whereDoesntHave('newsletters', function ($q) use ($newsletter) {
            $q->where("newsletters.id", $newsletter->id);
        });

        if ($request->has('search')) {
            $customers = $customers->search($request->get('search'));
        }

        $totalCount = $customers->count();

        list($page, $skip, $take) = Paginator::get($request);
        if ($take > 0) {
            $customers = $customers->skip($skip)->take($take);
        }

        $customers = $customers->get();

        $pagesCount = Paginator::pagesCount($take, $totalCount);

        return $this->resourceListResponse('customers', $customers, $totalCount, $pagesCount);
    }

    /**
     * @param Newsletter $newsletter
     * @param Request $request
     * @return JsonResponse
     */
    public function attachAll(Newsletter $newsletter, Request $request): JsonResponse
    {
        $customers = Customer::query()->whereDoesntHave('newsletters', function ($q) use ($newsletter) {
            $q->where("newsletters.id", $newsletter->id);
        });

        $filter = $request->get('filter');
        if (isset($filter->search)) {
            $customers = $customers->search($filter->search);
        }

        $newsletter->customers()->syncWithoutDetaching($customers->pluck("id"));

        return $this->emptySuccessResponse();
    }

    /**
     * @param Newsletter $newsletter
     * @param Customer $customer
     * @return JsonResponse
     */
    public function attachCustomer(Newsletter $newsletter, Customer $customer): JsonResponse
    {
        $newsletter->customers()->syncWithoutDetaching($customer->id);
        return $this->emptySuccessResponse();
    }


    /**
     * @param Newsletter $newsletter
     * @param Request $request
     * @return JsonResponse
     */
    public function attachedReceivers(Newsletter $newsletter, Request $request): JsonResponse
    {
        $customers = Customer::query()->whereHas('newsletters', function ($q) use ($newsletter) {
            $q->where("newsletters.id", $newsletter->id);
        });

        $totalCount = $customers->count();

        list($page, $skip, $take) = Paginator::get($request);
        if ($take > 0) {
            $customers = $customers->skip($skip)->take($take);
        }

        $customers = $customers->get();

        $pagesCount = Paginator::pagesCount($take, $totalCount);

        return $this->resourceListResponse('customers', $customers, $totalCount, $pagesCount);
    }

    /**
     * @param Newsletter $newsletter
     * @return JsonResponse
     */
    public function detachAll(Newsletter $newsletter): JsonResponse
    {
        $newsletter->customers()->detach();
        return $this->emptySuccessResponse();
    }

    /**
     * @param Newsletter $newsletter
     * @param Customer $customer
     * @return JsonResponse
     */
    public function detachCustomer(Newsletter $newsletter, Customer $customer): JsonResponse
    {
        $newsletter->customers()->detach($customer->id);
        return $this->emptySuccessResponse();
    }

    /**
     * @param Newsletter $newsletter
     * @return JsonResponse
     */
    public function send(Newsletter $newsletter): JsonResponse
    {
        $newsletter->status = Newsletter::STATUSES[1];
        $newsletter->save();
        return $this->emptySuccessResponse();
    }
}
