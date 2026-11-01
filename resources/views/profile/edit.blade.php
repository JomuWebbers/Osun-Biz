@extends('owner.layout')

@section('title', 'Profile and password | OsunBiz')

@section('content')
    <div class="max-w-3xl">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Owner portal</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Profile and password</h1>
        <p class="mt-2 text-sm text-stone-600">Update the name and email address associated with your owner account.</p>

        <div class="mt-6 space-y-5">
            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                @include('profile.partials.update-password-form')
            </section>

            <section class="rounded-2xl border border-rose-200 bg-white p-5 sm:p-7">
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
@endsection
