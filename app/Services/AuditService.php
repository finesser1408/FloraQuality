<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an audit event.
     *
     * @param  string      $action      e.g. 'created', 'updated', 'deleted', 'exported'
     * @param  string      $modelType   e.g. 'FlowerChecklist'
     * @param  int|null    $modelId
     * @param  string|null $description Human-readable summary
     */
    public static function log(
        string $action,
        string $modelType,
        ?int $modelId = null,
        ?string $description = null
    ): void {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
