@extends('components.layouts.owner.container')

@section('title', 'Data Penyewa')

@section('main')
    <div class="space-y-6 text-slate-800">
        @include('pages.owner.tenants.partials.flash')

        {{-- Header Section --}}
        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Data Penyewa</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">
                    Kelola dan pantau informasi penghuni/penyewa, kontak darurat, dan status sewa aktif dari seluruh cabang kos milikmu.
                </p>
            </div>
        </section>

        {{-- Statistik Cards --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Penyewa</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold">{{ $totalTenants }} <span class="text-base font-medium text-slate-500">Orang</span></p>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Penyewa Aktif</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold">{{ $activeTenants }} <span class="text-base font-medium text-slate-500">Orang</span></p>
                    <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">
                        {{ $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100) : 0 }}% Sedang Menghuni
                    </span>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Riwayat Selesai</p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold">{{ $completedTenants }} <span class="text-base font-medium text-slate-500">Orang</span></p>
                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">Alumni Penghuni</span>
                </div>
            </div>
        </section>

        {{-- Table & Filters Section --}}
        <section class="overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
                <form method="GET" action="{{ route('owner.tenants.index') }}" class="flex w-full flex-col gap-2 md:max-w-4xl md:flex-row">
                    <input type="search" name="q" value="{{ $search }}"
                        placeholder="Cari nama, email, no HP, atau profesi..."
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
                        <option value="active" @selected($status === 'active')>Aktif Menyewa</option>
                        <option value="inactive" @selected($status === 'inactive')>Riwayat Selesai</option>
                    </select>

                    <button type="submit" class="rounded-lg bg-blue-900 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800">
                        Cari
                    </button>

                    @if ($search !== '' || $branchId || $status)
                        <a href="{{ route('owner.tenants.index') }}" class="rounded-lg bg-slate-100 px-4 py-3 text-center text-sm font-medium text-slate-600 hover:bg-slate-200">
                            Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('owner.tenants.index') }}" title="Muat ulang" class="self-end rounded-lg p-2 text-slate-500 hover:bg-slate-100">↻</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="bg-indigo-50 text-xs uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-6 py-4">Penyewa</th>
                            <th class="px-6 py-4">Kontak & Profesi</th>
                            <th class="px-6 py-4">Kontak Darurat</th>
                            <th class="px-6 py-4">Kamar & Cabang</th>
                            <th class="px-6 py-4">Periode Sewa</th>
                            <th class="px-6 py-4">Status Sewa</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tenants as $tenant)
                            @php
                                $activeRent = $tenant->rents->firstWhere('status', 'active');
                                $latestRent = $activeRent ?? $tenant->rents->first();
                            @endphp
                            <tr class="hover:bg-slate-50">
                                {{-- Nama Penyewa --}}
                                <td class="px-6 py-5">
                                    <a href="{{ route('owner.tenants.show', $tenant->id) }}" class="font-semibold text-slate-900 hover:text-blue-800">
                                        {{ $tenant->user?->full_name ?? '—' }}
                                    </a>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $tenant->user?->email }}</p>
                                </td>

                                {{-- Kontak & Profesi --}}
                                <td class="px-6 py-5">
                                    <p class="font-medium text-slate-800">{{ $tenant->user?->phone_number ?: '—' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $tenant->profession ?: 'Profesi belum diatur' }}</p>
                                </td>

                                {{-- Kontak Darurat --}}
                                <td class="px-6 py-5">
                                    @if($tenant->emergency_name)
                                        <p class="font-medium text-slate-800">{{ $tenant->emergency_name }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $tenant->emergency_number ?: 'No. HP belum diatur' }}</p>
                                    @else
                                        <span class="text-xs text-slate-400">Belum diatur</span>
                                    @endif
                                </td>

                                {{-- Kamar & Cabang --}}
                                <td class="px-6 py-5">
                                    @if($latestRent && $latestRent->room)
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-semibold text-slate-900">{{ $latestRent->room->room_code }}</span>
                                            <span class="text-xs text-slate-400">({{ $latestRent->room->type }})</span>
                                        </div>
                                        <p class="mt-0.5 text-xs font-medium text-blue-800">
                                            {{ $latestRent->room->branch?->name ?? 'Cabang tidak ditemukan' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-400">Belum ada sewa</span>
                                    @endif
                                </td>

                                {{-- Periode Sewa --}}
                                <td class="px-6 py-5">
                                    @if($activeRent)
                                        <p class="text-xs font-semibold text-slate-800">
                                            {{ $activeRent->start_date?->format('d M Y') }} s/d {{ $activeRent->end_date?->format('d M Y') }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">
                                            Sisa {{ max(0, (int) now()->diffInDays($activeRent->end_date, false)) }} hari
                                        </p>
                                    @elseif($latestRent)
                                        <span class="text-xs text-slate-500">
                                            Berakhir {{ $latestRent->end_date?->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Status Sewa --}}
                                <td class="px-6 py-5">
                                    @if($activeRent)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            Aktif Menyewa
                                        </span>
                                    @elseif($latestRent && $latestRent->status === 'scheduled')
                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            Menunggu Mulai
                                        </span>
                                    @elseif($latestRent && $latestRent->status === 'completed')
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Riwayat Selesai
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Belum Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('owner.tenants.show', $tenant->id) }}" class="font-semibold text-blue-800 hover:underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                    Belum ada data penyewa yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>
                    Menampilkan {{ $tenants->firstItem() ?? 0 }}–{{ $tenants->lastItem() ?? 0 }} dari {{ $tenants->total() }} penyewa
                </p>
                {{ $tenants->links() }}
            </div>
        </section>
    </div>
@endsection
