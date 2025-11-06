@extends('layouts.admin')

@section('header', 'Edit Produk')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold mb-2">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Kategori</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Harga</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Stok</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Rincian</label>
                <textarea name="details" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('details', $product->details) }}</textarea>
            </div>

            <!-- Current Images -->
            @if($product->images->count() > 0)
            <div class="mb-4">
                <label class="block font-semibold mb-2">Gambar Saat Ini</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                    <div class="relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-24 object-cover rounded">
                        <form method="POST" action="{{ route('admin.product-images.destroy', $image) }}" class="absolute top-1 right-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-6">
                <label class="block font-semibold mb-2">Tambah Gambar Baru</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection