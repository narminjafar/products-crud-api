<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{
    public function all(int $perPage = null, ?int $page = null);
    public function find(int $id, array $relations = []): ?Model;

    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
