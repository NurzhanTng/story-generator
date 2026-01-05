#!/bin/bash

# Скрипт для настройки базы данных MySQL для проекта Story

echo "🔧 Настройка базы данных для проекта Story"
echo "============================================"
echo ""

# Запрашиваем данные
read -p "Введите имя базы данных (по умолчанию: story_db): " DB_NAME
DB_NAME=${DB_NAME:-story_db}

read -p "Введите имя пользователя БД (по умолчанию: story_user): " DB_USER
DB_USER=${DB_USER:-story_user}

read -sp "Введите пароль для пользователя БД: " DB_PASSWORD
echo ""

if [ -z "$DB_PASSWORD" ]; then
    echo "❌ Пароль не может быть пустым!"
    exit 1
fi

echo ""
echo "📋 Параметры подключения:"
echo "  База данных: $DB_NAME"
echo "  Пользователь: $DB_USER"
echo "  Хост: localhost"
echo ""

# Создаем SQL команды
SQL_COMMANDS="
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Создание пользователя
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';

-- Выдача прав
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';

-- Применение изменений
FLUSH PRIVILEGES;

-- Выбор базы данных
USE \`${DB_NAME}\`;

SELECT 'Database setup completed successfully!' as Status;
"

# Выполнение SQL команд
echo "🚀 Выполнение SQL команд..."
echo ""

# Используем sudo для входа в MySQL от root
sudo mysql -e "$SQL_COMMANDS"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ База данных успешно настроена!"
    echo ""
    echo "📝 Обновите конфигурацию Yii2:"
    echo "   Файл: config/db.php"
    echo ""
    echo "   return ["
    echo "       'class' => 'yii\db\Connection',"
    echo "       'dsn' => 'mysql:host=localhost;dbname=${DB_NAME}',"
    echo "       'username' => '${DB_USER}',"
    echo "       'password' => '${DB_PASSWORD}',"
    echo "       'charset' => 'utf8mb4',"
    echo "   ];"
    echo ""
    echo "🎯 Следующие шаги:"
    echo "   1. Обновите config/db.php"
    echo "   2. Запустите миграцию: php yii migrate --migrationPath=@app/modules/story/migrations"
else
    echo ""
    echo "❌ Ошибка при настройке базы данных!"
    echo "Попробуйте выполнить команды вручную."
fi