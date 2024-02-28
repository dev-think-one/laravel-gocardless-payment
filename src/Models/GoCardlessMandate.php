<?php

namespace GoCardlessPayment\Models;

use GoCardlessPayment\Database\Factories\GoCardlessMandateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoCardlessMandate extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function getTable()
    {
        return config('gocardless-payment.tables.mandates');
    }

    protected static function newFactory(): GoCardlessMandateFactory
    {
        return GoCardlessMandateFactory::new();
    }
}
