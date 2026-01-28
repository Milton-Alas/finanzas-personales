<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🎯 Progreso de Metas de Ahorro
        </x-slot>

        <x-slot name="description">
            Seguimiento de tus objetivos financieros activos
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($goals as $goal)
                <div class="p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg" style="background-color: {{ ($goal->color ?? '#3B82F6') }}20">
                                <x-filament::icon 
                                    :icon="$goal->icon ?? 'heroicon-m-sparkles'" 
                                    class="h-6 w-6"
                                    :style="'color: ' . ($goal->color ?? '#3B82F6')"
                                />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-950 dark:text-white">
                                    {{ $goal->name }}
                                </h4>
                                <p class="text-xs text-gray-500">Meta: ${{ number_format($goal->target_amount, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="text-xl font-black" style="color: {{ $goal->color ?? '#3B82F6' }}">
                                {{ number_format($goal->getProgressPercentage(), 0) }}%
                            </span>
                        </div>
                    </div>

                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-4 shadow-inner overflow-hidden">
                        <div 
                            class="h-full rounded-full transition-all duration-1000 ease-out shadow-sm"
                            style="width: {{ min($goal->getProgressPercentage(), 100) }}%; background-color: {{ $goal->color ?? '#3B82F6' }}"
                        ></div>
                    </div>

                    <div class="flex justify-between items-end">
                        <div class="text-xs">
                            <span class="text-gray-500 dark:text-gray-400 block mb-1 uppercase tracking-wider font-semibold">Ahorrado</span>
                            <span class="text-sm font-bold dark:text-white">${{ number_format($goal->current_amount, 2) }}</span>
                        </div>

                        <div class="text-right text-xs">
                            @php $days = $goal->getDaysRemaining(); @endphp
                            @if($days !== null)
                                <span @class([
                                    'px-2 py-1 rounded-md font-bold uppercase tracking-tighter',
                                    'bg-danger-500/10 text-danger-600' => $days < 0,
                                    'bg-warning-500/10 text-warning-600' => $days >= 0 && $days <= 7,
                                    'bg-success-500/10 text-success-600' => $days > 7,
                                ])>
                                    @if($days < 0)
                                        Vencida ({{ abs($days) }}d)
                                    @elseif($days == 0)
                                        ¡Vence hoy!
                                    @else
                                        {{ $days }} días restantes
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10">
                    <x-filament::icon icon="heroicon-o-face-frown" class="h-10 w-10 mx-auto text-gray-400 mb-3" />
                    <p class="text-gray-500 dark:text-gray-400 italic">No tienes metas de ahorro activas actualmente.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>