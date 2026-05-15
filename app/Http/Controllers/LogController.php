<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Services\LogService;
use Illuminate\View\View;

class LogController extends Controller
{
    public function __invoke(LogRequest $request, LogService $logService): View
    {
        $countRequests = $logService->getCountRequestsByDate($request);
        $chartData = $logService->getChartData($countRequests);

        return view('dashboard', ['countRequests' => $countRequests,
            'labels' => data_get($chartData, 'labels'),
            'values' => data_get($chartData, 'values')
        ]);
    }
}
