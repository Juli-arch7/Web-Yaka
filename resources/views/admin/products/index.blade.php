@extends('layouts.admin')

@section('header', 'Kelola Produk')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2">
        <input type="text" 
               name="search" 
               value="{{ request('search') }}"
               placeholder="Cari produk..."
               class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
            Cari
        </button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Tambah Produk
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-6">Gambar</th>
                <th class="text-left py-3 px-6">Nama Produk</th>
                <th class="text-left py-3 px-6">Kategori</th>
                <th class="text-right py-3 px-6">Harga</th>
                <th class="text-center py-3 px-6">Stok</th>
                <th class="text-center py-3 px-6">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-6">
                    @if($product->primaryImage)
                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                         alt="{{ $product->name }}"
                         class="w-16 h-16 object-cover rounded">
                    @else
                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                    @endif
                </td>
                <td class="py-3 px-6 font-semibold">{{ $product->name }}</td>
                <td class="py-3 px-6">{{ $product->category->name }}</td>
                <td class="py-3 px-6 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-center">{{ $product->stock }}</td>
                <td class="py-3 px-6">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.products.variants', $product) }}" class="text-green-600 hover:text-green-700">
                            <i class="fas fa-tags"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Hapus produk ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-600">Tidak ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection