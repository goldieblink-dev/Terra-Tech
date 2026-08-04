@extends('layouts.app')

@section('title', 'Konfigurasi Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Profil Perusahaan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola identitas utama, logo, favicon, kontak, dan tautan media sosial Terra Tech.</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.company_profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Identitas Utama Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Identitas Utama Perusahaan</span>
            </h3>

            <!-- Company Name -->
            <div>
                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Perusahaan <span class="text-rose-500">*</span></label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $profile->company_name) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('company_name') border-rose-500 @enderror" required />
                @error('company_name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tagline -->
            <div>
                <label for="tagline" class="block text-sm font-semibold text-gray-700 mb-1">Slogan / Tagline</label>
                <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $profile->tagline) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('tagline') border-rose-500 @enderror" placeholder="Contoh: Solusi Teknologi Masa Depan" />
                @error('tagline')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Singkat Perusahaan</label>
                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('description') border-rose-500 @enderror" placeholder="Jelaskan profil ringkas perusahaan di sini...">{{ old('description', $profile->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Aset Visual (Logo & Favicon) Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Logo & Favicon Perusahaan</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload Logo -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">Logo Perusahaan</label>

                    @if($profile->logo_url)
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center space-x-4">
                            <img src="{{ $profile->logo_url }}" alt="Logo Current" class="h-12 w-auto object-contain bg-white p-1 rounded-lg border border-gray-200" />
                            <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded">Logo Aktif</span>
                        </div>
                    @endif

                    <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    <p class="text-xs text-gray-400">Format: PNG, JPG, JPEG, WEBP, SVG. Maksimal 2MB.</p>
                    @error('logo')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Favicon -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">Favicon</label>

                    @if($profile->favicon_url)
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center space-x-4">
                            <img src="{{ $profile->favicon_url }}" alt="Favicon Current" class="h-8 w-8 object-contain bg-white p-1 rounded-lg border border-gray-200" />
                            <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded">Favicon Aktif</span>
                        </div>
                    @endif

                    <input type="file" name="favicon" id="favicon" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml,image/x-icon" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    <p class="text-xs text-gray-400">Format: ICO, PNG, JPG, WEBP, SVG. Maksimal 1MB.</p>
                    @error('favicon')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Informasi Kontak & Alamat Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Kontak & Alamat Kantor</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Resmi</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $profile->email) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('email') border-rose-500 @enderror" placeholder="info@terratech.test" />
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $profile->phone) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('phone') border-rose-500 @enderror" placeholder="+62 21 555 1234" />
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Kantor Lengkap</label>
                <textarea name="address" id="address" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('address') border-rose-500 @enderror" placeholder="Alamat jalan, kota, provinsi, kode pos">{{ old('address', $profile->address) }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tautan Media Sosial Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span>Media Sosial Perusahaan</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Facebook -->
                <div>
                    <label for="facebook_url" class="block text-sm font-semibold text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $profile->facebook_url) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('facebook_url') border-rose-500 @enderror" placeholder="https://facebook.com/..." />
                    @error('facebook_url')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label for="instagram_url" class="block text-sm font-semibold text-gray-700 mb-1">Instagram URL</label>
                    <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('instagram_url') border-rose-500 @enderror" placeholder="https://instagram.com/..." />
                    @error('instagram_url')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LinkedIn -->
                <div>
                    <label for="linkedin_url" class="block text-sm font-semibold text-gray-700 mb-1">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('linkedin_url') border-rose-500 @enderror" placeholder="https://linkedin.com/company/..." />
                    @error('linkedin_url')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube -->
                <div>
                    <label for="youtube_url" class="block text-sm font-semibold text-gray-700 mb-1">YouTube URL</label>
                    <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $profile->youtube_url) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('youtube_url') border-rose-500 @enderror" placeholder="https://youtube.com/@..." />
                    @error('youtube_url')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Action -->
        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
