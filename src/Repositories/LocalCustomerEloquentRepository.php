<?php

namespace GoCardlessPayment\Repositories;

use GoCardlessPayment\Contracts\GoCardlessCustomer;
use GoCardlessPayment\Contracts\LocalCustomerRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalCustomerEloquentRepository implements LocalCustomerRepository
{
    public readonly string $localCustomerModel;

    public readonly string $syncLocalKeyName;

    public function __construct(string $localCustomerModel, string $syncLocalKeyName)
    {
        $this->localCustomerModel = $localCustomerModel;
        $this->syncLocalKeyName = $syncLocalKeyName;
    }

    public function findLocalCustomer(string|GoCardlessCustomer $gocardlessId): ?GoCardlessCustomer
    {
        $gocardlessId = $gocardlessId instanceof GoCardlessCustomer ? $gocardlessId->gocardlessKey() : $gocardlessId;

        /** @var class-string<Model> $model */
        $model = $this->localCustomerModel;

        $builder = in_array(SoftDeletes::class, class_uses_recursive($model))
            ? $model::withTrashed()
            : new $model;

        return $gocardlessId ? $builder->where($this->syncLocalKeyName, $gocardlessId)->first() : null;
    }

    public function findLocalCustomerBySyncKey(string|GoCardlessCustomer $id): ?GoCardlessCustomer
    {
        $id = $id instanceof GoCardlessCustomer ? $id->getSyncKey() : $id;

        /** @var class-string<Model> $model */
        $model = $this->localCustomerModel;

        $builder = in_array(SoftDeletes::class, class_uses_recursive($model))
            ? $model::withTrashed()
            : new $model;

        return $id ? $builder->whereKey($id)->first() : null;
    }
}
