@extends('layouts.admin')

@section('header', 'Edit Kategori')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label class="block font-semibold mb-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection