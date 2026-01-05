# Yii2 Project

<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Project</h1>
</p>

Краткий шаблон Yii2 для быстрого старта проекта.

---

## Структура проекта

```
assets/ — ассеты
commands/ — консольные команды
config/ — конфигурации приложения
controllers/ — контроллеры Web
mail/ — шаблоны писем
models/ — модели
runtime/ — временные файлы приложения
tests/ — тесты
vendor/ — зависимости Composer
views/ — шаблоны Web
web/ — точка входа и веб-ресурсы
```

---

## Шаги установки

### 1. Скачивание проекта

Склонируйте репозиторий:

```
git clone https://github.com/NurzhanTng/story-generator.git
cd src/web
```

### 2. Установка PHP-зависимостей

```
composer install
```

### 3. Генерация базы данных

Запустите скрипт генерации базы (скрипт создаст все необходимые таблицы и данные по умолчанию):

```
./scripts/setup_db.sh
```

### 4. Создание `.env` файла

В корне проекта создать файл `.env` и вставить пароль из предыдущего этапа:

```
# Environment
APP_ENV=dev
APP_DEBUG=1

# Database
DB_HOST=localhost
DB_NAME=story_db
DB_USER=story_user
DB_PASSWORD=ВАШ_ПАРОЛЬ
DB_CHARSET=utf8mb4

# Python API
PYTHON_API_URL=http://127.0.0.1:8000
```

### 5. Применение миграций

Если есть миграции, примените их:

```
php yii migrate --migrationPath=@app/modules/story/migrations
```

### 6. Запуск приложения

Запустите встроенный веб-сервер Yii2:

```
php yii serve --docroot=web
```

Откройте в браузере:

```
http://localhost:8080
```

---

## Примечания

- Минимальные требования: PHP 7.4+, MySQL 5.7+
- После генерации базы и настройки `config/db.php` проект готов к использованию.
