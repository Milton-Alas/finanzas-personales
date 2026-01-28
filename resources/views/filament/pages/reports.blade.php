<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Filtros del Reporte
            </x-slot>

            <form wire:submit.prevent="$refresh">
                {{ $this->form }}
            </form>
        </x-filament::section>

        @php
            $data = $this->getReportData();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-success-50 dark:bg-success-950/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-success-600 dark:text-success-400 font-medium">Ingresos</p>
                        <p class="text-2xl font-bold text-success-700 dark:text-success-300 mt-1">
                            ${{ number_format($data['totals']['income'], 2) }}
                        </p>
                    </div>
                    <x-filament::icon icon="heroicon-o-arrow-trending-up" class="h-10 w-10 text-success-500" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-danger-50 dark:bg-danger-950/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-danger-600 dark:text-danger-400 font-medium">Gastos</p>
                        <p class="text-2xl font-bold text-danger-700 dark:text-danger-300 mt-1">
                            ${{ number_format($data['totals']['expenses'], 2) }}
                        </p>
                    </div>
                    <x-filament::icon icon="heroicon-o-arrow-trending-down" class="h-10 w-10 text-danger-500" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-info-50 dark:bg-info-950/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-info-600 dark:text-info-400 font-medium">Ahorros</p>
                        <p class="text-2xl font-bold text-info-700 dark:text-info-300 mt-1">
                            ${{ number_format($data['totals']['savings'], 2) }}
                        </p>
                    </div>
                    <x-filament::icon icon="heroicon-o-banknotes" class="h-10 w-10 text-info-500" />
                </div>
            </div>

            <div @class([
                'rounded-lg border border-gray-200 dark:border-gray-700 p-4',
                'bg-success-50 dark:bg-success-950/30' => $data['totals']['balance'] >= 0,
                'bg-warning-50 dark:bg-warning-950/30' => $data['totals']['balance'] < 0,
            ])>
                <div class="flex items-center justify-between">
                    <div>
                        <p @class([
                            'text-sm font-medium',
                            'text-success-600 dark:text-success-400' => $data['totals']['balance'] >= 0,
                            'text-warning-600 dark:text-warning-400' => $data['totals']['balance'] < 0,
                        ])>Balance</p>
                        <p @class([
                            'text-2xl font-bold mt-1',
                            'text-success-700 dark:text-success-300' => $data['totals']['balance'] >= 0,
                            'text-warning-700 dark:text-warning-300' => $data['totals']['balance'] < 0,
                        ])>
                            ${{ number_format($data['totals']['balance'], 2) }}
                        </p>
                    </div>
                    <x-filament::icon icon="heroicon-o-wallet" class="h-10 w-10" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament::section>
                <x-slot name="heading">Gastos por Categoría</x-slot>
                <div class="space-y-4">
                    @forelse($data['expensesByCategory'] as $category)
                        @php
                            $percentage = $data['totals']['expenses'] > 0 
                                ? ($category['total'] / $data['totals']['expenses']) * 100 
                                : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">{{ $category['name'] }}</span>
                                <span class="text-sm font-bold" style="color: {{ $category['color'] }}">
                                    ${{ number_format($category['total'], 2) }} ({{ number_format($percentage, 1) }}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-full rounded-full" style="width: {{ $percentage }}%; background-color: {{ $category['color'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4 italic">No hay gastos registrados</p>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Ingresos por Fuente</x-slot>
                <div class="space-y-3">
                    @forelse($data['incomesBySource'] ?? [] as $source)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-100 dark:border-white/10">
                            <span class="text-sm font-medium">{{ $source['name'] }}</span>
                            <span class="text-sm font-bold text-success-600 dark:text-success-400">
                                ${{ number_format($source['total'], 2) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4 italic">No hay ingresos registrados</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <x-slot name="heading">Gastos más relevantes</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-3 px-2 font-semibold">Fecha</th>
                            <th class="py-3 px-2 font-semibold">Descripción</th>
                            <th class="py-3 px-2 font-semibold">Categoría</th>
                            <th class="py-3 px-2 font-semibold text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-800">
                        @foreach($data['topExpenses'] as $expense)
                            <tr>
                                <td class="py-3 px-2">{{ $expense->date->format('d/m/Y') }}</td>
                                <td class="py-3 px-2 font-medium">{{ $expense->description }}</td>
                                <td class="py-3 px-2">
                                    <x-filament::badge :color="'gray'">
                                        {{ $expense->expenseCategory->name }}
                                    </x-filament::badge>
                                </td>
                                <td class="py-3 px-2 text-right font-bold text-danger-600 dark:text-danger-400">
                                    ${{ number_format($expense->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Botones de Acción -->
        <div class="flex justify-end gap-3">
            <x-filament::button color="gray" tag="a" href="{{ route('filament.admin.pages.reports') }}" wire:click="$refresh">
                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 mr-2" />
                Actualizar
            </x-filament::button>
            
            <x-filament::button color="success">
                <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                Exportar PDF
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>