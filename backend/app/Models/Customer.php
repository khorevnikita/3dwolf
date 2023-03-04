<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'surname', 'father_name', 'phone', 'email', 'telegram', 'type', 'entity_type', 'inn', 'kpp', 'ogrn', 'okpo', 'okved', 'address', 'ceo', 'rs', 'ks', 'bik', 'bank'];
}
