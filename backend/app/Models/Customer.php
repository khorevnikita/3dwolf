<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'surname', 'father_name', 'phone', 'email', 'telegram', 'type', 'entity_type', 'title', 'inn', 'kpp', 'ogrn', 'okpo', 'okved', 'address', 'ceo', 'rs', 'ks', 'bik', 'bank', 'source'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getTitleAttribute($value)
    {
        if (!$value) {
            return "$this->name $this->surname";
        }
        return $value;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where("name", "like", "%$search%")
                ->orWhere("surname", "like", "%$search%")
                ->orWhere("email", "like", "%$search%")
                ->orWhere("phone", "like", "%$search%")
                ->orWhere("inn", "like", "%$search%")
                ->orWhere("title", "like", "%$search%");
        });
    }
}
