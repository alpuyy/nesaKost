@extends('components.layouts.owner.container')

@section('title', 'Detail Sewa - ' . ($rent->rent_code ?: 'RENT-' . $rent->id))

@section('main')
    <div class="space-y-6 text-slate-800">
        @include('pages.owner.rents.partials.flash')

        @php
            $statusClasses = [
                'active' => 'bg-emerald-100 text-emerald-800',
                'scheduled' => 'bg-amber-100 text-amber-800',
                'completed' => 'bg-slate-100 text-slate-600',
                'cancelled' => 'bg-red-100 text-red-800',
            ];

            $statusLabels = [
                'active' => 'Aktif Berjalan',
                'scheduled' => 'Terjadwal (Menunggu Mulai)',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
        @endphp

        {{-- Header Breadcrumb & Title --}}
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                <a href="{{ route('owner.rents.index') }}" class="hover:text-blue-800">Data Sewa</a> / 
                <span class="font-semibold text-blue-800">Detail Sewa</span>
            </p>
            <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ $rent->rent_code ?: 'RENT-' . $rent->id }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <span class="font-semibold text-blue-800">Kamar {{ $rent->room?->room_code }}</span>
                        <span>•</span>
                        <span>{{ $rent->room?->branch?->name }}</span>
                        <span>•</span>
                        <span>Penyewa: {{ $rent->tenant?->user?->full_name }}</span>
                        <span>•</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$rent->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $statusLabels[$rent->status] ?? ucfirst($rent->status) }}
                        </span>
                    </div>
                </div>

                @if($rent->tenant?->user?->phone_number)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $rent->tenant->user->phone_number)) }}" 
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                        <span>Hubungi WhatsApp Penyewa</span>
                    </a>
                @endif
            </div>
        </section>

        {{-- Main Content Grid --}}
        <section class="grid gap-6 lg:grid-cols-3">
            {{-- Kolom Kiri: Rincian Lengkap --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Card 1: Informasi Kontrak Sewa --}}
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Informasi Kontrak Sewa</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kode Sewa</p>
                            <p class="mt-1 text-base font-bold text-slate-900">{{ $rent->rent_code ?: 'RENT-' . $rent->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Sewa</p>
                            <div class="mt-1">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$rent->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $statusLabels[$rent->status] ?? ucfirst($rent->status) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Mulai</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->start_date?->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Berakhir</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->end_date?->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Harga Sewa</p>
                            <p class="mt-1 text-2xl font-bold text-blue-900">
                                Rp {{ number_format((float) $rent->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Durasi Kontrak</p>
                            <p class="mt-1 font-medium text-slate-900">
                                {{ $rent->start_date && $rent->end_date ? $rent->start_date->diffInMonths($rent->end_date) : 0 }} Bulan
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Informasi Kamar & Cabang --}}
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">Informasi Kamar & Properti</h2>
                        @if($rent->room)
                            <a href="{{ route('owner.rooms.show', $rent->room->id) }}" class="text-xs font-semibold text-blue-800 hover:underline">
                                Lihat Detail Kamar &rarr;
                            </a>
                        @endif
                    </div>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kode & Tipe Kamar</p>
                            <p class="mt-1 font-bold text-slate-900">
                                {{ $rent->room?->room_code }} ({{ $rent->room?->type }})
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cabang Kos</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $rent->room?->branch?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Harga Kamar per Bulan</p>
                            <p class="mt-1 font-medium text-slate-900">
                                Rp {{ number_format((float) ($rent->room?->monthly_price ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Alamat Cabang</p>
                            <p class="mt-1 text-xs text-slate-600">{{ $rent->room?->branch?->address ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Informasi Penyewa --}}
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">Informasi Penyewa</h2>
                        @if($rent->tenant)
                            <a href="{{ route('owner.tenants.show', $rent->tenant->id) }}" class="text-xs font-semibold text-blue-800 hover:underline">
                                Lihat Profil Penyewa &rarr;
                            </a>
                        @endif
                    </div>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Lengkap</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->user?->full_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->user?->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Telepon</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->user?->phone_number ?: 'Belum diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profesi</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->profession ?: 'Belum diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kontak Darurat</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->emergency_name ?: 'Belum diatur' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">No. HP Darurat</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $rent->tenant?->emergency_number ?: 'Belum diatur' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Informasi Booking & Pembayaran Terkait --}}
                @if($rent->booking)
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                        <div class="border-b border-slate-100 p-6">
                            <h2 class="text-lg font-bold text-slate-900">Rincian Booking & Pembayaran Asal</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Transaksi sewa ini terbentuk dari pengajuan pemesanan kamar berikut:
                            </p>
                            <div class="mt-3 flex flex-wrap gap-4 text-xs">
                                <div>
                                    <span class="text-slate-400">Kode Booking:</span>
                                    <span class="font-bold text-slate-800">{{ $rent->booking->booking_code ?: 'BK-' . $rent->booking->id }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Tipe Booking:</span>
                                    <span class="font-semibold capitalize text-slate-800">{{ $rent->booking->booking_type }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Status Booking:</span>
                                    <span class="font-semibold text-emerald-800">{{ ucfirst($rent->booking->status) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Pembayaran --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-indigo-50 text-xs uppercase tracking-wide text-slate-600">
                                    <tr>
                                        <th class="px-6 py-4">ID Pembayaran</th>
                                        <th class="px-6 py-4">Metode / Channel</th>
                                        <th class="px-6 py-4">Nominal</th>
                                        <th class="px-6 py-4">Status Bayar</th>
                                        <th class="px-6 py-4">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($rent->booking->payments as $payment)
                                        @php
                                            $payStatusClasses = [
                                                'paid' => 'bg-emerald-100 text-emerald-800',
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'expired' => 'bg-slate-100 text-slate-600',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-6 py-4 font-semibold text-slate-900">
                                                #{{ $payment->id }}
                                            </td>
                                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                                {{ $payment->payment_channel ?: ($payment->payment_method ?: 'Virtual Account') }}
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-900">
                                                Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $payStatusClasses[$payment->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-slate-500">
                                                {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : $payment->created_at?->format('d M Y, H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-6 text-center text-slate-400">
                                                Belum ada catatan pembayaran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Aside Ringkasan --}}
            <aside class="h-fit rounded-xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Kontrak</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">ID Sewa</dt>
                        <dd class="mt-1 font-semibold text-slate-900">#{{ $rent->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status Sewa</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $statusLabels[$rent->status] ?? ucfirst($rent->status) }}
                        </dd>
                    </div>
                    @if($rent->status === 'active')
                        <div>
                            <dt class="text-slate-500">Sisa Waktu</dt>
                            <dd class="mt-1 font-bold text-emerald-800">
                                {{ max(0, (int) now()->diffInDays($rent->end_date, false)) }} hari lagi
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-slate-500">Kamar</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $rent->room?->room_code }} ({{ $rent->room?->type }})
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Cabang Kos</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $rent->room?->branch?->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Dibuat Pada</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $rent->created_at?->format('d M Y, H:i') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $rent->updated_at?->format('d M Y, H:i') ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <a href="{{ route('owner.rents.index') }}" 
                   class="mt-6 inline-flex w-full justify-center rounded-lg bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Kembali ke daftar
                </a>
            </aside>
        </section>
    </div>
@endsection
