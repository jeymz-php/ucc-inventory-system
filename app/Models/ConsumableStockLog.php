<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConsumableStockLog extends Model
{
    protected $fillable = ['consumable_id', 'action', 'change_amount', 'previous_total', 'new_total', 'user_id'];

    public function consumable() { return $this->belongsTo(Consumable::class); }
    public function user() { return $this->belongsTo(User::class); }
}