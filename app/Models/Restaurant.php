<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = "restaurants";
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['name'];

    // Relasi ke tabel schedules
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, "schedule_id", "id");
    }
}
