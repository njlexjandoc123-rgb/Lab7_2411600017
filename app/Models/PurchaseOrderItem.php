<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $purchase_order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $received_quantity
 * @property float|string $unit_price
 * @property-read int $outstanding_quantity
 * @property-read float $line_total
 */
class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'received_quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_price;
    }

    public function getOutstandingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->received_quantity);
    }
}
