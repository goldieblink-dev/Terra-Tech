@extends('layouts.app')

@section('title', 'Kategori Informasi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kategori Informasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori yang digunakan untuk mengelompokkan artikel informasi.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('cms.information.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar Artikel</span>
            </a>
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.information-categories.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Kategori</span>
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Jumlah Artikel</th>
                        @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                            <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $category->description ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $category->posts_count }} artikel
                                </span>
                            </td>
                            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('cms.information-categories.edit', $category) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium rounded-lg transition">Edit</a>
                                    <form method="POST" action="{{ route('cms.information-categories.destroy', $category) }}" class="inline-block" onsubmit="return confirm('Hapus kategori ini? Artikel terkait tidak akan terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-medium rounded-lg transition">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada kategori informasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
