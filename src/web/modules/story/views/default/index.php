<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use app\modules\story\models\StoryHistory;

$this->title = 'Генератор сказок';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/marked/marked.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Получаем историю сказок
$stories = StoryHistory::getRecentStories();
?>

<style>
:root {
    --primary: #6C5CE7;
    --primary-dark: #5F4FD1;
    --secondary: #FF6B8A;
    --text: #2D3436;
    --text-light: #636E72;
    --bg-light: #F8F9FE;
    --border: #E3E8F0;
    --sidebar-bg: #F8F9FE;
    --hover-bg: #E8EAFD;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: white;
    color: var(--text);
}

.story-app {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

/* Сайдбар со списком историй */
.sidebar {
    width: 300px;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    transition: transform 0.3s;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid var(--border);
}

.new-story-btn {
    width: 100%;
    padding: 14px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    font-size: 15px;
}

.new-story-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.story-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.story-item {
    padding: 14px;
    margin-bottom: 6px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.story-item:hover {
    background: var(--hover-bg);
    border-color: var(--border);
}

.story-item.active {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(108, 92, 231, 0.1);
}

.story-item-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    color: var(--text);
}

.story-item-meta {
    font-size: 12px;
    color: var(--text-light);
    display: flex;
    gap: 12px;
}

.story-item-date {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Основная область */
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
}

.main-header {
    padding: 20px 30px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.main-header h1 {
    font-size: 24px;
    font-weight: 700;
}

.chat-area {
    flex: 1;
    overflow-y: auto;
    padding: 30px;
}

/* Режим просмотра существующей сказки */
.story-view {
    max-width: 800px;
    margin: 0 auto;
}

.story-view-header {
    margin-bottom: 30px;
}

.story-view-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 12px;
}

.story-view-meta {
    display: flex;
    gap: 20px;
    color: var(--text-light);
    font-size: 14px;
}

.story-view-content {
    line-height: 1.8;
    font-size: 16px;
}

.story-view-content h1, 
.story-view-content h2, 
.story-view-content h3 {
    margin-top: 24px;
    margin-bottom: 16px;
    color: var(--text);
}

.story-view-content p {
    margin-bottom: 16px;
}

/* Режим создания новой сказки */
.new-story-mode {
    max-width: 800px;
    margin: 0 auto;
}

.welcome-message {
    text-align: center;
    padding: 60px 20px;
}

.welcome-message h2 {
    font-size: 28px;
    color: var(--text);
    margin-bottom: 12px;
}

.welcome-message p {
    font-size: 16px;
    color: var(--text-light);
}

/* Область генерации */
.generation-area {
    margin-top: 20px;
}

.character-input-section {
    background: var(--bg-light);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
}

.character-input-section h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
}

.character-input-row {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.character-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
}

.character-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
}

.add-character-btn {
    padding: 12px 24px;
    background: white;
    border: 2px solid var(--primary);
    color: var(--primary);
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.add-character-btn:hover {
    background: var(--primary);
    color: white;
}

.characters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.character-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: white;
    border: 2px solid var(--primary);
    color: var(--primary);
    border-radius: 20px;
    font-weight: 500;
    font-size: 14px;
}

.character-tag .remove-btn {
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
}

/* Панель ввода снизу */
.input-panel {
    border-top: 1px solid var(--border);
    padding: 20px 30px;
    background: white;
}

.input-panel-inner {
    max-width: 800px;
    margin: 0 auto;
}

.settings-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.setting-item {
    flex: 1;
}

.setting-item label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-light);
    margin-bottom: 6px;
}

.setting-item input,
.setting-item select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.setting-item input:focus,
.setting-item select:focus {
    outline: none;
    border-color: var(--primary);
}

.generate-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.generate-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(108, 92, 231, 0.3);
}

.generate-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Область вывода генерации */
.generation-output {
    background: var(--bg-light);
    border-radius: 16px;
    padding: 24px;
    margin-top: 20px;
    min-height: 200px;
}

.generation-output-content {
    line-height: 1.8;
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

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.error-message {
    background: #FFE5E5;
    color: #D63031;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
    border-left: 4px solid #D63031;
}

/* Скрыть элементы по умолчанию */
.hidden {
    display: none !important;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        z-index: 1000;
        transform: translateX(-100%);
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .settings-row {
        flex-direction: column;
    }
}
</style>

<div class="story-app">
    <!-- Сайдбар с историей -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="new-story-btn" id="newStoryBtn">
                <span>➕</span>
                <span>Новая сказка</span>
            </button>
        </div>
        
        <div class="story-list" id="storyList">
            <?php if (empty($stories)): ?>
                <div style="padding: 20px; text-align: center; color: var(--text-light);">
                    <p>История пуста</p>
                    <p style="font-size: 12px; margin-top: 8px;">Создайте свою первую сказку!</p>
                </div>
            <?php else: ?>
                <?php foreach ($stories as $story): ?>
                    <div class="story-item" data-id="<?= $story->id ?>">
                        <div class="story-item-title"><?= Html::encode($story->title) ?></div>
                        <div class="story-item-meta">
                            <span class="story-item-date">
                                🕒 <?= $story->getFormattedDate() ?>
                            </span>
                            <span>
                                <?= $story->language === 'ru' ? '🇷🇺' : '🇰🇿' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Основная область -->
    <div class="main-content">
        <div class="main-header">
            <h1>✨ Генератор сказок</h1>
        </div>

        <div class="chat-area" id="chatArea">
            <!-- Режим просмотра существующей сказки -->
            <div class="story-view hidden" id="storyView">
                <div class="story-view-header">
                    <h2 class="story-view-title" id="viewTitle"></h2>
                    <div class="story-view-meta" id="viewMeta"></div>
                </div>
                <div class="story-view-content" id="viewContent"></div>
            </div>

            <!-- Режим создания новой сказки -->
            <div class="new-story-mode" id="newStoryMode">
                <div class="welcome-message">
                    <img src="<?= Yii::getAlias('@web/images/story_icon.png') ?>" 
                        alt="Генератор сказок" 
                        style="width:160px; height:auto; margin-bottom:0px;">
                    <h2>Создайте волшебную сказку</h2>
                    <p>Добавьте персонажей и настройте параметры ниже</p>
                </div>

                <div class="generation-area">
                    <div class="character-input-section">
                        <h3>Персонажи сказки</h3>
                        <div class="character-input-row">
                            <input type="text" 
                                   class="character-input" 
                                   id="characterInput" 
                                   placeholder="Введите имя персонажа...">
                            <button class="add-character-btn" id="addCharacterBtn">
                                ➕ Добавить
                            </button>
                        </div>
                        <div class="characters-list" id="charactersList"></div>
                    </div>

                    <!-- Область вывода генерации -->
                    <div class="generation-output hidden" id="generationOutput">
                        <div class="error-message hidden" id="errorMessage"></div>
                        <div class="generation-output-content" id="outputContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Панель ввода снизу -->
        <div class="input-panel" id="inputPanel">
            <div class="input-panel-inner">
                <div class="settings-row">
                    <div class="setting-item">
                        <label>Возраст ребенка</label>
                        <input type="number" id="ageInput" min="3" max="18" value="7" placeholder="От 3 до 18">
                    </div>
                    <div class="setting-item">
                        <label>Язык сказки</label>
                        <select id="languageSelect">
                            <option value="ru">🇷🇺 Русский</option>
                            <option value="kk">🇰🇿 Қазақша</option>
                        </select>
                    </div>
                    <div class="setting-item">
                        <label>Выбрано персонажей</label>
                        <input type="text" id="characterCount" readonly value="0" style="background: var(--bg-light);">
                    </div>
                </div>
                <button class="generate-btn" id="generateBtn">
                    <span>✨</span>
                    <span>Сгенерировать сказку</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const App = {
    characters: [],
    currentStoryId: null,
    isGenerating: false,
    accumulatedMarkdown: '',

    init() {
        this.bindEvents();
        this.updateCharacterCount();
    },

    bindEvents() {
        // Новая сказка
        document.getElementById('newStoryBtn').addEventListener('click', () => {
            this.showNewStoryMode();
        });

        // Добавить персонажа
        document.getElementById('addCharacterBtn').addEventListener('click', () => {
            this.addCharacter();
        });

        document.getElementById('characterInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.addCharacter();
            }
        });

        // Генерация
        document.getElementById('generateBtn').addEventListener('click', () => {
            this.generateStory();
        });

        // Клик по истории
        document.querySelectorAll('.story-item').forEach(item => {
            item.addEventListener('click', () => {
                const storyId = item.dataset.id;
                this.loadStory(storyId);
            });
        });
    },

    showNewStoryMode() {
        this.currentStoryId = null;
        this.characters = [];
        this.accumulatedMarkdown = '';
        
        document.getElementById('storyView').classList.add('hidden');
        document.getElementById('newStoryMode').classList.remove('hidden');
        document.getElementById('inputPanel').classList.remove('hidden');
        document.getElementById('generationOutput').classList.add('hidden');
        document.getElementById('characterInput').value = '';
        
        this.updateCharactersList();
        this.updateCharacterCount();
        
        // Убрать активный класс у всех элементов истории
        document.querySelectorAll('.story-item').forEach(item => {
            item.classList.remove('active');
        });
    },

    addCharacter() {
        const input = document.getElementById('characterInput');
        const name = input.value.trim();
        
        if (name && !this.characters.includes(name)) {
            this.characters.push(name);
            this.updateCharactersList();
            this.updateCharacterCount();
            input.value = '';
            input.focus();
        }
    },

    removeCharacter(name) {
        this.characters = this.characters.filter(c => c !== name);
        this.updateCharactersList();
        this.updateCharacterCount();
    },

    updateCharactersList() {
        const list = document.getElementById('charactersList');
        list.innerHTML = this.characters.map(name => `
            <div class="character-tag">
                <span>${name}</span>
                <span class="remove-btn" onclick="App.removeCharacter('${name}')">✕</span>
            </div>
        `).join('');
    },

    updateCharacterCount() {
        document.getElementById('characterCount').value = this.characters.length;
    },

    async generateStory() {
        if (this.isGenerating) return;

        const age = document.getElementById('ageInput').value;
        const language = document.getElementById('languageSelect').value;

        if (!age || this.characters.length === 0) {
            this.showError('Пожалуйста, укажите возраст и добавьте хотя бы одного персонажа');
            return;
        }

        this.isGenerating = true;
        this.accumulatedMarkdown = '';

        const generateBtn = document.getElementById('generateBtn');
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<span class="loading-spinner"></span><span>Генерация...</span>';

        const output = document.getElementById('generationOutput');
        const content = document.getElementById('outputContent');
        const errorMsg = document.getElementById('errorMessage');

        output.classList.remove('hidden');
        errorMsg.classList.add('hidden');
        content.innerHTML = '<p style="color: var(--text-light);">Начинаем генерацию сказки...</p>';

        try {
            const response = await fetch('<?= Url::to(['default/stream']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                },
                body: JSON.stringify({
                    age: parseInt(age),
                    language: language,
                    characters: this.characters
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            const processChunk = async ({done, value}) => {
                if (done) {
                    if (this.accumulatedMarkdown) {
                        content.innerHTML = marked.parse(this.accumulatedMarkdown);
                    }
                    generateBtn.disabled = false;
                    generateBtn.innerHTML = '<span>✨</span><span>Сгенерировать новую сказку</span>';
                    this.isGenerating = false;
                    
                    // Обновить список историй
                    setTimeout(() => this.reloadStoryList(), 500);
                    return;
                }

                buffer += decoder.decode(value, {stream: true});
                const lines = buffer.split('\n\n');
                buffer = lines.pop() || '';

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const jsonData = JSON.parse(line.slice(6));
                            
                            if (jsonData.error) {
                                this.showError(jsonData.error);
                                generateBtn.disabled = false;
                                generateBtn.innerHTML = '<span>✨</span><span>Сгенерировать сказку</span>';
                                this.isGenerating = false;
                                return;
                            }

                            if (jsonData.done) {
                                if (this.accumulatedMarkdown) {
                                    content.innerHTML = marked.parse(this.accumulatedMarkdown);
                                }
                                generateBtn.disabled = false;
                                generateBtn.innerHTML = '<span>✨</span><span>Сгенерировать новую сказку</span>';
                                this.isGenerating = false;
                                setTimeout(() => this.reloadStoryList(), 500);
                                return;
                            }

                            if (jsonData.chunk) {
                                this.accumulatedMarkdown += jsonData.chunk;
                                content.innerHTML = marked.parse(this.accumulatedMarkdown) + 
                                    '<span class="typing-cursor"></span>';
                                content.scrollIntoView({ behavior: 'smooth', block: 'end' });
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                        }
                    }
                }

                return reader.read().then(processChunk);
            };

            await reader.read().then(processChunk);

        } catch (error) {
            this.showError('Не удалось подключиться к серверу: ' + error.message);
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<span>✨</span><span>Сгенерировать сказку</span>';
            this.isGenerating = false;
        }
    },

    showError(message) {
        const errorMsg = document.getElementById('errorMessage');
        errorMsg.textContent = '❌ ' + message;
        errorMsg.classList.remove('hidden');
    },

    async loadStory(id) {
        try {
            // Формируем правильный URL с параметром id
            const baseUrl = '<?= Url::to(['default/view']) ?>';
            // В Yii2 при использовании ?r= параметры должны идти через &, а не через ?
            const separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';
            const url = baseUrl + separator + 'id=' + encodeURIComponent(id);
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                this.currentStoryId = id;
                
                document.getElementById('newStoryMode').classList.add('hidden');
                document.getElementById('inputPanel').classList.add('hidden');
                document.getElementById('storyView').classList.remove('hidden');

                document.getElementById('viewTitle').textContent = data.story.title;
                document.getElementById('viewMeta').innerHTML = `
                    <span>👤 Возраст: ${data.story.age} лет</span>
                    <span>${data.story.language === 'ru' ? '🇷🇺 Русский' : '🇰🇿 Қазақша'}</span>
                    <span>👥 Персонажи: ${data.story.characters.join(', ')}</span>
                    <span>🕒 ${data.story.created_at}</span>
                `;
                document.getElementById('viewContent').innerHTML = marked.parse(data.story.story_text);

                // Активировать элемент в списке
                document.querySelectorAll('.story-item').forEach(item => {
                    item.classList.toggle('active', item.dataset.id == id);
                });
            }
        } catch (error) {
            console.error('Failed to load story:', error);
        }
    },

    async reloadStoryList() {
        try {
            const response = await fetch('<?= Url::to(['default/list']) ?>');
            const data = await response.json();

            if (data.success) {
                const listHtml = data.stories.map(story => `
                    <div class="story-item" data-id="${story.id}">
                        <div class="story-item-title">${story.title}</div>
                        <div class="story-item-meta">
                            <span class="story-item-date">🕒 ${story.created_at}</span>
                            <span>${story.language === 'ru' ? '🇷🇺' : '🇰🇿'}</span>
                        </div>
                    </div>
                `).join('');

                document.getElementById('storyList').innerHTML = listHtml || `
                    <div style="padding: 20px; text-align: center; color: var(--text-light);">
                        <p>История пуста</p>
                    </div>
                `;

                // Перебиндить события
                document.querySelectorAll('.story-item').forEach(item => {
                    item.addEventListener('click', () => {
                        this.loadStory(item.dataset.id);
                    });
                });
            }
        } catch (error) {
            console.error('Failed to reload story list:', error);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
</script>