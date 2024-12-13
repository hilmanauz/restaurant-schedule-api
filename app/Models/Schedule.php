<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "schedules";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        "restaurant_id",
        'day_of_week',
        'open_time',
        'close_time'
    ];

    // Relasi ke tabel restaurants
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, "restaurant_id", "id");
    }

}
