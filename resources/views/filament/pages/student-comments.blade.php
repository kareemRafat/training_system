<x-filament::page>

    {{-- Student card --}}
    <h3 class="text-lg font-bold dark:text-gray-100">اسم الطالب</h3>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 mb-6">
        <div class="flex items-center space-x-4 rtl:space-x-reverse">
            <div>
                <h2 class="text-lg font-medium text-lime-400 dark:text-lime-200 !important">{{ $record->name }}</h2>
            </div>
        </div>
    </div>

    {{-- Comments section --}}
    <div class="space-y-4">
        <h3 class="text-xl font-bold mb-4 dark:text-gray-100">التعليقات ({{ $record->comments->count() }})</h3>

        @forelse($record->comments as $comment)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex gap-4">
                {{-- Auto-incremented number --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <span class="text-gray-950 dark:text-white font-medium">{{ $loop->iteration }}</span>
                </div>

                <div class="flex-1">
                    <div class="prose max-w-none font-medium text-lg dark:text-gray-200">
                        {!! nl2br(e($comment->comment)) !!}
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-4"></div>
                    <p class="font-medium text-base" style="color: rgb(79, 209, 197);">{{ $comment->user->name }}</p>
                    <div class="flex justify-between items-start">
                        <div class="flex items-center text-sm gap-2 text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-calendar class="w-4 h-4 mx-2 rtl:ml-1 dark:text-gray-300" />
                            <span class="text-base text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at?->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex items-center text-base gap-2 text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-clock class="w-4 h-4 mx-2 rtl:ml-2 rtl:mr-2 dark:text-gray-300" />
                            <span>{{ $comment->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-chat-bubble-left-ellipsis class="mx-auto h-12 w-12 dark:text-gray-300"/>
                <p class="mt-2">لا توجد تعليقات بعد</p>
            </div>
        @endforelse
    </div>

</x-filament::page>
