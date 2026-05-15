<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика запросов</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex flex-col w-1/2 gap-4 m-auto mb-10">
    <canvas id="countRequests"></canvas>
    <canvas id="percentagePopularBrowsers"></canvas>

    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm text-gray-500">

                <thead
                    class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">
                <tr>
                    <!-- Сортировка по Дате -->
                    <th scope="col" class="px-6 py-4">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'log_date', 'sort_order' => request('sort_by') === 'log_date' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}"
                           class="group inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                            Дата лога
                            <span class="ml-1 flex-none rounded text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-900 transition-colors p-0.5">
                                @if(request('sort_by') === 'log_date' && request('sort_order') === 'asc')
                                    <span class="text-xs font-bold">▼</span>
                                @elseif(request('sort_by') === 'log_date' && request('sort_order') === 'desc')
                                    <span class="text-xs font-bold">▲</span>
                                @else
                                    <span class="text-xs font-bold">▲▼</span>
                                @endif
                                </span>
                        </a>
                    </th>

                    <!-- Сортировка по Просмотрам -->
                    <th scope="col" class="px-6 py-4">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'log_count', 'sort_order' => request('sort_by') === 'log_count' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}"
                           class="group inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                            Просмотры
                            <span class="ml-1 flex-none rounded text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-900 transition-colors p-0.5">
                                @if(request('sort_by') === 'log_count' && request('sort_order') === 'asc')
                                    <span class="text-xs font-bold">▼</span>
                                @elseif(request('sort_by') === 'log_count' && request('sort_order') === 'desc')
                                    <span class="text-xs font-bold">▲</span>
                                @else
                                    <span class="text-xs font-bold">▲▼</span>
                                @endif
                            </span>
                        </a>
                    </th>

                    <!-- Сортировка по Популярному URL -->
                    <th scope="col" class="px-6 py-4">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'log_popular_url', 'sort_order' => request('sort_by') === 'log_popular_url' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}"
                           class="group inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                            Популярный URL
                            <span class="ml-1 flex-none rounded text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-900 transition-colors p-0.5">
                                @if(request('sort_by') === 'log_popular_url' && request('sort_order') === 'asc')
                                    <span class="text-xs font-bold">▼</span>
                                @elseif(request('sort_by') === 'log_popular_url' && request('sort_order') === 'desc')
                                    <span class="text-xs font-bold">▲</span>
                                @else
                                    <span class="text-xs font-bold">▲▼</span>
                                @endif
                                </span>
                        </a>
                    </th>

                    <!-- Сортировка по Популярному Браузеру -->
                    <th scope="col" class="px-6 py-4">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'log_popular_browser', 'sort_order' => request('sort_by') === 'log_popular_browser' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}"
                           class="group inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                            Популярный Браузер
                            <span class="ml-1 flex-none rounded text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-900 transition-colors p-0.5">
                                @if(request('sort_by') === 'log_popular_browser' && request('sort_order') === 'asc')
                                    <span class="text-xs font-bold">▼</span>
                                @elseif(request('sort_by') === 'log_popular_browser' && request('sort_order') === 'desc')
                                    <span class="text-xs font-bold">▲</span>
                                @else
                                    <span class="text-xs font-bold">▲▼</span>
                                @endif
                            </span>
                        </a>
                    </th>
                </tr>
                </thead>

                <!-- Тело таблицы -->
                <tbody class="divide-y divide-gray-200">
                @forelse($getDataTable as $row)
                    {{-- @dd($row) --}}
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <!-- Дата лога -->
                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">
                            {{ $row['log_date'] }}
                        </td>

                        <!-- Кол-во просмотров -->
                        <td class="whitespace-nowrap px-6 py-4 font-bold text-gray-800">
                            <span class="bg-gray-100/80 px-2 py-1 rounded-md text-xs font-mono">
                                {{ number_format($row['log_count']) }}
                            </span>
                        </td>

                        <!-- Популярный URL -->
                        <td class="max-w-[350px] truncate px-6 py-4" title="{{ $row['log_popular_url'] }}">
                            <code
                                class="rounded bg-pink-50/60 px-2 py-1 text-xs font-mono text-pink-600 border border-pink-100/50">
                                {{ $row['log_popular_url'] ?? '—' }}
                            </code>
                        </td>

                        <!-- Популярный Браузер -->
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 border border-blue-100/60">
                                {{ $row['log_popular_browser'] ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <!-- Если данных нет -->
                    <tr>
                        <td colspan="4"
                            class="px-6 py-12 text-center text-sm font-medium text-gray-400 bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span>Данные в таблице отсутствуют по выбранным критериям</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('countRequests');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @js($labels),
            datasets: [{
                label: 'Количество запросов',
                data: @js($values),
                borderWidth: 1,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx1 = document.getElementById('percentagePopularBrowsers');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @js($labels),
            datasets: [{
                label: 'Процентное соотношение (% от числа запросов) для трех самых популярных браузеров',
                data: @js($percents),
                borderWidth: 1,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>
</body>
</html>
