<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterFile extends Model
{
    use HasFactory;

    protected $fillable = ['newsletter_id', 'url', 'path', 'name'];

    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class);
    }
}
