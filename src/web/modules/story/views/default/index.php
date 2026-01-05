<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Генератор сказок';

// Подключаем marked.js для парсинга Markdown
$this->registerJsFile('https://cdn.jsdelivr.net/npm/marked/marked.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
/* Стиль в духе testter.kz */
:root {
    --primary: #6C5CE7;
    --primary-dark: #5F4FD1;
    --secondary: #FF6B8A;
    --text: #2D3436;
    --text-light: #636E72;
    --bg-light: #F8F9FE;
    --border: #E3E8F0;
}

.story-generator-wrapper {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.generator-header {
    text-align: center;
    margin-bottom: 50px;
}

.generator-header h1 {
    font-size: 42px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 15px;
}

.generator-header p {
    font-size: 18px;
    color: var(--text-light);
}

.form-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08);
    margin-bottom: 30px;
}

.form-group label {
    font-weight: 600;
    color: var(--text);
    margin-bottom: 10px;
    display: block;
}

.form-control {
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 16px;
    transition: all 0.3s;
    min-width: 300px;
    margin-bottom: 20px;
}

.form-control:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
}

.character-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.character-checkboxes label {
    background: var(--bg-light);
    padding: 12px 20px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.character-checkboxes label:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

.character-checkboxes input[type="checkbox"] {
    margin-right: 10px;
}

.character-checkboxes input[type="checkbox"]:checked + span {
    font-weight: 600;
}

.btn-generate {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 20px;
    width: 100%;
}

.btn-generate:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 92, 231, 0.3);
}

.btn-generate:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.story-output {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08);
    display: none;
}

.story-output.active {
    display: block;
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.loading-indicator {
    text-align: center;
    padding: 30px;
}

.loading-spinner {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 4px solid var(--bg-light);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.story-content {
    line-height: 1.8;
    color: var(--text);
}

.story-content h1 {
    color: var(--primary);
    margin-bottom: 20px;
}

.story-content h2, .story-content h3 {
    color: var(--text);
    margin-top: 25px;
    margin-bottom: 15px;
}

.story-content p {
    margin-bottom: 15px;
}

.story-content strong {
    color: var(--primary);
}

.story-content hr {
    border: none;
    border-top: 2px solid var(--border);
    margin: 30px 0;
}

.typing-cursor {
    display: inline-block;
    width: 3px;
    height: 1.2em;
    background: var(--primary);
    margin-left: 2px;
    animation: blink 1s infinite;
    vertical-align: text-bottom;
}

@keyframes blink {
    0%, 49% { opacity: 1; }
    50%, 100% { opacity: 0; }
}

.error-message {
    background: #FFE5E5;
    color: #D63031;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 4px solid #D63031;
}
</style>

<div class="story-generator-wrapper">
    <div class="generator-header">
        <h1>✨ Генератор сказок</h1>
        <p>Создайте уникальную сказку с помощью искусственного интеллекта</p>
    </div>

    <div class="form-container">
        <?php $form = ActiveForm::begin([
            'id' => 'story-form',
            'options' => [
                'class' => 'story-form',
                'onsubmit' => 'return false;', // блокируем стандартный submit
            ],
            'enableClientValidation' => false, // отключаем JS-валидацию
            'enableAjaxValidation' => false,   // отключаем ajax-валидацию
        ]); ?>

        <?= $form->field($model, 'age')->input('number', [
            'class' => 'form-control',
            'min' => 3,
            'max' => 18,
            'placeholder' => 'Введите возраст ребенка'
        ])->label('Возраст ребенка') ?>

        <?= $form->field($model, 'language')->dropDownList([
            'ru' => '🇷🇺 Русский',
            'kk' => '🇰🇿 Қазақша',
        ], [
            'class' => 'form-control'
        ])->label('Язык сказки') ?>

        <div class="form-group">
            <label>Выберите персонажей</label>
            <div class="character-checkboxes">
                <?php
                $characters = [
                    'Заяц' => '🐰 Заяц',
                    'Волк' => '🐺 Волк',
                    'Лиса' => '🦊 Лиса',
                    'Медведь' => '🐻 Медведь',
                    'Ёж' => '🦔 Ёж',
                    'Алдар Көсе' => '🤵 Алдар Көсе',
                ];
                foreach ($characters as $value => $label):
                ?>
                <label>
                    <input type="checkbox" name="StoryForm[characters][]" value="<?= Html::encode($value) ?>">
                    <span><?= $label ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn-generate" id="generate-btn">
            🎭 Сгенерировать сказку
        </button>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="story-output" id="story-output">
        <div class="loading-indicator" id="loading">
            <div class="loading-spinner"></div>
            <p style="margin-top: 20px; color: var(--text-light);">Генерируем вашу сказку...</p>
        </div>
        <div class="error-message" id="error-msg" style="display: none;"></div>
        <div class="story-content" id="story-content"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('story-form');
    const generateBtn = document.getElementById('generate-btn');
    const output = document.getElementById('story-output');
    const loading = document.getElementById('loading');
    const errorMsg = document.getElementById('error-msg');
    const content = document.getElementById('story-content');
    
    let accumulatedMarkdown = '';

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset
        output.classList.add('active');
        loading.style.display = 'block';
        errorMsg.style.display = 'none';
        content.innerHTML = '';
        content.style.display = 'none';
        accumulatedMarkdown = '';
        generateBtn.disabled = true;
        generateBtn.textContent = '⏳ Генерация...';

        // Собираем данные формы
        const formData = new FormData(form);
        const data = {
            age: formData.get('StoryForm[age]'),
            language: formData.get('StoryForm[language]'),
            characters: formData.getAll('StoryForm[characters][]')
        };

        // Валидация на клиенте
        if (!data.age || !data.language || data.characters.length === 0) {
            errorMsg.textContent = '❌ Пожалуйста, заполните все поля формы';
            errorMsg.style.display = 'block';
            loading.style.display = 'none';
            generateBtn.disabled = false;
            generateBtn.textContent = '🎭 Сгенерировать сказку';
        }

        // Используем fetch для streaming вместо EventSource
        fetch('<?= Url::to(['default/stream']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            function processChunk({done, value}) {
                if (done) {
                    // Финальный рендер без курсора
                    if (accumulatedMarkdown) {
                        content.innerHTML = marked.parse(accumulatedMarkdown);
                    }
                    generateBtn.disabled = false;
                    generateBtn.textContent = '🎭 Сгенерировать новую сказку';
                    return;
                }

                buffer += decoder.decode(value, {stream: true});
                const lines = buffer.split('\n\n');
                buffer = lines.pop() || ''; // Сохраняем неполную строку

                lines.forEach(line => {
                    if (line.startsWith('data: ')) {
                        try {
                            const jsonData = JSON.parse(line.slice(6));
                            
                            if (jsonData.error) {
                                loading.style.display = 'none';
                                errorMsg.textContent = '❌ Ошибка: ' + (typeof jsonData.error === 'object' 
                                    ? JSON.stringify(jsonData.error) 
                                    : jsonData.error);
                                errorMsg.style.display = 'block';
                                generateBtn.disabled = false;
                                generateBtn.textContent = '🎭 Сгенерировать сказку';
                                return;
                            }

                            if (jsonData.done) {
                                // Завершение - финальный рендер
                                if (accumulatedMarkdown) {
                                    content.innerHTML = marked.parse(accumulatedMarkdown);
                                }
                                generateBtn.disabled = false;
                                generateBtn.textContent = '🎭 Сгенерировать новую сказку';
                                return;
                            }

                            if (jsonData.chunk) {
                                if (loading.style.display !== 'none') {
                                    loading.style.display = 'none';
                                    content.style.display = 'block';
                                }

                                accumulatedMarkdown += jsonData.chunk;
                                content.innerHTML = marked.parse(accumulatedMarkdown) + 
                                    '<span class="typing-cursor"></span>';
                                content.scrollIntoView({ behavior: 'smooth', block: 'end' });
                            }
                        } catch (e) {
                            console.error('Parse error:', e, line);
                        }
                    }
                });

                return reader.read().then(processChunk);
            }

            return reader.read().then(processChunk);
        })
        .catch(error => {
            loading.style.display = 'none';
            errorMsg.textContent = '❌ Не удалось подключиться к серверу: ' + error.message;
            errorMsg.style.display = 'block';
            generateBtn.disabled = false;
            generateBtn.textContent = '🎭 Сгенерировать сказку';
        });

        // Скрываем loading после первого чанка
        let firstChunk = true;

        return false;
    });
});
</script>