<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DeviceDetector\DeviceDetector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('log:parse')]
#[Description('Command description')]
class ParserLogsCommand extends Command
{
    public function handle()
    {
        DB::disableQueryLog();

        $this->info('Начался парсинг логов');

        $filePath = base_path('modimio.access.log');

        if (!file_exists($filePath)) {
            $this->error("Файл не найден: {$filePath}");
            return;
        }

        $rows = [];
        $chunk = 1000;
        $skipped = 0;
        $deviceDetector = new DeviceDetector();

        foreach ($this->readFile($filePath) as $line) {

            if ($parseLine = $this->parseLine($line, $deviceDetector)) {
                $rows[] = $parseLine;
            } else {
                $skipped++;
            }

            if (count($rows) == $chunk) {
                DB::table('logs')->insert($rows);
                $rows = [];
            };
        }

        if (!empty($rows)) {
            DB::table('logs')->insert($rows);
        }

        $this->newLine();
        $this->info('Парсинг успешно закончен');
        $this->warn("Пропущено строк (не подошли под regex): " . $skipped);
    }


    private function readFile(string $filePath): \Generator
    {
        $resource = fopen($filePath, 'r');

        if (!$resource) {
            throw new \Exception('Не удалось открыть файл');
        }

        try {
            while (($line = fgets($resource)) !== false) {
                yield $line;
            }
        } catch (\Exception $e) {
            $this->error("Ошибка чтения файла: {$e->getMessage()}");
        } finally {
            fclose($resource);
        }
    }

    private function parseLine(string $line, DeviceDetector $detector): array
    {
        //$regex = '/^(?<ip>\S+) \S+ \S+ \[(?<date>.*?)\] "(?<method>\S+)\s+(?<url>\S+)\s+(?<protocol>[^"]+)" (?<status>\d+) (?<bytes>\d+|-)(?: "(?<referer>[^"]*)" "(?<agent>[^"]*)")?$/';
        $regex = '/^(?<ip>\S+) \S+ \S+ \[(?<date>.*?)\] "(?<request>[^"]*)" (?<status>\d+) (?<bytes>\d+|-)(?: "(?<referer>[^"]*)" "(?<agent>[^"]*)")?$/';

        if (!preg_match($regex, $line, $matches)) {
            $this->warn("Не удалось распарсить строку: " . substr($line, 0, 50) . "...");
            return [];
        }

        $requestParts = explode(' ', $matches['request']);

        $url = $requestParts[1] ?? $matches['request'];

        if (isset($matches['agent']) && $matches['agent'] !== '-') {
            try {
                $detector->setUserAgent($matches['agent']);

                $detector->parse();
                $matches['os'] = data_get($detector->getOs(), 'name');
                $matches['architecture'] = data_get($detector->getOs(), 'platform');
                $matches['browser'] = data_get($detector->getClient(), 'name');

            } catch (\Exception $e) {
                $this->error("Ошибка парсинга: {$e->getMessage()}");
            }
        }

        return [
            'ip' => $matches['ip'],
            'date' => Carbon::parse($matches['date']),
            'url' => $url,
            'os' => $matches['os'] ?? null,
            'architecture' => $matches['architecture'] ?? null,
            'browser' => $matches['browser'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
