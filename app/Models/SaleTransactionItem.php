<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleTransactionItem extends Model
{
    /** @use HasFactory<\Database\Factories\SaleTransactionItemFactory> */
    use HasFactory;

    protected $fillable = [
        'sale_transaction_id',
        'product_id',
        'product_sku',
        'product_name',
        'product_price',
        'quantity_sold',
    ];

    public function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function saleTransaction(): BelongsTo
    {
        return $this->belongsTo(SaleTransaction::class, 'sale_transaction_id', 'id');
    }
}
