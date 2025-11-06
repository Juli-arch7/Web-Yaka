@extends('layouts.admin')

@section('header', 'Detail Pesanan - ' . $order->invoice_no)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="font-bold mb-2">Informasi Pembeli</h3>
            <p>{{ $order->user->username }}</p>
            <p>{{ $order->user->email }}</p>
            <p>{{ $order->user->phone }}</p>
        </div>
        <div>
            <h3 class="font-bold mb-2">Alamat Pengiriman</h3>
            <p>{{ $order->shipping_address }}</p>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="font-bold mb-2">Update Status Pesanan</h3>
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex gap-2">
            @csrf
            @method('PUT')
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button>
        </form>
    </div>

    <h3 class="font-bold mb-4">Detail Produk</h3>
    <table class="w-full mb-6">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-4">Produk</th>
                <th class="text-center py-3 px-4">Harga</th>
                <th class="text-center py-3 px-4">Jumlah</th>
                <th class="text-right py-3 px-4">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr class="border-b">
                <td class="py-3 px-4">{{ $item->product_name }}</td>
                <td class="py-3 px-4 text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-center">{{ $item->quantity }}</td>
                <td class="py-3 px-4 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-end">
        <div class="w-80 space-y-2">
            <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Ongkir</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection