# Laravel парсинг логов

## Технологии

- **Laravel 13+**
- **PHP 8.4+**
- **MySQL** — БД
- **Docker** — локальная разработка


## Запуск приложения

Для локальной разработки рекомендуется склонировать репозиторий traefik (https://github.com/David-Gelmut/fortress_test.git)

Приложение доступно по http://localhost:8080/


Для первого запуска, нужно выполнить команду

`cp .env.example .env` - добавить в .env данные БД <br>
`composer install` - добавить в .env данные БД <br>
`php artisan key:generate` - добавить в .env данные БД <br>
`make init` <br>

Дальнейшие запуски выполняются с помощью 

`make up/down` <br>


## Доступные команды

Для управления проектом используйте следующие команды:

- `make init` — инициализация проекта (установка зависимостей, миграции)
- `make up` — запустить приложение
- `make down` — остановить приложение
- `make build` — пересобрать Docker-образы
- `make env` — создать `.env` файл из `.env.example`
- `make migrate` — выполнить миграции базы данных

### Для парсинга логов из корня приложения
- `sudo make logs-parse path=storage/logs/modimio.access.log` 
- `sudo docker compose exec -it app php artisan log:parse storage/logs/modimio.access.log`

### Меняем права для Linux
- `sudo chown -R www-data:www-data storage bootstrap/cache`
- `sudo chmod -R 775 storage bootstrap/cache`

### Для запуска фронтенда
- `npm install`
- `npm run dev`
  
  
