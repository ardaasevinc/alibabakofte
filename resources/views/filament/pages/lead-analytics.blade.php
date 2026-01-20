<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $utm = $this->getUtmStats();
        $devices = $this->getDeviceStats();
        $browsers = $this->getBrowserStats();
        $trend = $this->getDailyLeadTrend();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <x-filament::stats.card
            label="Toplam Lead"
            value="{{ $stats['total'] }}"
            icon="heroicon-o-user-group"
            color="primary"
        />

        <x-filament::stats.card
            label="Bugünkü Lead"
            value="{{ $stats['today'] }}"
            icon="heroicon-o-bolt"
            color="success"
        />

        <x-filament::stats.card
            label="Eşsiz Ziyaret (PageView)"
            value="{{ $stats['unique'] }}"
            icon="heroicon-o-eye"
            color="warning"
        />

        <x-filament::stats.card
            label="Dönüşüm Oranı"
            value="{{ $stats['rate'] }}%"
            icon="heroicon-o-chart-bar"
            color="info"
        />
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

        <x-filament::section>
            <x-slot name="heading">Son 7 Gün Lead Trend</x-slot>

            <canvas id="leadTrendChart"></canvas>

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
                                tension: 0.3,
                            }]
                        }
                    });
                });
            </script>
        </x-filament::section>


        <x-filament::section>
            <x-slot name="heading">UTM Kaynak Performansı</x-slot>

            <canvas id="utmChart"></canvas>

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
                            }]
                        }
                    });
                });
            </script>
        </x-filament::section>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

        <x-filament::section>
            <x-slot name="heading">Cihaz Dağılımı</x-slot>

            <ul>
                @foreach ($devices as $d)
                    <li>{{ $d->device_id }} — <strong>{{ $d->total }}</strong></li>
                @endforeach
            </ul>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Tarayıcı (Browser ID)</x-slot>

            <ul>
                @foreach ($browsers as $b)
                    <li>{{ $b->browser_id ?? 'N/A' }} — <strong>{{ $b->total }}</strong></li>
                @endforeach
            </ul>
        </x-filament::section>

    </div>

</x-filament-panels::page>
