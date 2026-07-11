<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableRequestItem extends Model
{
    protected $fillable = [
        'consumable_request_id',
        'consumable_id',
        'quantity',
        'purpose',
        'release_date',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'release_date' => 'date:Y-m-d',
    ];

    public function request()
    {
        return $this->belongsTo(ConsumableRequest::class, 'consumable_request_id');
    }

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }
}