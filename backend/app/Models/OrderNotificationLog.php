<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderNotificationLog extends Model
{

    protected $fillable = ['order_id', 'user_id', 'channel','attached', 'order_status'];

    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(Order $order, string $channel, bool $attach): self
    {
        $log = new OrderNotificationLog([
            'order_id' => $order->id,
            'user_id' => auth("sanctum")->id(),
            'channel' => $channel,
            'order_status' => $order->status,
            'attached' => $attach,
        ]);
        $log->save();
        return $log;
    }
}
