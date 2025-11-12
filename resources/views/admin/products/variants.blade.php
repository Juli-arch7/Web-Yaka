@extends('layouts.admin')

@section('header', 'Kelola Varian - ' . $product->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Produk
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-xl font-bold mb-4">Tambah Varian Baru</h3>
    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @csrf
        <div>
            <label class="block font-semibold mb-2">Ukuran</label>
            <select name="size" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Pilih Ukuran</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="XXL">XXL</option>
                <option value="XXXL">XXXL</option>
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-2">Warna</label>
            <input type="text" name="color" placeholder="Hitam, Putih, dll"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block font-semibold mb-2">Stok</label>
            <input type="number" name="stock" value="0" min="0"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Tambah
            </button>
        </div>
    </form>
</div>


<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-6">Ukuran</th>
                <th class="text-left py-3 px-6">Warna</th>
                <th class="text-center py-3 px-6">Stok</th>
                <th class="text-center py-3 px-6">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($product->variants as $variant)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-6">
                    <span class="font-semibold">{{ $variant->size }}</span>
                </td>
                <td class="py-3 px-6">{{ $variant->color }}</td>
                <td class="py-3 px-6 text-center">
                    <form method="POST" action="{{ route('admin.variants.update', $variant) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="size" value="{{ $variant->size }}">
                        <input type="hidden" name="color" value="{{ $variant->color }}">
                        <input type="number" name="stock" value="{{ $variant->stock }}" min="0"
                               class="w-20 px-2 py-1 border rounded text-center"
                               onchange="this.form.submit()">
                    </form>
                </td>
                <td class="py-3 px-6 text-center">
                    <form method="POST" action="{{ route('admin.variants.destroy', $variant) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Hapus varian ini?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-600">
                    <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
                    <p>Belum ada varian. Tambahkan varian di atas.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($product->variants->count() > 0)
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
        <div>
            <p class="font-semibold text-blue-900">Total Varian: {{ $product->variants->count() }}</p>
            <p class="text-sm text-blue-800 mt-1">Total Stok Semua Varian: {{ $product->variants->sum('stock') }}</p>
        </div>
    </div>
</div>
@endif
@endsection