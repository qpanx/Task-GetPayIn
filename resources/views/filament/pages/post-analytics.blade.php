<x-filament-panels::page>
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Post Publishing Analytics</h1>
        <p class="mt-1 text-sm text-gray-500">Track your post publishing performance and platform distribution metrics.</p>
    </div>
    
    <div class="grid grid-cols-1 gap-y-8">
        @livewire(\App\Filament\Widgets\FailmentAnalyticsWidget::class)
        
        <div class="bg-white rounded-xl shadow">
            <div class="p-6">
                <h2 class="text-lg font-medium">Platform Distribution & Success Rates</h2>
                <p class="mt-1 text-sm text-gray-500">View how your posts are distributed across platforms and their publishing success rates.</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                
            </div>
        </div>
    </div>
</x-filament-panels::page>  