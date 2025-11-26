@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Message Details</h1>
        <a href="{{ route('admin.contact-messages.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            Back to Messages
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $contactMessage->name }}</h2>
                    <p class="text-gray-600">{{ $contactMessage->email }}</p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">{{ $contactMessage->created_at->format('M d, Y h:i A') }}</span>
                    <div class="mt-1">
                        @if($contactMessage->status === 'unread')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Unread
                            </span>
                        @elseif($contactMessage->status === 'read')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Read
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Replied on {{ $contactMessage->replied_at->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Message:</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $contactMessage->message }}</p>
            </div>
        </div>

        @if($contactMessage->status !== 'replied')
        <div class="px-6 py-4 bg-yellow-50 border-t">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Mark as Replied</h3>
            <form action="{{ route('admin.contact-messages.replied', $contactMessage) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Admin Notes (Required)
                    </label>
                    <textarea id="admin_notes" name="admin_notes" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter notes about how you handled this message..."></textarea>
                </div>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Mark as Replied
                </button>
            </form>
        </div>
        @else
        <div class="px-6 py-4 bg-green-50 border-t">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Admin Response:</h3>
            <div class="bg-white p-4 rounded-lg border">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $contactMessage->admin_notes }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    Replied on: {{ $contactMessage->replied_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
