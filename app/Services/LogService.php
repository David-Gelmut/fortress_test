<?php

namespace App\Services;

use App\Http\Requests\LogRequest;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LogService
{

    public function getCountRequestsByDate(LogRequest $request): Collection
    {
        return Log::query()
            ->select(DB::raw('DATE(date) as log_date, COUNT(*) as log_count'))
            ->groupBy(DB::raw('DATE(date)'))
            ->get();
    }

    public function getChartData(Collection $countRequests): array
    {
        return [
            'labels' => $countRequests->pluck('log_date')->toArray(),
            'values' => $countRequests->pluck('log_count')->toArray(),
        ];
    }

}
