<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-medium">Failment Posts Analytics</h2>
    <p class="mt-1 text-sm text-gray-500">Track your post publishing failures and identify common issues.</p>

    <div class="mt-6">
        <h3 class="text-base font-medium text-gray-900">Platform Failure Rates</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Platform</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Posts</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Failed Posts</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Failure Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($platformFailures as $platform)
                        <tr>
                            <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $platform['name'] }}</td>
                            <td class="px-3 py-4 text-sm text-gray-500">{{ $platform['type'] }}</td>
                            <td class="px-3 py-4 text-sm text-gray-500">{{ $platform['total_posts'] }}</td>
                            <td class="px-3 py-4 text-sm text-gray-500">{{ $platform['failed_posts'] }}</td>
                            <td class="px-3 py-4 text-sm">
                                <div class="flex items-center">
                                    <div class="h-2 w-24 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                        <div class="h-full bg-red-500 rounded-full" style="width: {{ min($platform['failure_rate'], 100) }}%;"></div>
                                    </div>
                                    <span class="{{ $platform['failure_rate'] > 50 ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                        {{ $platform['failure_rate'] }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-sm text-gray-500">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-base font-medium text-gray-900">Common Failure Reasons</h3>
        <div class="mt-3 overflow-x-auto">
            @if($commonFailures->count() > 0)
                <ul class="mt-4 space-y-3">
                    @foreach($commonFailures as $failure)
                        <li>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">{{ $failure->failure_reason }} <span class="font-medium text-gray-900">({{ $failure->count }} occurrences)</span></p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 mt-2">No failure data available</p>
            @endif
        </div>
    </div>
</div> 