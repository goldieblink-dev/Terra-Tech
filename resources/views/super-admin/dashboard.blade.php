@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-purple-900 text-white rounded-2xl p-6 shadow-lg">
        <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="mt-2 text-purple-200">Anda berada di Dashboard Super Admin. Anda memiliki akses penuh ke seluruh manajemen pengguna, role, dan konfigurasi sistem.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Total Users</div>
            <div class="text-3xl font-extrabold text-purple-600 mt-2">1</div>
            <div class="text-xs text-gray-400 mt-1">Akun Terdaftar</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Active Roles</div>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">4</div>
            <div class="text-xs text-gray-400 mt-1">Super Admin, Admin, Operator, Editor</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">System Access</div>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">Full</div>
            <div class="text-xs text-gray-400 mt-1">Seluruh Perizinan Aktif</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">CMS Version</div>
            <div class="text-3xl font-extrabold text-slate-700 mt-2">v1.0</div>
            <div class="text-xs text-gray-400 mt-1">Terra Tech CMS Foundation</div>
        </div>
    </div>
</div>
@endsection
