@extends('components.layouts.owner.container')

@section('title', 'Detail Penyewa - ' . ($tenant->user?->full_name ?? 'Penyewa'))

@section('main')
    <div class="space-y-6 text-slate-800">
        @include('pages.owner.tenants.partials.flash')

        {{-- Header Breadcrumb & Title --}}
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                <a href="{{ route('owner.tenants.index') }}" class="hover:text-blue-800">Data Penyewa</a> / 
                <span class="font-semibold text-blue-800">Detail Penyewa</span>
            </p>
            <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ $tenant->user?->full_name ?? 'Penyewa' }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <span class="font-semibold text-blue-800">{{ $tenant->profession ?: 'Profesi belum diatur' }}</span>
                        <span>•</span>
                        <span>{{ $tenant->user?->email }}</span>
                        <span>•</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $activeRent ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $activeRent ? 'Sedang Menyewa' : 'Tidak Ada Sewa Aktif' }}
                        </span>
                    </div>
                </div>

                @if($tenant->user?->phone_number)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $tenant->user->phone_number)) }}" 
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                        <span>Hubungi WhatsApp</span>
                    </a>
                @endif
            </div>
        </section>

        {{-- Main Content Grid --}}
        <section class="grid gap-6 lg:grid-cols-3">
            {{-- Kolom Kiri: Rincian Lengkap --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Card 1: Informasi Pribadi & Kontak --}}
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Informasi Pribadi & Kontak</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Lengkap</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->user?->full_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->user?->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Telepon / WhatsApp</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->user?->phone_number ?: 'Belum diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profesi / Pekerjaan</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->profession ?: 'Belum diisi' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Alamat Asal</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->user?->address ?: 'Belum diisi' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Kontak Darurat --}}
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Kontak Darurat</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Kontak Darurat</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->emergency_name ?: 'Belum diatur' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Telepon Darurat</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $tenant->emergency_number ?: 'Belum diatur' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Sewa Aktif Saat Ini (Jika Ada) --}}
                @if($activeRent)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-emerald-950">Sewa Aktif Saat Ini</h2>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                Sedang Berjalan
                            </span>
                        </div>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Kamar & Cabang</p>
                                <p class="mt-1 text-lg font-bold text-slate-900">
                                    {{ $activeRent->room?->room_code }} ({{ $activeRent->room?->type }})
                                </p>
                                <p class="text-xs font-medium text-blue-900">{{ $activeRent->room?->branch?->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Total Harga Sewa</p>
                                <p class="mt-1 text-lg font-bold text-slate-900">
                                    Rp {{ number_format((float) $activeRent->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Periode Sewa</p>
                                <p class="mt-1 font-medium text-slate-900">
                                    {{ $activeRent->start_date?->format('d M Y') }} s/d {{ $activeRent->end_date?->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Sisa Waktu</p>
                                <p class="mt-1 font-bold text-emerald-900">
                                    {{ max(0, (int) now()->diffInDays($activeRent->end_date, false)) }} hari lagi
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Card 4: Riwayat Sewa Kamar --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-6">
                        <h2 class="text-lg font-bold text-slate-900">Riwayat Transaksi Sewa</h2>
                        <p class="mt-1 text-xs text-slate-500">Seluruh riwayat sewa kamar penyewa di cabang milik kamu.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-indigo-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th class="px-6 py-4">Kode Sewa</th>
                                    <th class="px-6 py-4">Kamar & Cabang</th>
                                    <th class="px-6 py-4">Periode</th>
                                    <th class="px-6 py-4">Total Biaya</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tenant->rents as $rent)
                                    @php
                                        $rentStatusClasses = [
                                            'active' => 'bg-emerald-100 text-emerald-800',
                                            'scheduled' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-slate-100 text-slate-600',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $rentStatusLabels = [
                                            'active' => 'Aktif',
                                            'scheduled' => 'Terjadwal',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 font-semibold text-slate-900">
                                            {{ $rent->rent_code ?: 'RENT-' . $rent->id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-slate-900">{{ $rent->room?->room_code }}</p>
                                            <p class="text-xs text-blue-800">{{ $rent->room?->branch?->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-600">
                                            {{ $rent->start_date?->format('d M Y') }} s/d {{ $rent->end_date?->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-slate-900">
                                            Rp {{ number_format((float) $rent->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $rentStatusClasses[$rent->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ $rentStatusLabels[$rent->status] ?? ucfirst($rent->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                            Belum ada riwayat sewa kamar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Card 5: Riwayat Pengajuan Booking --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-6">
                        <h2 class="text-lg font-bold text-slate-900">Riwayat Pengajuan Booking</h2>
                        <p class="mt-1 text-xs text-slate-500">Catatan pengajuan pemesanan kamar oleh penyewa.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-indigo-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th class="px-6 py-4">Kode Booking</th>
                                    <th class="px-6 py-4">Kamar</th>
                                    <th class="px-6 py-4">Tipe & Durasi</th>
                                    <th class="px-6 py-4">Harga</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tenant->bookings as $booking)
                                    @php
                                        $bookingStatusClasses = [
                                            'confirmed' => 'bg-emerald-100 text-emerald-800',
                                            'pending_payment' => 'bg-amber-100 text-amber-800',
                                            'expired' => 'bg-slate-100 text-slate-600',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $bookingStatusLabels = [
                                            'confirmed' => 'Terkonfirmasi',
                                            'pending_payment' => 'Menunggu Bayar',
                                            'expired' => 'Kedaluwarsa',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 font-semibold text-slate-900">
                                            {{ $booking->booking_code ?: 'BK-' . $booking->id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-slate-900">{{ $booking->room?->room_code }}</p>
                                            <p class="text-xs text-blue-800">{{ $booking->room?->branch?->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-600">
                                            <span class="capitalize">{{ $booking->booking_type }}</span> ({{ $booking->duration_month }} bulan)
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-slate-900">
                                            Rp {{ number_format((float) $booking->price_snapshot, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingStatusClasses[$booking->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ $bookingStatusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                            Belum ada riwayat booking.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Aside Ringkasan --}}
            <aside class="h-fit rounded-xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Penyewa</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">ID Penyewa</dt>
                        <dd class="mt-1 font-semibold text-slate-900">#{{ $tenant->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status Penghuni</dt>
                        <dd class="mt-1 font-semibold {{ $activeRent ? 'text-emerald-700' : 'text-slate-700' }}">
                            {{ $activeRent ? 'Penghuni Aktif' : 'Tidak Sedang Menyewa' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Kamar Aktif Saat Ini</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $activeRent ? $activeRent->room?->room_code . ' - ' . $activeRent->room?->branch?->name : 'Tidak ada' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Total Transaksi Sewa</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $tenant->rents->count() }} kali</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Total Booking Diajukan</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $tenant->bookings->count() }} kali</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Terdaftar Sejak</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $tenant->created_at?->format('d M Y, H:i') ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <a href="{{ route('owner.tenants.index') }}" 
                   class="mt-6 inline-flex w-full justify-center rounded-lg bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Kembali ke daftar
                </a>
            </aside>
        </section>
    </div>
@endsection
