from openai import OpenAI
import os
from dotenv import load_dotenv

load_dotenv()

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

def stream_story(prompt: str):
    """
    Генерация сказки в потоковом режиме через OpenAI API.
    """
    response = client.responses.create(
        model="gpt-4.1-mini",
        input=prompt,
        stream=True,
    )
    for event in response:
        if event.type == "response.output_text.delta":
            yield event.delta
