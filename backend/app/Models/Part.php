<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'bought_at', 'inv_number', 'prod_number', 'manufacturer_id',
        'material_id', 'color', 'weight', 'price', 'status'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
