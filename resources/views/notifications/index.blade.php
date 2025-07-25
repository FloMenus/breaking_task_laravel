<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
            {{ __('Notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @forelse ($notifications as $notification)
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-3 flex justify-between items-start">
                            <!-- Contenu principal -->
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                <h3 class="font-semibold text-gray-800 dark:text-white">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                <p class="text-gray-600 whitespace-pre-line">
                                    {{ $notification->data['description'] ?? '' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-2">
                                    @if(!empty($notification->data['deadline']))
                                        Date d'échéance : {{ $notification->data['deadline'] }}
                                    @else
                                        Aucune date d'échéance
                                    @endif
                                </p>
                            </div>

                            <!-- Action à droite -->
                            <div class="ml-4 text-right">
                                @if (!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                            Marquer comme lue
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Lu {{ $notification->read_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <span>Aucune notification pour le moment</span>
                    @endforelse
                    
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>