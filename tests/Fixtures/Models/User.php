<?php

namespace GoCardlessPayment\Tests\Fixtures\Models;

use GoCardlessPayment\Contracts\GoCardlessCustomer;
use GoCardlessPayment\Models\Traits\AsGoCardlessCustomer;
use GoCardlessPayment\Tests\Fixtures\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model implements GoCardlessCustomer
{
    use AsGoCardlessCustomer;
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
