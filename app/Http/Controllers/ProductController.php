<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('validate.id:product')->only(['show', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListProductRequest $request)
    {
        $data = $this->productService->getAll( $request->page);
        return $this->successResponse('Product retrieved successfully', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(StoreProductRequest $request)
    {
        $authUser = $request->attributes->get('auth_user');
        return $this->handleResponse(function () use ($authUser, $request) {
            $data = $this->productService->addProduct($authUser->id, $request->all());

            return $this->result('Product created successfully', $data, 201, ProductResource::class);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->handleResponse(function () use ($id) {
            $data = $this->productService->getProductById($id);
            return $this->result('Product retrieved successfully.', $data, 200, ProductResource::class);
        });
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request,$id)
    {
        return $this->handleResponse(function () use ($id, $request) {
            $data = $this->productService->updateProduct($id, $request->all());
            return $this->successResponse('Product updated successfully', $data);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->handleResponse(function () use ($id) {
            $this->productService->deleteProduct($id);
            return $this->successResponse('Product deleted successfully', null);
        });
    }
}
