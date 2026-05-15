<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LogService
{
    /**
     * Получаем самые популярные url сгруппированные по дате
     * @return array
     */
    public function getPopularUrls(): array
    {
        return DB::select("SELECT log_date, url, log_count
                                    FROM (
                                             SELECT
                                                 DATE(date) as log_date,
                                                 url,
                                                 COUNT(*) as log_count,
                                                 ROW_NUMBER() OVER(PARTITION BY DATE(date) ORDER BY COUNT(*) DESC) as rn
                                             FROM logs
                                             GROUP BY DATE(date), url
                                         ) as ranked_logs
                                    WHERE rn = 1
                                    ORDER BY log_date;");
    }

    /**
     * Получаем самые популярные браузеры сгруппированные по дате
     * @return array
     */
    public function getPopularBrowsers(): array
    {
        return DB::select("SELECT log_date, browser, log_count
                                    FROM (
                                             SELECT
                                                 DATE(date) as log_date,
                                                 browser,
                                                 COUNT(*) as log_count,
                                                 ROW_NUMBER() OVER(PARTITION BY DATE(date) ORDER BY COUNT(*) DESC) as rn
                                             FROM logs
                                             GROUP BY DATE(date), browser
                                         ) as ranked_logs
                                    WHERE rn = 1
                                    ORDER BY log_date;");
    }

    /**
     * Получаем кол-во посещений сгруппированное по дате
     * @param array $request
     * @return Collection
     */
    public function getCountRequestsByDate(array $request): Collection
    {
        return Log::query()
            ->select(DB::raw('DATE(date) as log_date, COUNT(*) as log_count'))
            ->groupBy(DB::raw('DATE(date)'))
            ->get();
    }

    /**
     * Получаем данные для графиков
     * @param Collection $countRequests кол-во посещений сгруппированное по дате
     * @return array
     */
    public function getChartData(Collection $countRequests): array
    {
        return [
            'labels' => $countRequests->pluck('log_date')->toArray(),
            'values' => $countRequests->pluck('log_count')->toArray(),
        ];
    }

    /**
     * Получаем три самых популярных браузера в виде массива
     * @param int $count кол-во получаемых браузеров
     * @return array
     */
    public function getPopularBrowsersWithCount(int $count): array
    {
        return Log::query()
            ->select(DB::raw('browser'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->limit($count)
            ->pluck('browser')
            ->toArray();
    }

    /**
     * Получаем кол-во запросов для трех самых популярных браузеров и группируем по дате
     * @return array
     */
    public function getCountRequestsFromPopularBrowsers(): array
    {
        return Log::query()
            ->select(DB::raw('DATE(date) as log_date, COUNT(*) as count'))
            ->whereIn('browser', $this->getPopularBrowsersWithCount(3))
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->pluck('count', 'log_date')
            ->toArray();
    }

    /**
     * Получаем процент посещения с популярных браузеров по дате
     * @param Collection $countRequests кол-во посещений сгруппированное по дате
     * @return array
     */
    public function getPercentsPopularBrowsers(Collection $countRequests): array
    {
        $percents = [];
        $countRequestsFromPopularBrowsers = $this->getCountRequestsFromPopularBrowsers();
        foreach ($countRequests->toArray() as $request) {

            if (isset($request['log_date']) && isset($request['log_count'])) {

                $percents[] = round((data_get($countRequestsFromPopularBrowsers, $request['log_date'], 0) / $request['log_count']) * 100, 2);
            }
        }

        return $percents;
    }

    /**
     * Получаем данные для таблицы:
     *
     *   Дата
     *   Число запросов
     *   Самый популярный URL
     *   Самый популярный браузер
     *
     * @param Collection $countRequests кол-во посещений сгруппированное по дате
     * @param array $request Прокидываем данные реквеста для получения параметров сортировки. Сортируем через коллекцию, чтобы сортировать по browser и url.
     * @return array
     */
    public function getDataTable(Collection $countRequests, array $request): array
    {
        $result = [];
        $popularUrls = collect($this->getPopularUrls())->pluck('url', 'log_date')->toArray();
        $popularBrowsers = collect($this->getPopularBrowsers())->pluck('browser', 'log_date')->toArray();

        $sortBy = data_get($request, 'sort_by');
        $sortDir = data_get($request, 'sort_order');

        foreach ($countRequests as $key => $request) {
            if (isset($request['log_date']) && isset($request['log_count'])) {
                $date = $request['log_date'];
                $result[$date]['log_date'] = $date;
                $result[$date]['log_count'] = $request['log_count'];
                $result[$date]['log_popular_url'] = $popularUrls[$date];
                $result[$date]['log_popular_browser'] = $popularBrowsers[$date];
            }
        }
        $sorted = collect($result);

        return $sortDir === 'desc'
            ? $sorted->sortByDesc($sortBy)->values()->all()
            : $sorted->sortBy($sortBy)->values()->all();

    }
}
