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

`make env` - добавить в .env данные БД <br>
`make init` <br>

Дальнейшие запуски выполняются с помощью 

`make up/down` <br>


## Доступные команды

Для управления проектом используйте следующие команды:

- `make init` — инициализация проекта (создание .env, установка зависимостей, миграции)
- `make up` — запустить приложение
- `make down` — остановить приложение
- `make build` — пересобрать Docker-образы
- `make env` — создать `.env` файл из `.env.example`
- `make migrate` — выполнить миграции базы данных

### Для парсинга логов из корня приложения
- `sudo make logs-parse path=storage/logs/modimio.access.log` 
- `sudo docker compose exec -it app php artisan log:parse storage/logs/modimio.access.log`
  
