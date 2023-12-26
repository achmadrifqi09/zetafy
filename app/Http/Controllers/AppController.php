<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Http\Resources\AppCollection;
use App\Http\Resources\AppResource;
use App\Models\App;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AppController extends Controller
{
    public function create(AppRequest $request): JsonResponse
    {
        $data = $request->validated();
        $app = App::create([
            'name' => $data['name'],
            'app_key' => Str::random(255)
        ]);

        return (new AppResource($app))->response()->setStatusCode(201);
    }

    public function destroy($id)
    {
        $app = App::find($id);

        if (!$app) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $app->delete();
        return response()->json([
            'data' => true
        ]);
    }

    public function list()
    {
        $apps = App::all();

        return new AppCollection($apps);
    }
}
