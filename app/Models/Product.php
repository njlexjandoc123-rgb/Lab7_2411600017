<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int|null $supplier_id
 * @property int $qty
 * @property int $reorder_level
 * @property float|string $unit_price
 * @property string $sku
 * @property string $name
 * @property string|null $category
 * @property-read string $stock_status
 * @property-read string $stock_status_color
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'brand',
        'category',
        'supplier_id',
        'qty',
        'reorder_level',
        'unit_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'qty' => 'integer',
        'reorder_level' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    protected $appends = [
        'stock_status',
        'stock_status_color',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(InventoryTransaction::class)->latestOfMany();
    }

    public function isLowStock(): bool
    {
        return $this->qty > 0 && $this->qty <= (int) ($this->reorder_level ?? 5);
    }

    public function isOutOfStock(): bool
    {
        return $this->qty <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'danger';
        }

        if ($this->isLowStock()) {
            return 'warning';
        }

        return 'success';
    }

    public function stockStatusClass(): string
    {
        return match ($this->stock_status_color) {
            'danger' => 'bg-red-100 text-red-800',
            'warning' => 'bg-amber-100 text-amber-800',
            default => 'bg-emerald-100 text-emerald-800',
        };
    }

    public function stockStatusText(): string
    {
        return $this->stock_status;
    }

    public function inventoryValue(): float
    {
        return (float) $this->qty * (float) $this->unit_price;
    }

    public function scopeLowStock($query)
    {
        return $query->where('qty', '>', 0)
            ->whereColumn('qty', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('qty', '<=', 0);
    }

    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForSupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }
}
