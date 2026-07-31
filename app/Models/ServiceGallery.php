<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceGallery extends Model
{
    protected $fillable = ['service_category_id', 'image_path'];

    // Galeri sekarang milik Kategori, bukan Paket
    public function category() {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}