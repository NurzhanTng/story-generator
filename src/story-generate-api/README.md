# Установка и запуск Python API (локально)

## Требования

- Python 3.10+
- pip
- virtualenv / venv
- Доступ к интернету для получения OpenAI API Key

---

## 1. Клонирование проекта

```bash
git clone https://github.com/NurzhanTng/story-generator.git
cd src/story-generate-api
```

---

## 2. Получение OpenAI API Key

1. Перейти на сайт OpenAI
   [https://platform.openai.com/account/api-keys](https://platform.openai.com/account/api-keys)
2. Создать новый API Key
3. Сохранить ключ — он потребуется для `.env`

⚠️ Ключ **нельзя коммитить в репозиторий**

---

## 3. Создание виртуального окружения

```bash
python3 -m venv venv
```

---

## 4. Активация виртуального окружения

### Linux / macOS

```bash
source venv/bin/activate
```

### Windows (PowerShell)

```powershell
venv\Scripts\activate
```

После активации в терминале появится `(venv)`.

---

## 5. Создание `.env` файла

В корне проекта создать файл `.env`:

```env
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxx
```

---

## 6. Установка зависимостей

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

---

## 7. Запуск приложения через Uvicorn

```bash
uvicorn main:app --reload
```

---

## 8. Проверка работы API

Открыть в браузере:

```
http://127.0.0.1:8000
```

Swagger-документация:

```
http://127.0.0.1:8000/docs
```

---

## 9. Интеграция с Yii2

URL сервиса должен совпадать со значением в `.env` Yii2-проекта:

```env
PYTHON_API_URL=http://127.0.0.1:8000
```

Yii2 использует этот адрес для генерации сказок через API.

---

## Примечания

- Виртуальное окружение должно быть активировано **каждый раз перед запуском**
- OpenAI API Key читается из `.env`
