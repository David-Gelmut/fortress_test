<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика запросов</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex flex-col w-1/2 m-auto">
    <canvas id="countRequests"></canvas>
    <canvas id="percentagePopularBrowsers"></canvas>
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
