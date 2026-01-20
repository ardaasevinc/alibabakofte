<x-filament-panels::page>
    @php
        $utm      = $this->getUtmStats();
        $devices  = $this->getDeviceStats();
        $browsers = $this->getBrowserStats();
        $trend    = $this->getDailyLeadTrend();
    @endphp

    {{-- Trend & UTM Grafik Alanı --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        {{-- Lead Trend (7 Gün) --}}
        <x-filament::card>
            <x-slot name="heading">
                Son 7 Gün Lead Trend
            </x-slot>

            <canvas id="leadTrendChart" class="w-full h-64"></canvas>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    new Chart(document.getElementById("leadTrendChart"), {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($trend->pluck('date')) !!},
                            datasets: [{
                                label: 'Lead',
                                data: {!! json_encode($trend->pluck('total')) !!},
                                borderWidth: 2,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.15)',
                                tension: 0.3
                            }]
                        },
                        options: {
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                });
            </script>
        </x-filament::card>

        {{-- UTM Kaynak Performansı --}}
        <x-filament::card>
            <x-slot name="heading">
                UTM Kaynak Performansı
            </x-slot>

            <canvas id="utmChart" class="w-full h-64"></canvas>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    new Chart(document.getElementById("utmChart"), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($utm->pluck('utm_source')) !!},
                            datasets: [{
                                label: 'Toplam',
                                data: {!! json_encode($utm->pluck('total')) !!},
                                borderWidth: 1,
                                backgroundColor: '#10b981'
                            }]
                        },
                        options: {
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                });
            </script>
        </x-filament::card>

    </div>

    {{-- Cihaz & Tarayıcı Analizi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

        {{-- Cihaz Dağılımı --}}
        <x-filament::card>
            <x-slot name="heading">
                Cihaz Dağılımı
            </x-slot>

            <ul class="text-sm space-y-1">
                @foreach ($devices as $d)
                    <li class="flex justify-between border-b pb-1">
                        <span class="text-gray-700">{{ $d->device_id }}</span>
                        <strong>{{ $d->total }}</strong>
                    </li>
                @endforeach
            </ul>
        </x-filament::card>

        {{-- Tarayıcı Analizi --}}
        <x-filament::card>
            <x-slot name="heading">
                Tarayıcı (Browser ID)
            </x-slot>

            <ul class="text-sm space-y-1">
                @foreach ($browsers as $b)
                    <li class="flex justify-between border-b pb-1">
                        <span>{{ $b->browser_id ?? 'N/A' }}</span>
                        <strong>{{ $b->total }}</strong>
                    </li>
                @endforeach
            </ul>
        </x-filament::card>

    </div>

</x-filament-panels::page>
