<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TravelRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'flight_cost' => 'decimal:2',
        'hotel_cost' => 'decimal:2',
        'transit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the route key name for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'request_code';
    }

    /**
     * Retrieve the model for a bound value (supports both request_code and numeric id).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('request_code', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    protected static function booted()
    {
        static::creating(function ($trip) {
            $nextSeq = (static::max('id') ?? 0) + 1;
            if (empty($trip->request_code)) {
                $trip->request_code = 'TRV-'.(10000 + $nextSeq);
            }
            if (empty($trip->trip_id)) {
                $trip->trip_id = 'TRIP-'.(10000 + $nextSeq);
            }
            if (empty($trip->cost_center) && ! empty($trip->cost_center_id)) {
                $trip->cost_center = $trip->cost_center_id;
            }
            if (empty($trip->cost_center)) {
                $trip->cost_center = 'CC-402';
            }
            if (empty($trip->currency)) {
                $trip->currency = 'IDR';
            }
            if (empty($trip->approval_stage)) {
                $trip->approval_stage = 'Line Manager Review';
            }
            if (empty($trip->overall_status)) {
                $trip->overall_status = 'pending_approval';
            }
            if (empty($trip->policy_status)) {
                $trip->policy_status = 'compliant';
            }
            if (empty($trip->budget_status)) {
                $trip->budget_status = 'available';
            }
            if (empty($trip->departure_time_slot)) {
                $trip->departure_time_slot = 'Morning Flight (06:00 - 11:00)';
            }
            if (empty($trip->return_time_slot)) {
                $trip->return_time_slot = 'Evening Flight (17:00 - 22:00)';
            }
            if (empty($trip->purpose_type)) {
                $trip->purpose_type = 'Client Meeting';
            }
            if (empty($trip->submitted_at)) {
                $trip->submitted_at = now();
            }
        });
    }

    public function getPurposeAttribute()
    {
        return $this->purpose_title ?? $this->purpose_description ?? '';
    }

    public function setPurposeAttribute($value)
    {
        $this->attributes['purpose_title'] = Str::limit($value, 100);
        $this->attributes['purpose_description'] = $value;
    }

    public function getCostCenterIdAttribute()
    {
        return $this->cost_center ?? $this->attributes['cost_center'] ?? null;
    }

    public function setCostCenterIdAttribute($value)
    {
        $this->attributes['cost_center'] = $value;
    }

    public function traveler()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the waterfall authority tier based on trip total cost.
     * Tier 1: <= Rp 5.000.000 (Line Manager Single Clearance)
     * Tier 2: Rp 5.000.001 - Rp 15.000.000 (Manager -> Finance Review)
     * Tier 3: > Rp 15.000.000 (Manager -> Finance -> Executive Director SLA Review)
     */
    public function getWaterfallTierAttribute(): array
    {
        $cost = (float) ($this->total_cost ?? 0);

        if ($cost <= 5000000) {
            return [
                'level' => 1,
                'name' => 'Tier 1: Otorisasi Atasan Langsung',
                'description' => 'Persetujuan langsung oleh Atasan Langsung (≤ Rp 5.000.000)',
                'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'stages' => ['Line Manager Review'],
            ];
        }

        if ($cost <= 15000000) {
            return [
                'level' => 2,
                'name' => 'Tier 2: Otorisasi Ganda (Manajer + Keuangan)',
                'description' => 'Diteruskan ke Bagian Keuangan (Rp 5 Jt – Rp 15 Jt)',
                'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                'stages' => ['Line Manager Review', 'Finance Review'],
            ];
        }

        return [
            'level' => 3,
            'name' => 'Tier 3: Waterfall Eksekutif (Persetujuan Direktur)',
            'description' => 'Pengajuan bernilai tinggi memerlukan persetujuan Direktur (> Rp 15.000.000)',
            'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
            'stages' => ['Line Manager Review', 'Finance Review', 'Director SLA Review'],
        ];
    }

    /**
     * Determine next approval stage using the Waterfall Account Limit rules.
     *
     * @return array{next_stage: string, overall_status: string, is_final: bool, message: string}
     */
    public function determineNextApprovalStage(): array
    {
        $cost = (float) ($this->total_cost ?? 0);
        $stage = $this->approval_stage ?? 'Line Manager Review';

        if (str_contains($stage, 'Manager')) {
            if ($cost <= 5000000) {
                return [
                    'next_stage' => 'Ticket Issued / Confirmed',
                    'overall_status' => 'booking_ready',
                    'is_final' => true,
                    'message' => "Pengajuan perjalanan #{$this->request_code} disetujui oleh Atasan Langsung sesuai batas otorisasi (≤ Rp 5.000.000). Izin penerbitan tiket diterbitkan.",
                ];
            }

            return [
                'next_stage' => 'Finance Review',
                'overall_status' => 'pending_approval',
                'is_final' => false,
                'message' => "Pengajuan perjalanan #{$this->request_code} disetujui oleh Atasan Langsung. Total biaya (Rp ".number_format($cost, 0, ',', '.').') melebihi batas Manajer; diteruskan ke Otorisasi Keuangan.',
            ];
        }

        if (str_contains($stage, 'Finance')) {
            if ($cost <= 15000000) {
                return [
                    'next_stage' => 'Ticket Issued / Confirmed',
                    'overall_status' => 'booking_ready',
                    'is_final' => true,
                    'message' => "Pengajuan perjalanan #{$this->request_code} disetujui oleh Keuangan Perusahaan sesuai batas otorisasi (≤ Rp 15.000.000). Izin penerbitan tiket diterbitkan.",
                ];
            }

            return [
                'next_stage' => 'Director SLA Review',
                'overall_status' => 'pending_approval',
                'is_final' => false,
                'message' => "Pengajuan perjalanan #{$this->request_code} diverifikasi oleh Keuangan. Pengeluaran bernilai tinggi (Rp ".number_format($cost, 0, ',', '.').') diteruskan ke Persetujuan SLA Direktur Utama.',
            ];
        }

        return [
            'next_stage' => 'Ticket Issued / Confirmed',
            'overall_status' => 'booking_ready',
            'is_final' => true,
            'message' => "Pengajuan perjalanan #{$this->request_code} memperoleh persetujuan final dari Direktur Utama. Izin penerbitan tiket diterbitkan.",
        ];
    }
}
