from fastapi import FastAPI
from fastapi.responses import StreamingResponse
from schemas import StoryRequest
from openai_client import stream_story
from utils import make_prompt, markdown_header, markdown_footer

app = FastAPI(title="Story Generator API")

@app.post("/generate_story")
def generate_story(data: StoryRequest):
    prompt = make_prompt(data)

    def stream():
        yield markdown_header(data)
        for chunk in stream_story(prompt):
            yield chunk
        yield markdown_footer()

    return StreamingResponse(stream(), media_type="text/markdown")
