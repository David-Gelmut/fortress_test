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

    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <h3 class="text-base font-semibold text-gray-900">Фильтрация данных</h3>
        </div>

        <form action="{{ url()->current() }}" method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Дата От -->
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-600 mb-1.5">Дата от</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm py-2 px-3 border bg-gray-50/50">
            </div>

            <!-- Дата До -->
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-600 mb-1.5">Дата до (макс. 1
                    год)</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm py-2 px-3 border bg-gray-50/50">
            </div>

            <!-- ОС -->
            <div>
                <label for="os" class="block text-xs font-medium text-gray-600 mb-1.5">Операционная система</label>
                <select name="os" id="os"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm py-2 px-3 border bg-white">
                    <option value="">Все операционные системы</option>
                    @foreach($filterOptions['os'] as $os)
                        <option value="{{ $os }}" {{ request('os') == $os ? 'selected' : '' }}>
                            {{ $os }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Архитектура -->
            <div class="">
                <label for="arch" class="block text-xs font-medium text-gray-600 mb-1.5">Архитектура</label>
                <select name="arch" id="arch"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm py-2 px-3 border bg-white">
                    <option value="">Все</option>
                    @foreach($filterOptions['architectures'] as $arch)
                        <option value="{{ $arch }}" {{ request('arch') == $arch ? 'selected' : '' }}>
                            {{ $arch }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="">
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex justify-center items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                        Применить
                    </button>
                    <a href="{{ url()->current() }}"
                       class="flex-1 inline-flex justify-center items-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                        Сбросить
                    </a>
                </div>
            </div>

        </form>

        <!-- Ошибки валидации дат -->
        @if($errors->has('date_range'))
            <div
                class="mt-3 text-xs bg-red-50 text-red-600 p-2.5 rounded-lg border border-red-200 font-medium flex items-center gap-1.5">
                <span>⚠️</span> {{ $errors->first('date_range') }}
            </div>
        @endif
    </div>

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
