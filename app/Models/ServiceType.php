<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['user_id', 'service_category_id', 'name', 'description', 'price', 'photo_limit'];

    public function category() {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }
}