@extends('owner.layout')

@php
    $editingSubmission = $submission !== null;
    $editingBusiness = $business !== null;
    $formTitle = $editingSubmission
        ? 'Update your submission'
        : ($editingBusiness ? 'Request a listing edit' : 'Add a business');
    $formAction = $editingSubmission
        ? route('owner.submissions.update', $submission)
        : ($editingBusiness ? route('owner.businesses.update', $business) : route('owner.businesses.store'));
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
@endphp

@section('title', $formTitle.' | OsunBiz')

@section('content')
    <div class="max-w-4xl">
        <a href="{{ route('owner.dashboard') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-950">← Back to dashboard</a>
        <div class="mt-4">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Business listing</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight">{{ $formTitle }}</h1>
            <p class="mt-2 text-sm leading-6 text-stone-600">Complete the details below. New listings and edits are reviewed before they appear in the public directory.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-bold">Please check the highlighted fields.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($editingBusiness)
            <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-900">
                Your current listing remains public while this change is reviewed.
            </div>
        @elseif ($editingSubmission && $submission->admin_reason)
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-6 text-rose-900">
                <strong>Reviewer feedback:</strong> {{ $submission->admin_reason }}
            </div>
        @else
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-950">
                Your listing is reviewed by an administrator before it goes live.
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            @if ($editingSubmission || $editingBusiness)
                @method('PUT')
            @endif

            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                <h2 class="text-lg font-extrabold">Basic information</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-stone-800">Business name <span class="text-rose-600">*</span></label>
                        <input id="name" name="name" value="{{ old('name', data_get($values, 'name')) }}" required maxlength="200" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-stone-800">Category <span class="text-rose-600">*</span></label>
                        <select id="category_id" name="category_id" required class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', data_get($values, 'category_id')) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="location_id" class="block text-sm font-semibold text-stone-800">Town <span class="text-rose-600">*</span></label>
                        <select id="location_id" name="location_id" required class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                            <option value="">Select town</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) old('location_id', data_get($values, 'location_id')) === (string) $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-stone-800">Street address <span class="text-rose-600">*</span></label>
                        <input id="address" name="address" value="{{ old('address', data_get($values, 'address')) }}" required maxlength="500" autocomplete="street-address" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('address')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-stone-800">Description <span class="text-rose-600">*</span></label>
                        <textarea id="description" name="description" rows="5" required minlength="50" maxlength="5000" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">{{ old('description', data_get($values, 'description')) }}</textarea>
                        <p class="mt-1 text-xs text-stone-500">Describe your business, what you offer, and who you serve. At least 50 characters.</p>
                        @error('description')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="services" class="block text-sm font-semibold text-stone-800">Products and services</label>
                        <textarea id="services" name="services" rows="3" maxlength="2000" placeholder="List the main products or services you offer" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">{{ old('services', data_get($values, 'services')) }}</textarea>
                        @error('services')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                <h2 class="text-lg font-extrabold">Contact details</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-stone-800">Phone <span class="text-rose-600">*</span></label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', data_get($values, 'phone')) }}" required maxlength="50" autocomplete="tel" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('phone')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="whatsapp" class="block text-sm font-semibold text-stone-800">WhatsApp number</label>
                        <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp', data_get($values, 'whatsapp')) }}" maxlength="50" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('whatsapp')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-stone-800">Business email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', data_get($values, 'email')) }}" maxlength="255" autocomplete="email" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="website" class="block text-sm font-semibold text-stone-800">Website</label>
                        <input id="website" name="website" type="url" value="{{ old('website', data_get($values, 'website')) }}" maxlength="255" placeholder="https://" class="mt-1.5 w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        @error('website')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-lg font-extrabold">Opening hours</h2>
                    <p class="mt-1 text-sm text-stone-600">Choose whether the business is open each day. Times use your local time.</p>
                </div>
                <div class="mt-5 divide-y divide-stone-100">
                    @foreach ($days as $day => $dayName)
                        <div class="grid gap-3 py-4 first:pt-0 last:pb-0 sm:grid-cols-[7rem_9rem_1fr_1fr] sm:items-center">
                            <span class="text-sm font-semibold text-stone-800">{{ $dayName }}</span>
                            <input type="hidden" name="hours[{{ $day }}][day_of_week]" value="{{ $day }}">
                            <div>
                                <label for="hours-{{ $day }}-closed" class="sr-only">{{ $dayName }} opening status</label>
                                <select id="hours-{{ $day }}-closed" name="hours[{{ $day }}][is_closed]" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                    <option value="0" @selected(! (bool) old("hours.$day.is_closed", data_get($values, "hours.$day.is_closed", 1)))>Open</option>
                                    <option value="1" @selected((bool) old("hours.$day.is_closed", data_get($values, "hours.$day.is_closed", 1)))>Closed</option>
                                </select>
                            </div>
                            <div>
                                <label for="hours-{{ $day }}-open" class="sr-only">{{ $dayName }} opens at</label>
                                <input id="hours-{{ $day }}-open" name="hours[{{ $day }}][open_time]" type="time" value="{{ old("hours.$day.open_time", data_get($values, "hours.$day.open_time")) }}" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                @error("hours.$day.open_time")<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="hours-{{ $day }}-close" class="sr-only">{{ $dayName }} closes at</label>
                                <input id="hours-{{ $day }}-close" name="hours[{{ $day }}][close_time]" type="time" value="{{ old("hours.$day.close_time", data_get($values, "hours.$day.close_time")) }}" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                @error("hours.$day.close_time")<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('hours')<p class="mt-3 text-sm text-rose-700">{{ $message }}</p>@enderror
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                <h2 class="text-lg font-extrabold">Business images</h2>
                <p class="mt-1 text-sm leading-6 text-stone-600">Optional. JPG, PNG, or WebP images are stored privately until your listing is approved.</p>
                @if (is_array(data_get($values, 'images')) && count(data_get($values, 'images')) > 0)
                    <p class="mt-3 text-xs text-stone-600">
                        Current images:
                        {{ collect(data_get($values, 'images'))->countBy('type')->map(fn ($count, $type) => $count.' '.$type)->implode(', ') }}.
                        A new logo or cover replaces the previous one; gallery images are added.
                    </p>
                @endif
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="logo" class="block text-sm font-semibold text-stone-800">Logo <span class="font-normal text-stone-500">(max 1 MB)</span></label>
                        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
                        @error('logo')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="cover" class="block text-sm font-semibold text-stone-800">Cover photo <span class="font-normal text-stone-500">(max 2 MB)</span></label>
                        <input id="cover" name="cover" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
                        @error('cover')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="gallery" class="block text-sm font-semibold text-stone-800">Gallery <span class="font-normal text-stone-500">(up to 6 images, max 2 MB each)</span></label>
                        <input id="gallery" name="gallery[]" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple class="mt-1.5 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
                        @error('gallery')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        @foreach ($errors->get('gallery.*') as $messages)
                            @foreach ($messages as $message)
                                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap items-center justify-end gap-3 pb-4">
                <a href="{{ route('owner.dashboard') }}" class="rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-emerald-800 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-900">
                    {{ $editingSubmission ? 'Save and resubmit' : ($editingBusiness ? 'Submit changes for review' : 'Submit for review') }}
                </button>
            </div>
        </form>
    </div>
@endsection
