@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mb-1">Total Pesanan</p>
                <p class="text-3xl font-bold">{{ $totalOrders }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mb-1">Total Pemasukan</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <i class="fas fa-money-bill text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mb-1">Pesanan Pending</p>
                <p class="text-3xl font-bold">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Riwayat Pesanan Terbaru</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3">Invoice</th>
                    <th class="text-left py-3">Nama</th>
                    <th class="text-left py-3">Tanggal</th>
                    <th class="text-right py-3">Total</th>
                    <th class="text-center py-3">Status</th>
                    <th class="text-center py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 font-semibold">{{ $order->invoice_no }}</td>
                    <td class="py-3">{{ $order->user->username }}</td>
                    <td class="py-3">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="py-3 text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="py-3 text-center">
                        @if($order->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded">Pending</span>
                        @elseif($order->status === 'processing')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded">Diproses</span>
                        @elseif($order->status === 'completed')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded">Selesai</span>
                        @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-700">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-600">Belum ada pesanan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection