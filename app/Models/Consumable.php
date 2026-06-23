<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    protected $fillable = [
        'item_name', 'category_id', 'brand', 'unit', 'current_stock',
        'max_stock', 'critical_threshold', 'low_threshold', 'id_code', 'campus_id',
    ];

    public function category() { return $this->belongsTo(ConsumableCategory::class, 'category_id'); }
    public function campus() { return $this->belongsTo(Campus::class); }
    public function stockLogs() { return $this->hasMany(ConsumableStockLog::class)->latest(); }
    public function requestItems() { return $this->hasMany(ConsumableRequestItem::class); }

    public function getStatusAttribute()
    {
        if ($this->current_stock <= $this->critical_threshold) return 'critical';
        if ($this->current_stock <= $this->low_threshold) return 'low';
        return 'available';
    }

    public static function generateIdCode(string $itemName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $itemName), 0, 3));
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return "{$prefix}-{$random}";
    }
}