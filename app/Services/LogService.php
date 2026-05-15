<?php

namespace App\Services;

use App\Http\Requests\LogRequest;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LogService
{
    /**
     * Получаем кол-во посещений сгруппированное по дате
     * @param LogRequest $request
     * @return Collection
     */
    public function getCountRequestsByDate(LogRequest $request): Collection
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

}
