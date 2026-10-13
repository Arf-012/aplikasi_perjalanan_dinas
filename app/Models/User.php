<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
     * Check if user has any financial authorization rights.
     */
    public function hasApprovalAuthority(): bool
    {
        return (float) $this->approval_limit > 0 || in_array($this->band, ['Band 4', 'Band 5']);
    }

    /**
     * Check if user can approve a given trip request at its current stage.
     *
     * @return array{allowed: bool, reason: string}
     */
    public function canAuthorizeTrip(TravelRequest $trip): array
    {
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
            // Line Manager stage requires Band 4+ or Manager role
            if (! in_array($this->band, ['Band 4', 'Band 5']) && ! str_contains($this->role, 'Manager') && ! str_contains($this->role, 'Approver')) {
                return [
                    'allowed' => false,
                    'reason' => 'Requires Line Manager (Band 4+) authorization authority.',
                ];
            }

            return ['allowed' => true, 'reason' => 'Authorized as Line Manager.'];
        }

        if (str_contains($stage, 'Finance')) {
            // Finance stage requires Finance role or Band 5 with Finance authority
            $isFinance = str_contains($this->role, 'Finance') || str_contains($this->department, 'Finance');
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
            // Director stage requires Band 5 / C-Suite
            $isDirector = $this->band === 'Band 5' || str_contains($this->role, 'Director') || str_contains($this->role, 'C-Suite');
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
    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'user_id');
    }
}
