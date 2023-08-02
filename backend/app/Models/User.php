<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\Mutator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
        'balance',
        'customer_id',
        'tg_username',
        'tg_channel_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['permission'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function permission()
    {
        return $this->hasOne(UserPermission::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeSearch($q, $search)
    {
        return $q->where(function ($q) use ($search) {
            $q->where("name", "like", "%$search%")
                ->orWhere("surname", "like", "%$search%");
        });
    }

    public function scopeModerator($q)
    {
        return $q->whereNull("customer_id");
    }

    public function scopeCustomer($q, $customerId = null)
    {
        if (!$customerId) return $q->whereNotNull("customer_id");

        return $q->where("customer_id", $customerId);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function updateBalance(float $expense = 0, float $income = 0)
    {
        $this->balance = $this->balance - $expense + $income;
        $this->save();
    }

    public function setPermission(array $permission)
    {
        if (!UserPermission::query()->where("user_id", $this->id)->exists()) {
            UserPermission::query()->insert(['user_id' => $this->id]);
        }
        UserPermission::query()->where("user_id", $this->id)->update([
            'users' => in_array('users', $permission),
            'customers' => in_array('customers', $permission),
            'materials' => in_array('materials', $permission),
            'manufacturers' => in_array('manufacturers', $permission),
            'parts' => in_array('parts', $permission),
            'accounts' => in_array('accounts', $permission),
            'orders' => in_array('orders', $permission),
            'contracts' => in_array('contracts', $permission),
            'payments' => in_array('payments', $permission),
            'regular_payments' => in_array('regular_payments', $permission),
            'estimates' => in_array('estimates', $permission),
            'newsletters' => in_array('newsletters', $permission),
            'tasks' => in_array('tasks', $permission),
            'branches' => in_array('branches', $permission),
            'settings' => in_array('settings', $permission),
            'delivery_address' => in_array('delivery_address', $permission),
        ]);
    }

    public function getPermissionAttribute()
    {
        $permission = $this->permission()->first();
        if (!$permission) return [];;
        return array_keys(array_filter($permission->toArray(), function ($p, $k) {
            return $p && !in_array($k, ['id', 'user_id', 'updated_at']);
        }, 1));
    }

    public function isCustomer(): bool
    {
        return !!$this->customer_id;
    }
}
