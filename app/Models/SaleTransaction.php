<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\SaleTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'cashier_id',
        'transaction_time',
    ];

    public function casts(): array
    {
        return [
            'transaction_time' => 'datetime',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleTransactionItem::class, 'sale_transaction_id', 'id');
    }
}
