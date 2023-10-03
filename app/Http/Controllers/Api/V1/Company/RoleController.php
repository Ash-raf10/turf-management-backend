<?php

namespace App\Http\Controllers\Api\V1\Company;


use App\Http\Resources\RoleResource;
use App\Services\Company\RoleService;
use App\Http\Controllers\Api\V1\BaseController;

class RoleController extends BaseController
{

    public function __construct(protected RoleService $roleService)
    {
    }

    public function index()
    {
        $roles = $this->roleService->roleList();

        return $this->sendResponse(
            true,
            RoleResource::collection($roles),
            "",
            200,
            0000
        );
    }
}
