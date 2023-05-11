<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'name', 'mime_type', 'size', 'url','path'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function copy(): OrderFile
    {
        $newFile = new OrderFile($this->toArray());
        $newFile->save();

        return $newFile;
    }
}
