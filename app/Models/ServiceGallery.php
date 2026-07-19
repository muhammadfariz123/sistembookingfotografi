<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceGallery extends Model
{
    protected $fillable = ['service_type_id', 'image_path'];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}