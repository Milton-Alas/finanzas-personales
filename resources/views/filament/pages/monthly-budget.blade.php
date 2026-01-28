<x-filament-panels::page>
    <form wire:submit.prevent="$refresh">
        {{ $this->form }}
    </form>

    @php $budget = $this->getBudgetData(); @endphp

    {{-- Resumen Total --}}
    <x-filament::section>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-500 uppercase tracking-wider">Presupuesto Total</p>
                <p class="text-3xl font-bold">${{ number_format($budget['totals']['budget'], 2) }}</p>
            </div>
            <div class="w-full md:w-1/3">
                <div class="flex justify-between text-xs mb-1 font-bold">
                    <span>Consumido: ${{ number_format($budget['totals']['spent'], 2) }}</span>
                    <span>{{ number_format($budget['totals']['percentage'], 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                    <div class="h-4 rounded-full {{ $budget['totals']['percentage'] >= 100 ? 'bg-danger-500' : 'bg-primary-500' }}" 
                         style="width: {{ min($budget['totals']['percentage'], 100) }}%"></div>
                </div>
            </div>
            <div class="text-center md:text-right">
                <p class="text-sm text-gray-500 uppercase tracking-wider">Disponible</p>
                <p class="text-3xl font-bold text-success-600">${{ number_format($budget['totals']['remaining'], 2) }}</p>
            </div>
        </div>
    </x-filament::section>

    {{-- Detalle por Categoría --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($budget['categories'] as $cat)
            <x-filament::section>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold" style="color: {{ $cat['color'] }}">{{ $cat['name'] }}</h3>
                        <p class="text-xs text-gray-500 italic">Límite: ${{ number_format($cat['budget'], 2) }}</p>
                    </div>
                    <x-filament::badge :color="$cat['status'] === 'over' ? 'danger' : ($cat['status'] === 'warning' ? 'warning' : 'success')">
                        {{ $cat['status'] === 'over' ? 'Excedido' : ($cat['status'] === 'warning' ? 'Crítico' : 'Saludable') }}
                    </x-filament::badge>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Gastado: <strong>${{ number_format($cat['spent'], 2) }}</strong></span>
                        <span>{{ number_format($cat['percentage'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500" 
                             style="width: {{ min($cat['percentage'], 100) }}%; background-color: {{ $cat['color'] }}"></div>
                    </div>
                    <p class="text-right text-xs {{ $cat['remaining'] > 0 ? 'text-gray-400' : 'text-danger-500 font-bold' }}">
                        {{ $cat['remaining'] > 0 ? 'Restan: $' . number_format($cat['remaining'], 2) : 'Presupuesto agotado' }}
                    </p>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>