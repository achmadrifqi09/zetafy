<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdministratorOwnerPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ownerRoleId = Role::where('name', 'Owner')->first()->id;
        $adminRoleId = Role::where('name', 'Administrator')->first()->id;

        $userRole = UserRole::where('user_id', Auth::user()->id)
            ->with('role')
            ->first();

        if ($userRole['role']['id'] === $adminRoleId || $userRole['role']['id'] === $ownerRoleId) {
            return $next($request);
        }

        throw new HttpResponseException(response([
            'errors' => [
                'message' => [
                    'forbidden'
                ]
            ]
        ], 403));
    }
}
