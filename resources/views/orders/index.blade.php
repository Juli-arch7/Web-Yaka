@extends('layouts.app')

@section('title', 'Pesanan Saya - Yaka Store')

@section('content')
<h1 class="text-3xl font-bold mb-8">Pesanan Saya</h1>

@if($orders->isEmpty())
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-box text-6xl text-gray-400 mb-4"></i>
    <h3 class="text-xl font-semibold mb-2">Belum Ada Pesanan</h3>
    <p class="text-gray-600 mb-6">Mulai berbelanja sekarang!</p>
    <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
        Belanja Sekarang
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($orders as $order)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4 pb-4 border-b">
            <div>
                <p class="text-sm text-gray-600">Invoice</p>
                <p class="font-bold text-lg">{{ $order->invoice_no }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Tanggal Pesanan</p>
                <p class="font-semibold">{{ $order->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Status</p>
                @if($order->status === 'pending')
                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded">
                    Menunggu Pembayaran
                </span>
                @elseif($order->status === 'processing')
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded">
                    Diproses
                </span>
                @elseif($order->status === 'completed')
                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded">
                    Selesai
                </span>
                @else
                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded">
                    Dibatalkan
                </span>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Item</p>
                <p class="font-semibold">{{ $order->items->sum('quantity') }} item</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('orders.show', $order) }}" class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
                Lihat Detail
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-8">
    {{ $orders->links() }}
</div>
@endif
@endsection