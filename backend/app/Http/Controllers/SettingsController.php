<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function get(): JsonResponse
    {
        $settings = DB::table("settings")->first();
        return $this->resourceItemResponse('settings', $settings);
    }

    public function set(Request $request): JsonResponse
    {
        $request->validate([

        ]);

        DB::table("settings")->update($request->all());

        return $this->emptySuccessResponse();
    }
}
