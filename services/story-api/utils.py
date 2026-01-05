from datetime import datetime

def make_prompt(data) -> str:
    """
    Формирует prompt для OpenAI из данных запроса.
    """
    return (
        f"Напиши сказку на {data.language} языке "
        f"для ребёнка {data.age} лет "
        f"с персонажами: {', '.join(data.characters)}."
    )

def markdown_header(data) -> str:
    """
    Формирует Markdown шапку для сказки.
    """
    return (
        f"# Сказка для {data.age} лет\n\n"
        f"**Язык:** {data.language}\n"
        f"**Персонажи:** {', '.join(data.characters)}\n\n"
    )

def markdown_footer() -> str:
    """
    Добавляет дату генерации в конец Markdown.
    """
    return f"\n\n---\n_Сгенерировано: {datetime.utcnow().isoformat()}_"
