<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['user_id', 'name'];

    // 1 Kategori punya banyak Paket
    public function serviceTypes() {
        return $this->hasMany(ServiceType::class);
    }

    // 1 Kategori punya banyak Foto Galeri (Portofolio)
    public function galleries() {
        return $this->hasMany(ServiceGallery::class);
    }
}