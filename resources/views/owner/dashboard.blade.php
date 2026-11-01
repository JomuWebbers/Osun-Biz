@extends('owner.layout')

@section('title', 'My owner dashboard | OsunBiz')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Owner portal</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight">My dashboard</h1>
            <p class="mt-2 text-sm text-stone-600">Manage your business listings and review their submission status.</p>
        </div>
        <a href="{{ route('owner.businesses.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-900">
            <span aria-hidden="true">+</span> Add a business
        </a>
    </div>

    <section class="mt-7 grid gap-4 sm:grid-cols-3" aria-label="Listing totals">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-semibold text-stone-500">Live listings</p>
            <p class="mt-2 text-3xl font-black tracking-tight">{{ $businesses->count() }}</p>
            <p class="mt-1 text-xs text-stone-500">Approved and visible in the directory</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-semibold text-stone-500">Awaiting review</p>
            <p class="mt-2 text-3xl font-black tracking-tight">{{ $pendingCount }}</p>
            <p class="mt-1 text-xs text-stone-500">Submissions not yet reviewed</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-semibold text-stone-500">Needs attention</p>
            <p class="mt-2 text-3xl font-black tracking-tight">{{ $rejectedCount }}</p>
            <p class="mt-1 text-xs text-stone-500">Rejected submissions you can update</p>
        </div>
    </section>

    <section class="mt-8">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight">My live listings</h2>
                <p class="mt-1 text-sm text-stone-600">Changes to an approved listing are reviewed before they appear publicly.</p>
            </div>
        </div>

        @if ($businesses->isNotEmpty())
            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                <ul class="divide-y divide-stone-100">
                    @foreach ($businesses as $business)
                        <li class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 font-extrabold text-emerald-900" aria-hidden="true">{{ mb_strtoupper(mb_substr($business->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-bold">{{ $business->name }}</h3>
                                    <p class="mt-1 text-xs text-stone-500">{{ $business->category->name }} · {{ $business->location->name }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">Live</span>
                                <a href="{{ route('directory.show', $business->slug) }}" class="rounded-lg border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">View</a>
                                <a href="{{ route('owner.businesses.edit', $business) }}" class="rounded-lg border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">Request an edit</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-8">
                <h3 class="font-bold">No live listings yet</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Once a submission is approved, it will appear here and in the public directory.</p>
            </div>
        @endif
    </section>

    <section class="mt-8">
        <div class="mb-4">
            <h2 class="text-xl font-extrabold tracking-tight">Submissions</h2>
            <p class="mt-1 text-sm text-stone-600">Listings and listing changes awaiting review, or requiring updates.</p>
        </div>

        @if ($submissions->isNotEmpty())
            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                <ul class="divide-y divide-stone-100">
                    @foreach ($submissions as $submission)
                        @php($data = $submission->business_data)
                        <li class="p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold">{{ $data['name'] ?? 'Business submission' }}</h3>
                                        @if (data_get($data, 'business_id'))
                                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-800">Listing update</span>
                                        @endif
                                        @if ($submission->status === 'pending')
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-800">Pending review</span>
                                        @elseif ($submission->status === 'rejected')
                                            <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-800">Needs changes</span>
                                        @else
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800">{{ ucfirst($submission->status) }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-stone-500">{{ $data['category_name'] ?? 'Category' }} · {{ $data['location_name'] ?? 'Town' }} · Submitted {{ $submission->created_at->format('j M Y') }}</p>
                                    @if ($submission->admin_reason)
                                        <div class="mt-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                                            <strong>Reviewer feedback:</strong> {{ $submission->admin_reason }}
                                        </div>
                                    @endif
                                </div>
                                @if (in_array($submission->status, ['pending', 'rejected'], true))
                                    <a href="{{ route('owner.submissions.edit', $submission) }}" class="shrink-0 rounded-lg border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                                        {{ $submission->status === 'rejected' ? 'Edit and resubmit' : 'Edit submission' }}
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-8">
                <h3 class="font-bold">No submissions yet</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Add a business and submit it for review. New listings are not public until approved.</p>
                <a href="{{ route('owner.businesses.create') }}" class="mt-4 inline-flex rounded-lg bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-900">Add your first business</a>
            </div>
        @endif
    </section>
@endsection
