@extends('layouts.admin')

@section('header', 'Kelola Pesanan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice/nama..." 
               class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 border rounded-lg" onchange="this.form.submit()">
            <option value="all">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-6">Invoice</th>
                <th class="text-left py-3 px-6">Nama</th>
                <th class="text-left py-3 px-6">Tanggal</th>
                <th class="text-right py-3 px-6">Total</th>
                <th class="text-center py-3 px-6">Status</th>
                <th class="text-center py-3 px-6">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <td class="py-3 px-6 font-semibold">{{ $order->invoice_no }}</td>
                <td class="py-3 px-6">{{ $order->user->username }}</td>
                <td class="py-3 px-6">{{ $order->created_at->format('d M Y') }}</td>
                <td class="py-3 px-6 text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-center">
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
                <td class="py-3 px-6 text-center">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-700">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
                <td colspan="6" class="py-8 text-center text-gray-600">Tidak ada pesanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection