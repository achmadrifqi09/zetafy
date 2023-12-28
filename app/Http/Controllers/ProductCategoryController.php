<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryRequest;
use App\Http\Resources\ProductCategoryCollection;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    public function create(ProductCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = ProductCategory::create($data);

        return (new ProductCategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function get(string $id): ProductCategoryResource
    {
        $category = $this->findtProductCategory($id);
        return new ProductCategoryResource($category);
    }

    public function list(): ProductCategoryCollection
    {
        $categoris = ProductCategory::all();
        return new ProductCategoryCollection($categoris);
    }

    public function update(ProductCategoryRequest $request, string $id): ProductCategoryResource
    {
        $data = $request->validated();
        $category = $this->findtProductCategory($id);

        $category->update($data);
        return new ProductCategoryResource($category);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = $this->findtProductCategory($id);
        $category->delete();

        return response()->json([
            'data' => true
        ]);
    }

    public function findtProductCategory(string $id): ProductCategory
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        return $category;
    }
}
