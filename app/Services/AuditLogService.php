<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogService
{
    /**
     * Log an auditable action.
     */
    public function log(
        string $action,
        ?User $user = null,
        ?User $actor = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id,
            'actor_id' => $actor?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Log a data export request.
     */
    public function logDataExport(User $user): AuditLog
    {
        return $this->log(
            action: 'data_export',
            user: $user,
            metadata: ['requested_at' => now()->toIso8601String()]
        );
    }

    /**
     * Log account deletion request.
     */
    public function logAccountDeletionRequested(User $user): AuditLog
    {
        return $this->log(
            action: 'account_deletion_requested',
            user: $user,
            metadata: ['requested_at' => now()->toIso8601String()]
        );
    }

    /**
     * Log account deletion cancelled.
     */
    public function logAccountDeletionCancelled(User $user): AuditLog
    {
        return $this->log(
            action: 'account_deletion_cancelled',
            user: $user,
            metadata: ['cancelled_at' => now()->toIso8601String()]
        );
    }

    /**
     * Log account deleted.
     */
    public function logAccountDeleted(int $userId, string $email): AuditLog
    {
        return $this->log(
            action: 'account_deleted',
            metadata: [
                'user_id' => $userId,
                'email' => $email,
                'deleted_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Log consent change.
     */
    public function logConsentChanged(
        User $user,
        string $consentType,
        bool $oldValue,
        bool $newValue
    ): AuditLog {
        return $this->log(
            action: 'consent_changed',
            user: $user,
            entityType: 'UserConsent',
            oldValues: ['consented' => $oldValue, 'type' => $consentType],
            newValues: ['consented' => $newValue, 'type' => $consentType]
        );
    }

    /**
     * Log admin action on user.
     */
    public function logAdminAction(
        User $admin,
        User $user,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        return $this->log(
            action: 'admin_action',
            user: $user,
            actor: $admin,
            oldValues: $oldValues,
            newValues: $newValues,
            metadata: ['admin_action' => $action]
        );
    }
}
