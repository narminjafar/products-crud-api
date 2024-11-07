<?php

namespace App\Services;

use App\Exceptions\OrderNotFoundException;
use App\Exceptions\ProductNotFoundException;
use App\Interfaces\ProductRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductService extends BaseService
{

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
    )
    {
    }

    public function getAll(?int $page = null)
    {
        $perPage = 10;

        return $this->productRepository->all($perPage, $page);
    }

    public function addProduct(int $userId, array $data)
    {
        return $this->runInTransaction(function () use ($userId, $data) {
            $data['user_id'] = $userId;
            return $this->productRepository->create($data);

        }, 'Product creation failed.', 500);
    }

    public function getProductById(int $productId)
    {
        return $this->findOrFail($productId);
    }

    public function updateProduct(int $productId, array $data)
    {
        return $this->runInTransaction(function () use ($productId, $data) {

            $product = $this->findOrFail($productId);

            $product->update($data);

            return $product;

        }, 'Product update failed: ', 500);
    }


    public function deleteProduct(int $productId): bool
    {
         $this->findOrFail($productId);
        return (bool)$this->productRepository->delete($productId);
    }

    public function runInTransaction(callable $callback, string $errorMessage, int $errorCode)
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            throw new RuntimeException($errorMessage . $e->getMessage(), $errorCode ?? $e->getCode(), $e);
        }
    }

    protected function findOrFail(int $productId, array $relations = [])
    {

        $product = $this->productRepository->find($productId, $relations);

         if (!$product) {
            throw new ProductNotFoundException($productId);
        }
        return $product;
    }

}
