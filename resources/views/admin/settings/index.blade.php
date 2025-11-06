@extends('layouts.admin')

@section('header', 'Pengaturan Toko')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold mb-2">Nama Toko</label>
                <input type="text" name="store_name" value="{{ $settings->store_name }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Email Toko</label>
                <input type="email" name="email" value="{{ $settings->email }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">No. HP / WhatsApp</label>
                <input type="text" name="phone" value="{{ $settings->phone }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2">Alamat Toko</label>
                <textarea name="address" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $settings->address }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection