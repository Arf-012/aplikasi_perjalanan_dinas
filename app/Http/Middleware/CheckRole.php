<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request and ensure the user possesses the required role(s).
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->route('login');
        }

        // Flatten any comma-separated string parameters (e.g. 'role:manager,finance,travel_admin,direktur')
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $part) {
                $trimmed = strtolower(trim($part));
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Support special alias "approver" representing all 4 approval roles
        if (in_array('approver', $allowedRoles, true) || in_array('approvers', $allowedRoles, true)) {
            $allowedRoles = array_unique(array_merge($allowedRoles, ['manager', 'finance', 'travel_admin', 'direktur']));
        }

        // Check if user has any of the permitted roles
        if (! empty($allowedRoles) && ! $user->hasRole($allowedRoles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki otoritas untuk mengakses fitur ini.',
                ], 403);
            }

            return redirect()->route('dashboard')->with(
                'error',
                'Akses ditolak: Fitur persetujuan hanya dapat diakses oleh peran Manajer, Keuangan, Travel Admin, dan Direktur.'
            );
        }

        return $next($request);
    }
}
