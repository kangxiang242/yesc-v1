<x-filament-widgets::widget>
    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Environment</h3>
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500 w-36">URL</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <a href="{{ $appUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">{{ $appUrl }}</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">PHP version</td>
                        <td class="px-4 py-3 text-sm text-gray-900">PHP/{{ $phpVersion }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Laravel version</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $laravelVersion }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Cache driver</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cacheDriver }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Session driver</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $sessionDriver }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Queue driver</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $queueDriver }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Timezone</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $timezone }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Locale</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $locale }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-500">Env</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $env }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>
