<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalIncome extends Model
{
    protected $fillable = ['user_id', 'description', 'amount', 'date'];

    protected $casts = ['date' => 'date', 'amount' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}