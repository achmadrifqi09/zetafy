<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function findAddress($userId, $addressId)
    {
        $address = Address::where('user_id', $userId)
            ->where('id', $addressId)
            ->first();

        if (!$address) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        return $address;
    }
    public function create(AddressRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $data['user_id'] = $user['id'];

        $address = Address::create($data);
        return (new AddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    public function update(AddressRequest $request, string $id)
    {
        $data = $request->validated();
        $userId = Auth::user()->id;
        $address = $this->findAddress($userId, $id);

        $address->update($data);
        return new AddressResource($address);
    }

    public function list(): AddressCollection
    {
        $userId = Auth::user()->id;
        $addresses = Address::where('user_id', $userId)->get();

        return new AddressCollection($addresses);
    }

    public function get(string $id): AddressResource
    {
        $userId = Auth::user()->id;
        $address = $this->findAddress($userId, $id);
        return new AddressResource($address);
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = Auth::user()->id;
        $address = $this->findAddress($userId, $id);
        $address->delete();
        
        return response()->json([
            'data' => true
        ]);
    }
}
