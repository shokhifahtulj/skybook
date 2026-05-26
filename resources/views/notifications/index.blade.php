<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-[28px] font-bold text-slate-900">Notifications</h2>
            <p class="mt-1 text-sm text-slate-500">Track updates about your trips, bookings, and operational changes.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Notifications" description="Your latest booking and travel updates.">
            @php
                $notificationCollection = method_exists($notifications, 'getCollection') ? $notifications->getCollection() : $notifications;
                $unreadCount = $notificationCollection->where('is_read', false)->count();
            @endphp

            <div class="mb-4">
                <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-sm font-semibold text-sky-700">{{ $unreadCount }} unread</span>
            </div>

            @if($notificationCollection->isEmpty())
                <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                    <p class="text-lg font-semibold text-slate-700">No notifications yet</p>
                    <p class="mt-2 text-sm text-slate-500">Your booking updates will appear here once your trip is confirmed.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="rounded-[24px] border border-slate-200 {{ $notification->is_read ? 'bg-slate-50' : 'bg-white' }} p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-base font-semibold text-slate-900">{{ $notification->title }}</p>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600">{{ $notification->type }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-500">{{ $notification->message }}</p>
                                    <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                @if(! $notification->is_read)
                                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                        @csrf
                                        <button class="rounded-[16px] bg-skybook-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-skybook-secondary/90">Mark as read</button>
                                    </form>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Read</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(method_exists($notifications, 'links'))
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </x-admin.card>
    </div>
</x-app-layout>
