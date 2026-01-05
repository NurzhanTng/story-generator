from pydantic import BaseModel, Field
from typing import List, Literal

class StoryRequest(BaseModel):
    age: int = Field(gt=0)
    language: Literal["ru", "kk"]
    characters: List[str] = Field(min_items=1)
