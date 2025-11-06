@extends('layouts.admin')

@section('header', 'Kelola Kategori')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left py-3 px-6">Nama Kategori</th>
                <th class="text-center py-3 px-6">Jumlah Produk</th>
                <th class="text-center py-3 px-6">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-6 font-semibold">{{ $category->name }}</td>
                <td class="py-3 px-6 text-center">{{ $category->products_count }}</td>
                <td class="py-3 px-6">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Hapus kategori ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="py-8 text-center text-gray-600">Tidak ada kategori</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
