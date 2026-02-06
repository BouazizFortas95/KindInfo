<div class="flex items-center space-x-1 rtl:space-x-reverse text-sm">
    <span class="font-medium text-gray-900 dark:text-gray-100">
        {{ $user }}
    </span>
    <span class="text-gray-500 dark:text-gray-400 px-1">
        {{ $action }}
    </span>
    <span
        class="font-bold text-gray-900 dark:text-white {{ $type === 'badge' ? 'text-yellow-600 dark:text-yellow-500' : '' }}">
        {{ $subject }}
    </span>
</div>
