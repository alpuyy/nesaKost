@extends('components.layouts.owner.container')

@section('title', 'Data Sewa')

@section('main')
    <div class="space-y-6 text-slate-800">
        @include('pages.owner.rents.partials.flash')

        {{-- Header Section --}}
        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Data Sewa</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">
                    Pantau dan kelola seluruh transaksi dan kontrak sewa kamar yang sedang berjalan maupun riwayat sewa di kos milikmu.
                </p>
            </div>
        </section>

        {{-- Statistik Cards --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Transaksi Sewa</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold">{{ $totalRents }} <span class="text-base font-medium text-slate-500">Kontrak</span></p>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Sewa Aktif Berjalan</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold">{{ $activeRents }} <span class="text-base font-medium text-slate-500">Kamar</span></p>
                    <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">
                        {{ $totalRents > 0 ? round(($activeRents / $totalRents) * 100) : 0 }}% Aktif
                    </span>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Nilai Kontrak</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-2xl font-bold text-slate-900">Rp {{ number_format((float) $totalRevenue, 0, ',', '.') }}</p>
                    <span class="rounded-md bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                        {{ $completedRents }} Selesai • {{ $scheduledRents }} Menunggu
                    </span>
                </div>
            </div>
        </section>

        {{-- Table & Filters Section --}}
        <section class="overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
                <form method="GET" action="{{ route('owner.rents.index') }}" class="flex w-full flex-col gap-2 md:max-w-4xl md:flex-row">
                    <input type="search" name="q" value="{{ $search }}"
                        placeholder="Cari kode sewa, kamar, cabang, atau nama penyewa..."
                        class="w-full rounded-lg border-0 bg-indigo-50 px-4 py-3 text-sm outline-none ring-blue-300 focus:ring-2">

                    <select name="branch" class="rounded-lg border-0 bg-indigo-50 px-4 py-3 text-sm outline-none ring-blue-300 focus:ring-2">
                        <option value="">Semua cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) $branchId === (string) $branch->id)>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-lg border-0 bg-indigo-50 px-4 py-3 text-sm outline-none ring-blue-300 focus:ring-2">
                        <option value="">Semua status</option>
                        <option value="active" @selected($status === 'active')>Aktif</option>
                        <option value="scheduled" @selected($status === 'scheduled')>Terjadwal</option>
                        <option value="completed" @selected($status === 'completed')>Selesai</option>
                        <option value="cancelled" @selected($status === 'cancelled')>Dibatalkan</option>
                    </select>

                    <button type="submit" class="rounded-lg bg-blue-900 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800">
                        Cari
                    </button>

                    @if ($search !== '' || $branchId || $status)
                        <a href="{{ route('owner.rents.index') }}" class="rounded-lg bg-slate-100 px-4 py-3 text-center text-sm font-medium text-slate-600 hover:bg-slate-200">
                            Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('owner.rents.index') }}" title="Muat ulang" class="self-end rounded-lg p-2 text-slate-500 hover:bg-slate-100">↻</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="bg-indigo-50 text-xs uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-6 py-4">Kode Sewa</th>
                            <th class="px-6 py-4">Penyewa</th>
                            <th class="px-6 py-4">Kamar & Cabang</th>
                            <th class="px-6 py-4">Periode Sewa</th>
                            <th class="px-6 py-4">Total Biaya</th>
                            <th class="px-6 py-4">Status Sewa</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rents as $rent)
                            @php
                                $statusClasses = [
                                    'active' => 'bg-emerald-100 text-emerald-800',
                                    'scheduled' => 'bg-amber-100 text-amber-800',
                                    'completed' => 'bg-slate-100 text-slate-600',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];

                                $statusLabels = [
                                    'active' => 'Aktif',
                                    'scheduled' => 'Terjadwal',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50">
                                {{-- Kode Sewa --}}
                                <td class="px-6 py-5">
                                    <a href="{{ route('owner.rents.show', $rent->id) }}" class="font-semibold text-slate-900 hover:text-blue-800">
                                        {{ $rent->rent_code ?: 'RENT-' . $rent->id }}
                                    </a>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $rent->created_at?->format('d M Y') }}</p>
                                </td>

                                {{-- Penyewa --}}
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-slate-900">
                                        {{ $rent->tenant?->user?->full_name ?? '—' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $rent->tenant?->user?->phone_number ?: $rent->tenant?->user?->email }}</p>
                                </td>

                                {{-- Kamar & Cabang --}}
                                <td class="px-6 py-5">
                                    @if($rent->room)
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-semibold text-slate-900">{{ $rent->room->room_code }}</span>
                                            <span class="text-xs text-slate-400">({{ $rent->room->type }})</span>
                                        </div>
                                        <p class="mt-0.5 text-xs font-medium text-blue-800">
                                            {{ $rent->room->branch?->name ?? 'Cabang tidak ditemukan' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-400">Kamar tidak ditemukan</span>
                                    @endif
                                </td>

                                {{-- Periode Sewa --}}
                                <td class="px-6 py-5">
                                    <p class="font-medium text-slate-800">
                                        {{ $rent->start_date?->format('d M Y') }} s/d {{ $rent->end_date?->format('d M Y') }}
                                    </p>
                                    @if($rent->status === 'active')
                                        <p class="mt-0.5 text-[11px] font-semibold text-emerald-700">
                                            Sisa {{ max(0, (int) now()->diffInDays($rent->end_date, false)) }} hari
                                        </p>
                                    @endif
                                </td>

                                {{-- Total Biaya --}}
                                <td class="px-6 py-5 font-semibold text-slate-900">
                                    Rp {{ number_format((float) $rent->total_price, 0, ',', '.') }}
                                </td>

                                {{-- Status Sewa --}}
                                <td class="px-6 py-5">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$rent->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $statusLabels[$rent->status] ?? ucfirst($rent->status) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('owner.rents.show', $rent->id) }}" class="font-semibold text-blue-800 hover:underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                    Belum ada data transaksi sewa yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>
                    Menampilkan {{ $rents->firstItem() ?? 0 }}–{{ $rents->lastItem() ?? 0 }} dari {{ $rents->total() }} kontrak sewa
                </p>
                {{ $rents->links() }}
            </div>
        </section>
    </div>
@endsection
