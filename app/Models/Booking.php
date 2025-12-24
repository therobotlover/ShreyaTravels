<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING_PAYMENT';

    public const STATUS_PAID = 'PAID';

    public const STATUS_FAILED = 'FAILED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'reference',
        'user_id',
        'user_email',
        'tour_id',
        'travel_date',
        'travelers',
        'note',
        'base_amount',
        'discount_amount',
        'total_amount',
        'discount_code',
        'status',
    ];

    protected $casts = [
        'travel_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
