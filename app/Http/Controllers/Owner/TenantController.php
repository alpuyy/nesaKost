<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BranchDorm;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Cabang yang dimiliki owner yang sedang login.
     */
    private function ownerBranches(Request $request)
    {
        return BranchDorm::query()
            ->whereHas('owner', fn ($query) =>
                $query->where('user_id', $request->user()->id)
            );
    }

    /**
     * Daftar seluruh penyewa di cabang milik owner.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $branchId = $request->query('branch');
        $status = $request->query('status');

        $branches = $this->ownerBranches($request)
            ->orderBy('name')
            ->get();

        $ownedBranchIds = $branches->pluck('id');

        // Pastikan filter cabang benar-benar milik owner
        if ($branchId !== null && $branchId !== '') {
            abort_unless($branches->contains('id', (int) $branchId), 404);
        }

        // Base query: Penyewa yang pernah atau sedang menyewa di cabang owner
        $baseQuery = Tenant::query()
            ->where(function ($query) use ($ownedBranchIds) {
                $query->whereHas('rents.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds))
                    ->orWhereHas('bookings.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds));
            });

        // Hitung statistik
        $totalTenants = (clone $baseQuery)->count('tenants.id');

        $activeTenants = (clone $baseQuery)->whereHas('rents', function ($rq) use ($ownedBranchIds) {
            $rq->where('status', 'active')
                ->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds));
        })->count('tenants.id');

        $completedTenants = max(0, $totalTenants - $activeTenants);

        // Query penyewa dengan filter
        $tenantsQuery = Tenant::query()
            ->with([
                'user',
                'rents' => function ($rq) use ($ownedBranchIds) {
                    $rq->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds))
                        ->with(['room.branch'])
                        ->latest('id');
                },
            ])
            ->where(function ($query) use ($ownedBranchIds) {
                $query->whereHas('rents.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds))
                    ->orWhereHas('bookings.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds));
            });

        // Filter kata kunci pencarian
        if ($search !== '') {
            $tenantsQuery->where(function ($query) use ($search) {
                $query->whereHas('user', function ($uq) use ($search) {
                    $uq->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                })
                ->orWhere('profession', 'like', "%{$search}%")
                ->orWhere('emergency_name', 'like', "%{$search}%")
                ->orWhereHas('rents.room', function ($rq) use ($search) {
                    $rq->where('room_code', 'like', "%{$search}%");
                });
            });
        }

        // Filter cabang kos
        if ($branchId !== null && $branchId !== '') {
            $tenantsQuery->where(function ($query) use ($branchId) {
                $query->whereHas('rents.room', fn ($rq) => $rq->where('branch_dorm_id', (int) $branchId))
                    ->orWhereHas('bookings.room', fn ($rq) => $rq->where('branch_dorm_id', (int) $branchId));
            });
        }

        // Filter status sewa
        if ($status === 'active') {
            $tenantsQuery->whereHas('rents', function ($rq) use ($ownedBranchIds) {
                $rq->where('status', 'active')
                    ->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds));
            });
        } elseif ($status === 'inactive') {
            $tenantsQuery->whereDoesntHave('rents', function ($rq) use ($ownedBranchIds) {
                $rq->where('status', 'active')
                    ->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds));
            });
        }

        $tenants = $tenantsQuery
            ->latest('tenants.id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.owner.tenants.index', [
            'tenants' => $tenants,
            'branches' => $branches,
            'search' => $search,
            'branchId' => $branchId,
            'status' => $status,
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'completedTenants' => $completedTenants,
        ]);
    }

    /**
     * Detail informasi penyewa beserta riwayat sewa & bookingnya.
     */
    public function show(Request $request, int $id): View
    {
        $branches = $this->ownerBranches($request)->get();
        $ownedBranchIds = $branches->pluck('id');

        $tenant = Tenant::query()
            ->with([
                'user',
                'rents' => function ($rq) use ($ownedBranchIds) {
                    $rq->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds))
                        ->with(['room.branch', 'booking.payments'])
                        ->latest('id');
                },
                'bookings' => function ($bq) use ($ownedBranchIds) {
                    $bq->whereHas('room', fn ($rm) => $rm->whereIn('branch_dorm_id', $ownedBranchIds))
                        ->with(['room.branch', 'payments'])
                        ->latest('id');
                },
            ])
            ->where(function ($query) use ($ownedBranchIds) {
                $query->whereHas('rents.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds))
                    ->orWhereHas('bookings.room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds));
            })
            ->findOrFail($id);

        $activeRent = $tenant->rents->firstWhere('status', 'active');

        return view('pages.owner.tenants.show', [
            'tenant' => $tenant,
            'activeRent' => $activeRent,
        ]);
    }
}
