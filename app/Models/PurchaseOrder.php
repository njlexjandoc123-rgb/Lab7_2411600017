<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $supplier_id
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property string $reference_number
 * @property string $status
 * @property Carbon $order_date
 * @property Carbon|null $expected_delivery_date
 * @property Carbon|null $approved_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $received_at
 * @property float|string $subtotal
 * @property float|string $tax_amount
 * @property float|string $total_amount
 */
class PurchaseOrder extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft', 'pending', 'approved', 'sent', 'received', 'cancelled', 'rejected'];

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'created_by',
        'approved_by',
        'status',
        'order_date',
        'expected_delivery_date',
        'approved_at',
        'sent_at',
        'received_at',
        'subtotal',
        'tax_amount',
        'total_amount',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['received', 'cancelled', 'rejected']);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForSupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeBetweenDates(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        return $query
            ->when($startDate, fn (Builder $builder) => $builder->whereDate('order_date', '>=', $startDate))
            ->when($endDate, fn (Builder $builder) => $builder->whereDate('order_date', '<=', $endDate));
    }

    public function calculateTotal(): float
    {
        $subtotal = (float) $this->items()->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total')->value('total');
        $this->forceFill([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal + (float) $this->tax_amount,
        ])->save();

        return $subtotal + (float) $this->tax_amount;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeReceived(): bool
    {
        return \in_array($this->status, ['approved', 'sent'], true)
            && $this->items()->whereColumn('received_quantity', '<', 'quantity')->exists();
    }

    public static function generateReferenceNumber(): string
    {
        do {
            $reference = 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (static::withTrashed()->where('reference_number', $reference)->exists());

        return $reference;
    }
}
