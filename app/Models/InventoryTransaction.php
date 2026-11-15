<?php

namespace App\Models;

use App\Observers\InventoryTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([InventoryTransactionObserver::class])]
class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'transaction_type',
        'quantity',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetweenDates($query, ?string $startDate = null, ?string $endDate = null)
    {
        if (! empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }
}
