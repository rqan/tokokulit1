<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an audit event.
     */
    public static function log(string $action, ?string $targetType = null, ?int $targetId = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}
