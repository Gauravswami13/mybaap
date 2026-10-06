<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteAccountRequest;
use App\Services\AccountService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AccountService $accounts) {}

    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->accounts->delete($request->user());

        return $this->ok((object) [], 'Account deleted.');
    }
}
