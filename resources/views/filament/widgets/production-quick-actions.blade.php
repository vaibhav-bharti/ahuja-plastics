<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Quick Actions</x-slot>

        <x-slot name="description">
            {{ $isAdmin ? 'Production, job, return, and inventory shortcuts.' : 'Create production entries or view production jobs and returns.' }}
        </x-slot>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($actions as $action)
                <a href="{{ $action['url'] }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 text-sm font-semibold text-gray-950 shadow-sm transition hover:border-primary-400 hover:bg-primary-50 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:border-primary-500 dark:hover:bg-primary-500/10">
                    <x-filament::icon :icon="$action['icon']" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
