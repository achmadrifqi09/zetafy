<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateReqest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateAvatarRequest;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function findUser($id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        return $user;
    }

    public function register(UserCreateReqest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user['token'] = $user->createToken($user['id'] . '-' . $user['email'])
            ->plainTextToken;

        $customerRoleId = Role::where('name', 'Customer')->first();
        UserRole::create([
            'role_id' => $customerRoleId['id'],
            'user_id' => $user['id']
        ]);

        return (new UserAuthResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserAuthResource
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'invalid credential'
                    ]
                ]
            ], 401));
        }

        $user = User::where('email', $data['email'])->first();
        $user['token'] = $user->createToken($user['id'] . '-' . $user['email'])
            ->plainTextToken;

        return new UserAuthResource($user);
    }

    public function get(): UserResource
    {
        $user = $this->findUser(Auth::user()->id);
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = $this->findUser(Auth::user()->id);

        if (User::where('id', '!=', $user['id'])->where('email', $data['email'])->count() > 0) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'email already taken'
                    ]
                ]
            ], 400));
        }

        $user->update($data);
        return new UserResource($user);
    }

    public function updatePassword(UserUpdatePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->findUser(Auth::user()->id);

        if (!Hash::check($data['current_password'], $user['password'])) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'wrong password'
                    ]
                ]
            ], 400));
        }

        $user['password'] = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'data' => true
        ]);
    }

    public function updateAvatar(UserUpdateAvatarRequest $request): UserResource
    {
        $request->validated();
        $user = $this->findUser(Auth::user()->id);

        if (Storage::exists(strval($user['avatar']))) {
            Storage::delete(strval($user['avatar']));
        }

        $fileName = $user['id'] . '-' . str_replace(' ', '_', $user['name']) . '.' . $request->file('avatar')
            ->getClientOriginalExtension();
        $user['avatar'] = $request->file('avatar')->storeAs('user-avatars', $fileName);
        $user->save();

        return new UserResource($user);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'data' => true
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!Hash::check($request['password'], $user['password'])) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'failed to delete account'
                    ]
                ]
            ]);
        }

        if (Storage::exists(strval($user['avatar']))) {
            Storage::delete(strval($user['avatar']));
        }

        $user->delete();
        return response()->json([
            'data' => true
        ]);
    }
}
