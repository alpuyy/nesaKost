<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BranchDorm;
use App\Models\Rent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentController extends Controller
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
     * Daftar seluruh transaksi sewa kamar di cabang milik owner.
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

        // Base query: Sewa yang kamarnya berada di cabang milik owner
        $baseQuery = Rent::query()
            ->whereHas('room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds));

        // Hitung statistik transaksi sewa
        $totalRents = (clone $baseQuery)->count();
        $activeRents = (clone $baseQuery)->where('status', 'active')->count();
        $scheduledRents = (clone $baseQuery)->where('status', 'scheduled')->count();
        $completedRents = (clone $baseQuery)->where('status', 'completed')->count();
        $totalRevenue = (clone $baseQuery)->whereIn('status', ['active', 'completed'])->sum('total_price');

        // Query data sewa dengan eager loading
        $rentsQuery = Rent::query()
            ->with([
                'tenant.user',
                'room.branch',
                'booking',
            ])
            ->whereHas('room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds));

        // Filter kata kunci pencarian
        if ($search !== '') {
            $rentsQuery->where(function ($query) use ($search) {
                $query->where('rent_code', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($rq) use ($search) {
                        $rq->where('room_code', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhereHas('branch', fn ($bq) => $bq->where('name', 'like', "%{$search}%"));
                    })
                    ->orWhereHas('tenant.user', function ($uq) use ($search) {
                        $uq->where('fname', 'like', "%{$search}%")
                            ->orWhere('lname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter cabang kos
        if ($branchId !== null && $branchId !== '') {
            $rentsQuery->whereHas('room', fn ($rq) => $rq->where('branch_dorm_id', (int) $branchId));
        }

        // Filter status sewa
        if ($status !== null && $status !== '') {
            $rentsQuery->where('status', $status);
        }

        $rents = $rentsQuery
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.owner.rents.index', [
            'rents' => $rents,
            'branches' => $branches,
            'search' => $search,
            'branchId' => $branchId,
            'status' => $status,
            'totalRents' => $totalRents,
            'activeRents' => $activeRents,
            'scheduledRents' => $scheduledRents,
            'completedRents' => $completedRents,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    /**
     * Detail transaksi sewa kamar beserta rincian kamar, penyewa, dan pembayaran.
     */
    public function show(Request $request, int $id): View
    {
        $branches = $this->ownerBranches($request)->get();
        $ownedBranchIds = $branches->pluck('id');

        $rent = Rent::query()
            ->with([
                'tenant.user',
                'room.branch',
                'booking.payments',
            ])
            ->whereHas('room', fn ($q) => $q->whereIn('branch_dorm_id', $ownedBranchIds))
            ->findOrFail($id);

        return view('pages.owner.rents.show', [
            'rent' => $rent,
        ]);
    }
}
