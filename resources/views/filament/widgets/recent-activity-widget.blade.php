<x-filament-widgets::widget>
    <x-filament::section class="bg-gray-900/50 border-white/10 ring-1 ring-white/10">
        <x-slot name="heading">
            {{ __('Recent Activity') }}
        </x-slot>

        @if ($this->activities->isNotEmpty())
            <div class="flow-root mt-4">
                <ul role="list" class="-mb-8">
                    @foreach ($this->activities as $loopIndex => $activity)
                        <li>
                            <div class="relative pb-8">
                                {{-- Dashed Timeline Line --}}
                                @unless ($loop->last)
                                    <span
                                        class="absolute top-4 left-5 -ml-px h-full w-0.5 border-l-2 border-dashed border-gray-700/50"
                                        aria-hidden="true"></span>
                                @endunless

                                <div class="relative flex space-x-4 rtl:space-x-reverse">
                                    {{-- Icon --}}
                                    <div @class([
                                        'relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full ring-8 ring-gray-900',
                                        'bg-blue-600' => $activity->type === 'lesson',
                                        'bg-yellow-500' => $activity->type === 'badge',
                                    ])>
                                        @if ($activity->type === 'lesson')
                                            <x-filament::icon icon="heroicon-m-play" class="h-5 w-5 text-white" />
                                        @else
                                            <x-filament::icon icon="heroicon-m-trophy" class="h-5 w-5 text-white" />
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex min-w-0 flex-1 justify-between gap-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-300">
                                                @if ($activity->user)
                                                    @if ($activity->type === 'lesson')
                                                        @if (auth()->id() === $activity->user->id)
                                                            <span
                                                                class="font-medium text-white">{{ __('You') }}</span>
                                                        @else
                                                            <span
                                                                class="font-medium text-white">{{ $activity->user->name }}</span>
                                                        @endif
                                                        {{ __('completed') }}
                                                        <span class="font-bold text-white">
                                                            {{ $activity->subject ? $activity->subject->title : __('Unknown Lesson') }}
                                                        </span>
                                                    @else
                                                        @if (auth()->id() === $activity->user->id)
                                                            <span
                                                                class="font-medium text-white">{{ __('You') }}</span>
                                                        @else
                                                            <span
                                                                class="font-medium text-white">{{ $activity->user->name }}</span>
                                                        @endif
                                                        {{ __('earned') }}
                                                        <span class="font-bold text-yellow-500">
                                                            {{ $activity->subject ? $activity->subject->name : __('Unknown Badge') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    {{ __('Unknown User action') }}
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Date --}}
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($activity->activity_date)->format('M d') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Pagination Links --}}
            @if ($this->activities instanceof \Illuminate\Contracts\Pagination\Paginator && $this->activities->hasPages())
                <div class="pt-6 border-t border-white/10 mt-2">
                    {{ $this->activities->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center space-y-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 ring-1 ring-white/10">
                    <x-heroicon-o-x-mark class="h-6 w-6 text-gray-400" />
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-medium text-white">{{ __('No recent activity') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Check back later for new updates.') }}</p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
