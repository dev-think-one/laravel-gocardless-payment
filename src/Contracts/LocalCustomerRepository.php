<?php

namespace GoCardlessPayment\Contracts;

interface LocalCustomerRepository
{
    public function findLocalCustomer(string|GoCardlessCustomer $gocardlessId): ?GoCardlessCustomer;

    public function findLocalCustomerBySyncKey(string|GoCardlessCustomer $id): ?GoCardlessCustomer;
}
