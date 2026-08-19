<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'address',
        'phone',
        'payment_method',
        'status',
        'total_price',
        'delivery_fee',
        'observations',
        'estimated_time',
    ];

    protected $casts = [
        'total_price'   => 'decimal:2',
        'delivery_fee'  => 'decimal:2',
        'estimated_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAwaiting($query)
    {
        return $query->where('status', 'awaiting_confirmation');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'awaiting_confirmation' => 'Aguardando confirmação',
            'preparing'             => 'Preparando',
            'ready_for_delivery'    => 'Pronto / Saiu para entrega',
            'delivered'             => 'Entregue / Retirado',
            'canceled'              => 'Cancelado',
            default                 => $this->status,
        };
    }
}
