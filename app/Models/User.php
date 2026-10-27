<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_EMPLOYEE = 'employee';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_FINANCE = 'finance';

    public const ROLE_TRAVEL_ADMIN = 'travel_admin';

    public const ROLE_DIREKTUR = 'direktur';

    /**
     * The 4 roles authorized to access and perform approvals.
     * Employees are strictly excluded.
     */
    public const APPROVAL_ROLES = [
        self::ROLE_MANAGER,
        self::ROLE_FINANCE,
        self::ROLE_TRAVEL_ADMIN,
        self::ROLE_DIREKTUR,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_code',
        'job_title',
        'department',
        'band',
        'role',
        'approval_limit',
        'phone',
        'avatar_url',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approval_limit' => 'decimal:2',
        ];
    }

    /**
     * Check if user has any of the specified roles.
     * Supports case-insensitivity, trim, array or variable arguments, and alias mapping.
     */
    public function hasRole(string|array ...$roles): bool
    {
        $flattened = [];
        foreach ($roles as $item) {
            if (is_array($item)) {
                foreach ($item as $subItem) {
                    $flattened[] = strtolower(trim((string) $subItem));
                }
            } else {
                $flattened[] = strtolower(trim((string) $item));
            }
        }

        $userRole = strtolower(trim($this->role ?? ''));

        foreach ($flattened as $role) {
            if ($role === '') {
                continue;
            }

            // Direct match
            if ($userRole === $role) {
                return true;
            }

            // Alias mappings
            if (($role === 'director' || $role === 'direktur') && ($userRole === 'direktur' || $userRole === 'director')) {
                return true;
            }
            if (($role === 'admin' || $role === 'travel_admin') && ($userRole === 'travel_admin' || $userRole === 'admin')) {
                return true;
            }

            // Backward compatibility for legacy verbose role strings
            if (str_contains($userRole, $role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the user is authorized to access approval features.
     * Approval is strictly restricted to the 4 roles: manager, finance, travel_admin, and direktur.
     */
    public function canAccessApprovals(): bool
    {
        return $this->hasRole(self::APPROVAL_ROLES);
    }

    /**
     * Check if the user is a standard employee without approval rights.
     */
    public function isEmployee(): bool
    {
        return ! $this->canAccessApprovals();
    }

    /**
     * Get a clean, human-readable display name for the user's role.
     */
    public function getRoleDisplayNameAttribute(): string
    {
        return match (strtolower(trim($this->role ?? ''))) {
            'employee' => 'Employee',
            'manager' => 'Line Manager',
            'finance' => 'Finance',
            'travel_admin', 'admin' => 'Travel Admin',
            'direktur', 'director' => 'Direktur',
            default => $this->role ?? 'Employee',
        };
    }

    /**
     * Relationship: Associated Role definition.
     */
    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /**
     * Check if user has any financial authorization rights.
     */
    public function hasApprovalAuthority(): bool
    {
        return $this->canAccessApprovals();
    }

    /**
     * Check if user can approve a given trip request at its current stage.
     *
     * @return array{allowed: bool, reason: string}
     */
    public function canAuthorizeTrip(TravelRequest $trip): array
    {
        // 0. Only the 4 approval roles (excluding employee) can approve
        if (! $this->canAccessApprovals()) {
            return [
                'allowed' => false,
                'reason' => 'Karyawan (Employee) tidak memiliki kewenangan otorisasi persetujuan.',
            ];
        }

        // 1. Segregation of duties: cannot approve own trip
        if ($this->id === $trip->user_id) {
            return [
                'allowed' => false,
                'reason' => 'Segregation of duties: You cannot approve your own travel request.',
            ];
        }

        // 2. Check if trip is in a pending approval stage
        $stage = $trip->approval_stage ?? 'Line Manager Review';
        if (in_array($stage, ['Ticket Issued / Confirmed', 'Rejected', 'Cancelled', 'Clarification Requested'])) {
            return [
                'allowed' => false,
                'reason' => "This travel request is in '{$stage}' stage and does not require approval.",
            ];
        }

        $cost = (float) ($trip->total_cost ?? 0);
        $userLimit = (float) ($this->approval_limit ?? 0);

        // 3. Stage-specific waterfall authorization
        if (str_contains($stage, 'Manager')) {
            // Line Manager stage requires Band 4+ or Manager / Travel Admin / Direktur role
            if (! in_array($this->band, ['Band 4', 'Band 5']) && ! $this->hasRole([self::ROLE_MANAGER, self::ROLE_TRAVEL_ADMIN, self::ROLE_DIREKTUR])) {
                return [
                    'allowed' => false,
                    'reason' => 'Requires Line Manager (Band 4+) authorization authority.',
                ];
            }

            return ['allowed' => true, 'reason' => 'Authorized as Line Manager.'];
        }

        if (str_contains($stage, 'Finance')) {
            // Finance stage requires Finance role, Travel Admin, or Band 5 with Finance authority
            $isFinance = $this->hasRole([self::ROLE_FINANCE, self::ROLE_TRAVEL_ADMIN]) || str_contains(strtolower($this->department ?? ''), 'finance');
            if (! $isFinance && $this->band !== 'Band 5') {
                return [
                    'allowed' => false,
                    'reason' => 'Requires Corporate Finance authorization authority.',
                ];
            }
            if ($userLimit > 0 && $userLimit < 5000000) {
                return [
                    'allowed' => false,
                    'reason' => 'Your approval limit (Rp '.number_format($userLimit, 0, ',', '.').') is below the Finance threshold.',
                ];
            }

            return ['allowed' => true, 'reason' => 'Authorized as Corporate Finance.'];
        }

        if (str_contains($stage, 'Director')) {
            // Director stage requires Band 5 / Direktur / Travel Admin
            $isDirector = $this->band === 'Band 5' || $this->hasRole([self::ROLE_DIREKTUR, self::ROLE_TRAVEL_ADMIN]);
            if (! $isDirector) {
                return [
                    'allowed' => false,
                    'reason' => 'Requires Executive Director / C-Suite authorization authority.',
                ];
            }

            return ['allowed' => true, 'reason' => 'Authorized as Executive Director.'];
        }

        return ['allowed' => false, 'reason' => 'Stage not actionable for authorization.'];
    }

    /**
     * Relationship: Travel requests authored by this user.
     */
    public function travelRequests(): HasMany
    {
        return $this->hasMany(TravelRequest::class, 'user_id');
    }
}
