<?php

namespace App\Models;

use App\Services\SupplierPerformanceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $contact_person
 * @property string|null $phone
 * @property int $lead_time_days
 * @property float|string $minimum_order_amount
 * @property bool $is_active
 */
class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'payment_terms',
        'lead_time_days',
        'minimum_order_amount',
        'is_active',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'minimum_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithLeadTime($query, int $days)
    {
        return $query->where('lead_time_days', '<=', $days);
    }

    public function getFullContactAttribute(): string
    {
        return collect([$this->contact_person, $this->email, $this->phone])
            ->filter()
            ->implode(' · ');
    }

    public function getPerformanceRatingAttribute(): float
    {
        return app(SupplierPerformanceService::class)->metricsForSupplier($this)['on_time_percentage'];
    }
}
