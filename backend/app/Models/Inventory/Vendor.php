<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendors';

    const TYPE_SUPPLIER = 'supplier';
    const TYPE_CONTRACTOR = 'contractor';
    const TYPE_SERVICE_PROVIDER = 'service_provider';
    const TYPE_MANUFACTURER = 'manufacturer';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLACKLISTED = 'blacklisted';

    protected $fillable = [
        'uuid',
        'vendor_code',
        'name',
        'name_bn',
        'contact_person',
        'mobile',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'district',
        'country',
        'tax_id',
        'trade_license',
        'vendor_type',
        'status',
        'rating',
        'payment_terms',
        'credit_limit',
        'credit_days',
        'notes',
    ];

    protected $casts = [
        'rating' => 'integer',
        'credit_limit' => 'integer',
        'credit_days' => 'integer',
    ];

    public static function generateVendorCode(): string
    {
        $prefix = 'VND';
        $year = date('Y');
        $lastVendor = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastVendor ? ((int) substr($lastVendor->vendor_code, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'vendor_id');
    }

    public static function vendorTypes(): array
    {
        return [
            self::TYPE_SUPPLIER => 'Supplier',
            self::TYPE_CONTRACTOR => 'Contractor',
            self::TYPE_SERVICE_PROVIDER => 'Service Provider',
            self::TYPE_MANUFACTURER => 'Manufacturer',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_BLACKLISTED => 'Blacklisted',
        ];
    }
}
