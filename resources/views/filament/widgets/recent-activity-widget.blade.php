<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('general.recent_activity') }}
        </x-slot>
        <div class="space-y-4">
            @forelse ($activities as $activity)
                <div class="relative flex items-start {{ app()->getLocale() === 'ar' ? 'flex-row-reverse gap-x-4' : 'gap-x-4' }}">
                <!-- Timeline Icon -->
                <div class="relative flex-shrink-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500/20 ring-4 ring-white/5">
                        @if($activity['type'] === 'lesson')
                            <x-filament::icon 
                                icon="heroicon-o-check-circle" 
                                class="h-5 w-5 text-primary-400"
                            />
                        @elseif($activity['type'] === 'badge')
                            <x-filament::icon 
                                icon="heroicon-o-trophy" 
                                class="h-5 w-5 text-warning-400"
                            />
                        @else
                            <x-filament::icon 
                                icon="heroicon-o-sparkles" 
                                class="h-5 w-5 text-info-400"
                            />
                        @endif
                    </div>
                    @unless($loop->last)
                        <div class="absolute top-10 {{ app()->getLocale() === 'ar' ? 'right-5 translate-x-1/2' : 'left-5 -translate-x-1/2' }} h-full w-0.5 bg-gray-700/50"></div>
                    @endunless
                </div>
                
                <!-- Activity Content -->
                <div class="min-w-0 flex-1">
                    <div class="rounded-lg bg-white/5 p-4 backdrop-blur-sm border border-gray-700/30">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-white" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                    {{ $activity['title'] }}
                                </h4>
                                @if(isset($activity['description']))
                                    <p class="mt-1 text-xs text-gray-400" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                        {{ $activity['description'] }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex-shrink-0 {{ app()->getLocale() === 'ar' ? 'text-left' : 'text-right' }}">
                                <time class="text-xs text-gray-500" dir="ltr">
                                    {{ $activity['timestamp'] }}
                                </time>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center py-12">
                    <x-filament::icon icon="heroicon-o-clock" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-300" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        {{ __('general.no_activity_found') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        {{ __('general.no_activity_description') }}
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
