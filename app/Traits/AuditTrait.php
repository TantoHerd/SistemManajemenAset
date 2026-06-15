<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

trait AuditTrait
{
    protected static function bootAuditTrait()
    {
        static::created(function ($model) {
            $model->logAudit('create', $model->toArray(), null);
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            if (!empty($changes)) {
                $model->logAudit('update', $changes, $original);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('delete', null, $model->getOriginal());
        });
    }

    public function logAudit($action, $newData = null, $oldData = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()?->name,
            'action' => $action,
            'module' => $this->getModuleName(),
            'record_id' => $this->id,
            'record_name' => $this->getRecordName(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    protected function getModuleName()
    {
        // Override di masing-masing model
        return strtolower(class_basename($this));
    }

    protected function getRecordName()
    {
        // Override di masing-masing model
        return $this->name ?? $this->title ?? $this->asset_code ?? $this->id;
    }
}