<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $description, ?int $userId = null, ?string $ipAddress = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    public function getDisplayEventAttribute(): string
    {
        return match ($this->action) {
            'CREATE', 'UPDATE', 'DELETE' => 'DATA.MUTATION',
            default => $this->action,
        };
    }
}
