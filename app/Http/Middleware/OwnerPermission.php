<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\UserRole;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ownerRoleId = Role::where('name', 'Owner')->first()->id;
        $userRole = UserRole::where('user_id', Auth::user()->id)
            ->with('role')
            ->first();

        if ($userRole['role']['id'] !== $ownerRoleId) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'forbidden'
                    ]
                ]
            ], 403));
        }

        return $next($request);
    }
}
