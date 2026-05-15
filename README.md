# Cyberteka

Cyberteka — мини-приложение интернет-магазина на React с PHP backend и MySQL.

## Стек

- Frontend: React, Vite, React Router
- Backend: PHP 8.2, Apache
- Database: MySQL 8
- Запуск окружения: Docker Compose

## Структура проекта

- `frontend/` — React-приложение
- `backend/` — PHP backend, API и конфигурация Apache
- `backend/database/cyberteka.sql` — SQL-скрипт для создания и наполнения базы
- `docker-compose.yml` — запуск frontend, backend, MySQL и phpMyAdmin

## Быстрый запуск через Docker

Перед запуском должен быть установлен и запущен Docker Desktop.

Из корня проекта выполните:

```bash
docker-compose up --build -d
```

После запуска сайт будет доступен по адресам:

- Frontend: [http://localhost:3000](http://localhost:3000)
- Backend: [http://localhost:8080](http://localhost:8080)
- phpMyAdmin: [http://localhost:8081](http://localhost:8081)

## Доступ к базе данных

Для входа в phpMyAdmin:

- сервер: `db`
- пользователь: `cyberteka`
- пароль: `cyberteka`
- база данных: `cyberteka`

База создаётся автоматически при первом запуске контейнера `db`. Для этого используется файл:

```text
backend/database/cyberteka.sql
```

Если нужно пересоздать базу с нуля, остановите контейнеры с удалением volume:

```bash
docker-compose down -v
docker-compose up --build -d
```

## Управление проектом

Остановить контейнеры:

```bash
docker-compose down
```

Посмотреть статус контейнеров:

```bash
docker-compose ps
```

Посмотреть логи frontend:

```bash
docker-compose logs -f frontend
```

Посмотреть логи backend:

```bash
docker-compose logs -f backend
```

## Локальная проверка frontend

Если нужно проверить только React-приложение без Docker:

```bash
cd frontend
npm install
npm run dev
```

Сборка frontend:

```bash
cd frontend
npm run build
```

Проверка ESLint:

```bash
cd frontend
npm run lint
```

## Функциональность

- каталог товаров с карточками
- фильтрация и сортировка товаров
- добавление товаров в избранное
- добавление товаров в корзину
- изменение количества товаров в корзине
- регистрация, вход и выход из аккаунта
- страница профиля с редактированием данных
- подписка на новости через форму
- загрузка данных через API-запросы к backend

