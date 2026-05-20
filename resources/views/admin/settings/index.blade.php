@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <h1 class="text-2xl font-bold">Site Settings</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-6">
                <!-- General Settings -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">General Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('site_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="show_site_name" id="show_site_name" value="1" {{ old('show_site_name', $settings->show_site_name) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                            <label for="show_site_name" class="ml-2 text-sm text-gray-700">Show Site Name in Header</label>
                        </div>
                    </div>
                </div>

                <!-- Branding -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Branding</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            @if($settings->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="h-12 object-contain">
                                </div>
                            @endif
                            <input type="file" name="logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Recommended: PNG, SVG or JPG with transparent background</p>
                            @error('logo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                            @if($settings->favicon)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="h-8 w-8 object-contain">
                                </div>
                            @endif
                            <input type="file" name="favicon" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Recommended: ICO or PNG (32x32 or 16x16)</p>
                            @error('favicon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Home Banner Background</label>
                            @if($settings->banner_background)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $settings->banner_background) }}" alt="Home Banner Background" class="h-24 w-full max-w-md object-cover rounded-lg border">
                                </div>
                            @endif
                            <input type="file" name="banner_background" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Used on the homepage welcome banner. Recommended: 1600x700 JPG/PNG/WEBP.</p>
                            @error('banner_background') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contact Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('contact_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Address</label>
                            <textarea name="contact_address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('contact_address', $settings->contact_address) }}</textarea>
                            @error('contact_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('whatsapp_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Social Media</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                            <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('facebook_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('instagram_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                            <input type="url" name="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            @error('twitter_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Footer</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                        <textarea name="footer_text" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('footer_text', $settings->footer_text) }}</textarea>
                        @error('footer_text') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-sm font-medium">Save Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
