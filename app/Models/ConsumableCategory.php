<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConsumableCategory extends Model
{
    protected $fillable = ['name'];
    public function consumables() { return $this->hasMany(Consumable::class); }
}