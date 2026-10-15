from pathlib import Path
import json

import cv2
from insightface.app import FaceAnalysis


IMAGE_PATH = Path(
    r"C:\xampp\htdocs\bioverify-ai\storage\app\public\people\1\faces\VDm2x8pkhGTItXIDs1H14OGHxplwxUpoGvX8E19D.jpg"
)


print("Loading InsightFace model...")

app = FaceAnalysis(
    name="buffalo_l",
    providers=["CPUExecutionProvider"],
)

app.prepare(
    ctx_id=0,
    det_size=(640, 640),
)

print("Model loaded.")

image = cv2.imread(str(IMAGE_PATH))

if image is None:
    raise RuntimeError("Could not read the image.")

faces = app.get(image)

print(f"Faces detected: {len(faces)}")

if len(faces) == 0:
    raise RuntimeError("No face detected.")

if len(faces) > 1:
    raise RuntimeError(
        "Multiple faces detected. Please use an image containing one person."
    )

face = faces[0]

embedding = face.embedding.tolist()

print()
print("Face detected successfully.")
print(f"Detection score: {face.det_score:.5f}")
print(f"Embedding length: {len(embedding)}")

with open("face_embedding.json", "w", encoding="utf-8") as file:
    json.dump(embedding, file)

print()
print("Embedding saved to:")
print("face_embedding.json")