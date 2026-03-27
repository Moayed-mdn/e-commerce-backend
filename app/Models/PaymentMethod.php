<?php
// app/Models/PaymentMethod.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'provider', 'payment_method_id', 'brand', 'last_four',
        'exp_month', 'exp_year', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCardNumberAttribute()
    {
        return '**** **** **** ' . $this->last_four;
    }

    public function getExpirationAttribute()
    {
        return str_pad($this->exp_month, 2, '0', STR_PAD_LEFT) . '/' . $this->exp_year;
    }

    public function isExpired()
    {
        return now()->gt(\Carbon\Carbon::createFromDate($this->exp_year, $this->exp_month, 1)->endOfMonth());
    }
}