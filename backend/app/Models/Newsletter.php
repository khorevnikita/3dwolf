<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'text'];

    protected $appends = ['editable'];

    const STATUSES = ['draft', 'sending', 'sent'];

    public function customers()
    {
        return $this->belongsToMany(Customer::class)->withPivot('sent_at');
    }

    public function queuedUsers()
    {
        return $this->customers()->orderBy('customer_newsletter.id')->wherePivotNull('sent_at');
    }

    public function sentUsers()
    {
        return $this->customers()->orderBy('customer_newsletter.id')->wherePivotNotNull('sent_at');
    }

    public function getEditableAttribute()
    {
        return $this->status === self::STATUSES[0];
    }

    public function scopeSending($query)
    {
        return $query->where("status", self::STATUSES[1]);
    }
}
