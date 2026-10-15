from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from insightface.app import FaceAnalysis

import cv2
import numpy as np
import onnxruntime as ort
import json
import os
import shutil
import subprocess
import tempfile
import time
from pathlib import Path



# ============================================================
# FASTAPI APPLICATION
# ============================================================

app = FastAPI(
    title="BioVerify AI Service",
    version="1.0.0",
)


# ============================================================
# INSIGHTFACE MODEL
# ============================================================

MODEL_NAME = "buffalo_l"
MODEL_VERSION = "buffalo_l"

# Passive liveness (anti-spoofing) score from InsightFace's verified
# addon (~/.insightface/addons/liveness.onnx). "observe" mode keeps the
# embedding for every face and only reports the score; Laravel decides
# whether a non-live result blocks verification.
LIVENESS_THRESHOLD = float(os.getenv("FACE_LIVENESS_THRESHOLD", "0.80"))

face_app = FaceAnalysis(
    name=MODEL_NAME,
    providers=["CPUExecutionProvider"],
    # Only the models this service uses. Skipping the age/gender and
    # 3D-landmark models makes every request faster; embeddings are
    # unchanged because the recognition model is the same.
    allowed_modules=["detection", "recognition"],
    addons=["liveness"],
    liveness_mode="observe",
    liveness_threshold=LIVENESS_THRESHOLD,
)

# 512x512 is a good speed/accuracy compromise.
#
# The browser is also sending an optimized 640px-wide image,
# so there is no need to run the detector at 640x640.
#
face_app.prepare(
    ctx_id=0,
    det_size=(512, 512),
)


# ============================================================
# FACE MATCHING SETTINGS
# ============================================================

# Temporary threshold for calibration.
#
# Do NOT consider this the final production threshold yet.
#
# We will determine the final value using actual:
#
# SAME PERSON:
#   - same image
#   - different image
#   - different lighting
#   - different angle
#
# DIFFERENT PERSON:
#   - several unrelated people
#
MATCH_THRESHOLD = 0.50


# ============================================================
# CONSTANTS
# ============================================================

EXPECTED_EMBEDDING_SIZE = 512


# ============================================================
# ROOT
# ============================================================

@app.get("/")
def root():

    return {
        "success": True,
        "service": "BioVerify AI Service",
        "status": "online",
        "model": MODEL_VERSION,
    }


# ============================================================
# HEALTH CHECK
# ============================================================

@app.get("/health")
def health():

    return {
        "success": True,
        "status": "healthy",
        "model": MODEL_VERSION,
        "face_threshold": MATCH_THRESHOLD,
        "liveness_model_loaded": "liveness" in face_app.addons,
        "liveness_threshold": LIVENESS_THRESHOLD,
        "voice_model": VOICE_MODEL_VERSION,
        "voice_model_available": VOICE_MODEL_PATH.is_file(),
        "voice_threshold": VOICE_MATCH_THRESHOLD,
    }


# ============================================================
# IMAGE DECODER
# ============================================================

def decode_image(contents: bytes):

    if not contents:

        raise HTTPException(
            status_code=422,
            detail="Empty image file."
        )


    image_array = np.frombuffer(
        contents,
        dtype=np.uint8
    )


    image = cv2.imdecode(
        image_array,
        cv2.IMREAD_COLOR
    )


    if image is None:

        raise HTTPException(
            status_code=422,
            detail="Invalid image file."
        )


    return image


# ============================================================
# GET SINGLE FACE
# ============================================================

def detect_single_face(image):

    faces = face_app.get(image)


    if len(faces) == 0:

        raise HTTPException(
            status_code=422,
            detail="No face detected."
        )


    if len(faces) > 1:

        raise HTTPException(
            status_code=422,
            detail=(
                "Multiple faces detected. "
                "Please make sure only one person is visible."
            )
        )


    return faces[0]


# ============================================================
# ENROLLMENT QUALITY CHECK
# ============================================================

# A poor enrollment photo produces a weak template that causes both
# false rejects and false matches later. Only enrollment is gated;
# recognition accepts whatever the detector finds.
MIN_ENROLL_DET_SCORE = 0.70
MIN_ENROLL_FACE_SIZE = 80


def check_enrollment_quality(face):

    if float(face.det_score) < MIN_ENROLL_DET_SCORE:

        raise HTTPException(
            status_code=422,
            detail=(
                "The face is not clear enough. Use good lighting, "
                "face the camera directly and avoid blur."
            )
        )


    x1, y1, x2, y2 = face.bbox.astype(int).tolist()

    if min(x2 - x1, y2 - y1) < MIN_ENROLL_FACE_SIZE:

        raise HTTPException(
            status_code=422,
            detail="The face is too small. Move closer to the camera."
        )


# ============================================================
# PASSIVE LIVENESS
# ============================================================

def face_liveness(face):
    """Liveness result for a detected face (observe mode)."""

    result = getattr(face, "liveness", None) or {}

    score = result.get("live_score")

    return {
        "status": result.get("status", "unavailable"),
        "is_live": result.get("is_live"),
        "live_score": None if score is None else round(float(score), 4),
        "threshold": LIVENESS_THRESHOLD,
    }


# ============================================================
# VALIDATE EMBEDDING
# ============================================================

def validate_embedding(
    embedding,
    error_message
):

    if embedding is None:

        raise HTTPException(
            status_code=422,
            detail=error_message
        )


    embedding = np.asarray(
        embedding,
        dtype=np.float32
    )


    if embedding.ndim != 1:

        raise HTTPException(
            status_code=422,
            detail="Invalid face embedding format."
        )


    if embedding.shape[0] != EXPECTED_EMBEDDING_SIZE:

        raise HTTPException(
            status_code=422,
            detail=(
                f"Invalid face embedding length: "
                f"{embedding.shape[0]}. "
                f"Expected {EXPECTED_EMBEDDING_SIZE}."
            )
        )


    if not np.all(
        np.isfinite(embedding)
    ):

        raise HTTPException(
            status_code=422,
            detail="Face embedding contains invalid values."
        )


    norm = np.linalg.norm(
        embedding
    )


    if norm == 0:

        raise HTTPException(
            status_code=422,
            detail="Invalid face embedding."
        )


    return embedding


# ============================================================
# GENERATE FACE EMBEDDING
# ============================================================

@app.post("/face/embedding")
async def face_embedding(
    face_image: UploadFile = File(...)
):

    start_time = time.perf_counter()


    try:

        # ------------------------------------------------------
        # READ IMAGE
        # ------------------------------------------------------

        contents = await face_image.read()


        # ------------------------------------------------------
        # DECODE IMAGE
        # ------------------------------------------------------

        image = decode_image(
            contents
        )


        # ------------------------------------------------------
        # DETECT SINGLE FACE
        # ------------------------------------------------------

        face = detect_single_face(
            image
        )


        # ------------------------------------------------------
        # ENROLLMENT QUALITY
        # ------------------------------------------------------

        check_enrollment_quality(
            face
        )


        # ------------------------------------------------------
        # GET EMBEDDING
        # ------------------------------------------------------

        embedding = validate_embedding(
            face.embedding,
            "Unable to generate face embedding."
        )


        # ------------------------------------------------------
        # PROCESSING TIME
        # ------------------------------------------------------

        processing_time_ms = round(
            (
                time.perf_counter() -
                start_time
            ) * 1000,
            2
        )


        # ------------------------------------------------------
        # RETURN RESULT
        # ------------------------------------------------------

        return {

            "success": True,

            "faces_detected": 1,

            "detection_score":
                float(face.det_score),

            "embedding":
                embedding.tolist(),

            "embedding_length":
                int(embedding.shape[0]),

            "model_version":
                MODEL_VERSION,

            "processing_time_ms":
                processing_time_ms,

        }


    except HTTPException:

        raise


    except Exception as e:

        raise HTTPException(
            status_code=500,
            detail=str(e)
        )


# ============================================================
# FACE RECOGNITION
# ============================================================

@app.post("/face/recognize")
async def face_recognize(

    face_image: UploadFile = File(...),

    registered_faces: str = Form(...)

):

    start_time = time.perf_counter()


    try:

        # ======================================================
        # READ IMAGE
        # ======================================================

        contents = await face_image.read()


        # ======================================================
        # DECODE IMAGE
        # ======================================================

        image = decode_image(
            contents
        )


        # ======================================================
        # DETECT SINGLE FACE
        # ======================================================

        face = detect_single_face(
            image
        )


        # ======================================================
        # GET QUERY EMBEDDING
        # ======================================================

        query_embedding = validate_embedding(
            face.embedding,
            "Unable to generate query face embedding."
        )


        # ======================================================
        # DETECTION SCORE
        # ======================================================

        detection_score = float(
            face.det_score
        )


        # ======================================================
        # BOUNDING BOX
        # ======================================================

        bbox = (
            face.bbox
            .astype(int)
            .tolist()
        )

        liveness = face_liveness(
            face
        )


        # ======================================================
        # PARSE REGISTERED FACES
        # ======================================================

        try:

            registered = json.loads(
                registered_faces
            )

        except Exception:

            raise HTTPException(
                status_code=422,
                detail="Invalid registered face data."
            )


        if not isinstance(
            registered,
            list
        ):

            raise HTTPException(
                status_code=422,
                detail="Registered faces must be a list."
            )


        # ======================================================
        # NORMALIZE QUERY EMBEDDING
        # ======================================================

        query_norm = np.linalg.norm(
            query_embedding
        )


        if query_norm == 0:

            raise HTTPException(
                status_code=422,
                detail="Invalid query face embedding."
            )


        query_normalized = (
            query_embedding /
            query_norm
        )


        # ======================================================
        # PREPARE REGISTERED EMBEDDINGS
        # ======================================================

        usable_embeddings = 0

        best_score = -1.0

        best_person_id = None


        # ======================================================
        # COMPARE REGISTERED FACES
        # ======================================================

        for registered_face in registered:

            if not isinstance(
                registered_face,
                dict
            ):

                continue


            person_id = registered_face.get(
                "person_id"
            )


            embedding = registered_face.get(
                "embedding"
            )


            if not person_id:

                continue


            if not isinstance(
                embedding,
                list
            ):

                continue


            if len(embedding) != EXPECTED_EMBEDDING_SIZE:

                continue


            try:

                stored_embedding = np.asarray(
                    embedding,
                    dtype=np.float32
                )

            except Exception:

                continue


            if stored_embedding.ndim != 1:

                continue


            if stored_embedding.shape[0] != EXPECTED_EMBEDDING_SIZE:

                continue


            if not np.all(
                np.isfinite(
                    stored_embedding
                )
            ):

                continue


            stored_norm = np.linalg.norm(
                stored_embedding
            )


            if stored_norm == 0:

                continue


            stored_normalized = (
                stored_embedding /
                stored_norm
            )


            # --------------------------------------------------
            # COSINE SIMILARITY
            # --------------------------------------------------

            score = float(
                np.dot(
                    query_normalized,
                    stored_normalized
                )
            )


            if not np.isfinite(
                score
            ):

                continue


            usable_embeddings += 1


            # --------------------------------------------------
            # BEST MATCH
            # --------------------------------------------------

            if score > best_score:

                best_score = score

                best_person_id = person_id


        # ======================================================
        # PYTHON PROCESSING TIME
        # ======================================================

        processing_time_ms = round(
            (
                time.perf_counter() -
                start_time
            ) * 1000,
            2
        )


        # ======================================================
        # NO USABLE EMBEDDINGS
        # ======================================================

        if best_person_id is None:

            return {

                "success": True,

                "matched": False,

                "faces_detected": 1,

                "detection_score":
                    detection_score,

                "registered_faces":
                    len(registered),

                "usable_embeddings":
                    usable_embeddings,

                "similarity_score":
                    None,

                "best_score":
                    None,

                "threshold":
                    MATCH_THRESHOLD,

                "person_id":
                    None,

                "message":
                    "No usable registered face embeddings found.",

                "liveness":
                    liveness,

            "bbox":
                    bbox,

                "model_version":
                    MODEL_VERSION,

                "processing_time_ms":
                    processing_time_ms,

            }


        # ======================================================
        # THRESHOLD CHECK
        # ======================================================

        matched = (
            best_score >=
            MATCH_THRESHOLD
        )


        # ======================================================
        # NOT MATCHED
        # ======================================================

        if not matched:

            return {

                "success": True,

                "matched": False,

                "faces_detected": 1,

                "detection_score":
                    detection_score,

                "similarity_score":
                    best_score,

                "best_score":
                    best_score,

                "threshold":
                    MATCH_THRESHOLD,

                "person_id":
                    None,

                "message":
                    "Face detected but no sufficiently similar registered face was found.",

                "liveness":
                    liveness,

            "bbox":
                    bbox,

                "model_version":
                    MODEL_VERSION,

                "processing_time_ms":
                    processing_time_ms,

            }


        # ======================================================
        # MATCHED
        # ======================================================

        return {

            "success": True,

            "matched": True,

            "faces_detected": 1,

            "detection_score":
                detection_score,

            "person_id":
                best_person_id,

            "similarity_score":
                best_score,

            "best_score":
                best_score,

            "threshold":
                MATCH_THRESHOLD,

            "liveness":
                liveness,

            "bbox":
                bbox,

            "model_version":
                MODEL_VERSION,

            "processing_time_ms":
                processing_time_ms,

        }


    # ==========================================================
    # HTTP EXCEPTION
    # ==========================================================

    except HTTPException:

        raise


    # ==========================================================
    # UNEXPECTED ERROR
    # ==========================================================

    except Exception as e:

        raise HTTPException(
            status_code=500,
            detail=str(e)
        )


# ============================================================
# LIVE FACE ANALYSIS (capture guidance)
# ============================================================
#
# Fast, detection-only check used while the camera is open. It never
# compares against registered faces. It reports where the face is,
# how large, which way the head is turned, brightness, sharpness and
# the passive liveness score so the browser can guide the person and
# Laravel can run the head-turn challenge.
#
MAX_ANALYZE_BYTES = 2 * 1024 * 1024


def face_quality(image, bbox):

    height, width = image.shape[:2]

    x1, y1, x2, y2 = [int(v) for v in bbox[:4]]
    x1, y1 = max(x1, 0), max(y1, 0)
    x2, y2 = min(x2, width), min(y2, height)

    crop = image[y1:y2, x1:x2]

    if crop.size == 0:
        return 0.0, 0.0

    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)

    # Normalise size so sharpness is comparable across distances.
    gray = cv2.resize(gray, (112, 112))

    brightness = float(np.mean(gray))
    sharpness = float(cv2.Laplacian(gray, cv2.CV_64F).var())

    return brightness, sharpness


def head_yaw(kps):
    """
    Horizontal head turn from the 5 landmarks: nose offset from the
    eye midpoint, in eye-distance units. ~0 when facing the camera;
    roughly +/-0.3 or more for a clear turn to either side.
    """

    left_eye, right_eye, nose = kps[0], kps[1], kps[2]

    eye_distance = float(np.linalg.norm(right_eye - left_eye))

    if eye_distance <= 0:
        return 0.0

    eye_mid_x = (left_eye[0] + right_eye[0]) / 2.0

    return float((nose[0] - eye_mid_x) / eye_distance)


@app.post("/face/analyze")
def face_analyze(
    face_image: UploadFile = File(...)
):

    start_time = time.perf_counter()

    contents = face_image.file.read(MAX_ANALYZE_BYTES + 1)

    if len(contents) > MAX_ANALYZE_BYTES:

        raise HTTPException(
            status_code=413,
            detail="Frame is too large."
        )

    image = decode_image(
        contents
    )

    height, width = image.shape[:2]

    bboxes, kpss = face_app.det_model.detect(
        image,
        max_num=0,
        metric="default",
    )

    faces_count = int(bboxes.shape[0])

    result = {
        "success": True,
        "faces_detected": faces_count,
        "frame": {"width": width, "height": height},
        "face": None,
        "liveness": None,
    }

    if faces_count == 0:

        result["processing_time_ms"] = round((time.perf_counter() - start_time) * 1000, 2)

        return result


    # Guide using the largest face.
    areas = (bboxes[:, 2] - bboxes[:, 0]) * (bboxes[:, 3] - bboxes[:, 1])
    index = int(np.argmax(areas))

    bbox = bboxes[index]
    kps = kpss[index]

    brightness, sharpness = face_quality(image, bbox)

    x1, y1, x2, y2 = [float(v) for v in bbox[:4]]

    result["face"] = {
        "detection_score": round(float(bbox[4]), 4),
        "center_x": round(((x1 + x2) / 2) / width, 4),
        "center_y": round(((y1 + y2) / 2) / height, 4),
        "width": round((x2 - x1) / width, 4),
        "height": round((y2 - y1) / height, 4),
        "yaw": round(head_yaw(kps), 4),
        "brightness": round(brightness, 1),
        "sharpness": round(sharpness, 1),
    }

    liveness_model = face_app.addons.get("liveness")

    if liveness_model is not None:

        try:

            prediction = liveness_model.predict(image, kps)

            score = prediction.get("live_score")

            result["liveness"] = {
                "status": prediction.get("status"),
                "is_live": prediction.get("is_live"),
                "live_score": None if score is None else round(float(score), 4),
                "threshold": LIVENESS_THRESHOLD,
            }

        except Exception:

            result["liveness"] = {
                "status": "error",
                "is_live": None,
                "live_score": None,
                "threshold": LIVENESS_THRESHOLD,
            }

    result["processing_time_ms"] = round((time.perf_counter() - start_time) * 1000, 2)

    return result


# ============================================================
# VOICE RECOGNITION SETTINGS
# ============================================================

# Speaker embeddings come from WeSpeaker ResNet34-LM (VoxCeleb),
# run through onnxruntime. PyTorch is not used because Windows
# Smart App Control blocks its DLLs on this machine.
#
# Model file (not in git):
#   python-ai/models/voxceleb_resnet34_LM.onnx
#   https://huggingface.co/Wespeaker/wespeaker-resnet34-LM
#   SHA-256 7bb2f06e9df17cdf1ef14ee8a15ab08ed28e8d0ef5054ee135741560df2ec068
#
VOICE_MODEL_VERSION = "wespeaker-resnet34-lm-v1"

VOICE_MODEL_PATH = Path(
    os.getenv(
        "VOICE_MODEL_PATH",
        str(
            Path(__file__).resolve().parent
            / "models"
            / "voxceleb_resnet34_LM.onnx"
        ),
    )
)

# Cosine threshold for a voice match. Measured scores (5 s clips):
#   same real person, different speech:  0.57
#   different speakers (real vs TTS):    0.15 - 0.25
# 0.45 sits between them with ~0.12 margin each side. Re-check as more
# staff enroll; override with the VOICE_MATCH_THRESHOLD env var.
VOICE_MATCH_THRESHOLD = float(
    os.getenv("VOICE_MATCH_THRESHOLD", "0.45")
)

VOICE_SAMPLE_RATE = 16000
EXPECTED_VOICE_EMBEDDING_SIZE = 256
MAX_AUDIO_BYTES = 10 * 1024 * 1024
MIN_AUDIO_DURATION = 2.0
MAX_AUDIO_DURATION = 30.0
MIN_AUDIO_RMS = 0.005

voice_session = None


def get_voice_session():

    global voice_session

    if voice_session is None:

        if not VOICE_MODEL_PATH.is_file():

            raise HTTPException(
                status_code=503,
                detail=(
                    "Voice model file is missing. Expected "
                    f"{VOICE_MODEL_PATH}."
                )
            )

        voice_session = ort.InferenceSession(
            str(VOICE_MODEL_PATH),
            providers=["CPUExecutionProvider"],
        )

    return voice_session


# ============================================================
# FFMPEG
# ============================================================

def get_ffmpeg_path():

    configured_path = os.getenv("FFMPEG_PATH")

    if configured_path and os.path.isfile(configured_path):

        return configured_path


    detected_path = shutil.which("ffmpeg")

    if detected_path:

        return detected_path


    fallback = os.path.join(
        os.getenv("LOCALAPPDATA", ""),
        "Microsoft",
        "WinGet",
        "Packages",
        "Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe",
        "ffmpeg-9.0.1-essentials_build",
        "bin",
        "ffmpeg.exe",
    )

    if os.path.isfile(fallback):

        return fallback


    raise HTTPException(
        status_code=503,
        detail="FFmpeg was not found. Set the FFMPEG_PATH environment variable."
    )


# ============================================================
# AUDIO DECODER
# ============================================================

def decode_audio(contents: bytes):
    """
    Decode browser audio (webm/ogg/wav/mp3/m4a) to mono 16 kHz
    float32 samples in [-1, 1] and run basic quality checks.
    """

    if not contents:

        raise HTTPException(
            status_code=422,
            detail="Empty audio file."
        )


    if len(contents) > MAX_AUDIO_BYTES:

        raise HTTPException(
            status_code=413,
            detail="Audio file exceeds the 10 MB size limit."
        )


    input_path = None

    try:

        # webm from MediaRecorder is not reliably seekable from a
        # pipe, so ffmpeg reads it from a temporary file.
        with tempfile.NamedTemporaryFile(
            suffix=".audio",
            delete=False
        ) as input_file:

            input_file.write(contents)
            input_path = input_file.name


        try:

            result = subprocess.run(
                [
                    get_ffmpeg_path(),
                    "-nostdin",
                    "-hide_banner",
                    "-loglevel", "error",
                    "-i", input_path,
                    "-vn",
                    "-ac", "1",
                    "-ar", str(VOICE_SAMPLE_RATE),
                    "-f", "s16le",
                    "-",
                ],
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                timeout=45,
                check=False,
            )

        except subprocess.TimeoutExpired:

            raise HTTPException(
                status_code=422,
                detail="Audio conversion timed out."
            )


        if result.returncode != 0 or not result.stdout:

            raise HTTPException(
                status_code=422,
                detail="Unable to decode the audio file. Try recording it again."
            )

    finally:

        if input_path and os.path.exists(input_path):

            try:
                os.remove(input_path)
            except OSError:
                pass


    samples = (
        np.frombuffer(result.stdout, dtype=np.int16)
        .astype(np.float32)
        / 32768.0
    )

    duration = samples.shape[0] / float(VOICE_SAMPLE_RATE)


    if duration < MIN_AUDIO_DURATION:

        raise HTTPException(
            status_code=422,
            detail=(
                f"Audio is too short. Record at least "
                f"{MIN_AUDIO_DURATION:.0f} seconds."
            )
        )


    if duration > MAX_AUDIO_DURATION:

        raise HTTPException(
            status_code=422,
            detail=(
                f"Audio is too long. Maximum duration is "
                f"{MAX_AUDIO_DURATION:.0f} seconds."
            )
        )


    rms = float(np.sqrt(np.mean(samples ** 2)))

    if rms < MIN_AUDIO_RMS:

        raise HTTPException(
            status_code=422,
            detail="Audio is too quiet. Speak closer to the microphone and try again."
        )


    return samples, duration


# ============================================================
# KALDI-COMPATIBLE FILTERBANK FEATURES
# ============================================================
#
# Matches the WeSpeaker front end:
#   kaldi.fbank(wav * 32768, num_mel_bins=80, frame_length=25,
#               frame_shift=10, dither=0, window_type="hamming")
#   followed by per-utterance mean normalisation.
#

FBANK_FRAME_LENGTH = 400      # 25 ms at 16 kHz
FBANK_FRAME_SHIFT = 160       # 10 ms at 16 kHz
FBANK_FFT_SIZE = 512
FBANK_NUM_BINS = 80
FBANK_LOW_FREQ = 20.0
FBANK_PREEMPHASIS = 0.97


def _mel(freq):

    return 1127.0 * np.log(1.0 + freq / 700.0)


def _mel_filterbank():

    num_fft_bins = FBANK_FFT_SIZE // 2
    bin_width = VOICE_SAMPLE_RATE / FBANK_FFT_SIZE

    mel_low = _mel(FBANK_LOW_FREQ)
    mel_high = _mel(VOICE_SAMPLE_RATE / 2.0)
    mel_delta = (mel_high - mel_low) / (FBANK_NUM_BINS + 1)

    fft_mels = _mel(np.arange(num_fft_bins) * bin_width)

    left = mel_low + np.arange(FBANK_NUM_BINS)[:, None] * mel_delta
    center = left + mel_delta
    right = center + mel_delta

    up = (fft_mels - left) / (center - left)
    down = (right - fft_mels) / (right - center)

    weights = np.where(
        (fft_mels > left) & (fft_mels < right),
        np.where(fft_mels <= center, up, down),
        0.0,
    )

    # Kaldi ignores the Nyquist bin; append a zero column for it.
    return np.pad(weights, ((0, 0), (0, 1))).astype(np.float32)


MEL_FILTERBANK = _mel_filterbank()

HAMMING_WINDOW = (
    0.54 - 0.46 * np.cos(
        2 * np.pi * np.arange(FBANK_FRAME_LENGTH)
        / (FBANK_FRAME_LENGTH - 1)
    )
).astype(np.float32)


def compute_fbank(samples):

    waveform = samples * 32768.0

    num_frames = 1 + (
        (waveform.shape[0] - FBANK_FRAME_LENGTH)
        // FBANK_FRAME_SHIFT
    )

    indices = (
        np.arange(FBANK_FRAME_LENGTH)[None, :]
        + FBANK_FRAME_SHIFT * np.arange(num_frames)[:, None]
    )

    frames = waveform[indices].astype(np.float32)

    # Remove DC offset.
    frames = frames - frames.mean(axis=1, keepdims=True)

    # Pre-emphasis (Kaldi applies it to the first sample too).
    previous = np.concatenate(
        [frames[:, :1], frames[:, :-1]],
        axis=1
    )
    frames = frames - FBANK_PREEMPHASIS * previous

    frames = frames * HAMMING_WINDOW

    spectrum = np.fft.rfft(frames, n=FBANK_FFT_SIZE, axis=1)
    power = (spectrum.real ** 2 + spectrum.imag ** 2).astype(np.float32)

    mel_energies = power @ MEL_FILTERBANK.T

    feats = np.log(
        np.maximum(mel_energies, np.finfo(np.float32).eps)
    )

    # Cepstral mean normalisation over the utterance.
    return (feats - feats.mean(axis=0, keepdims=True)).astype(np.float32)


# ============================================================
# VOICE EMBEDDING
# ============================================================

def generate_voice_embedding(samples):

    session = get_voice_session()

    feats = compute_fbank(samples)[None, :, :]

    embedding = session.run(
        None,
        {session.get_inputs()[0].name: feats}
    )[0][0]

    embedding = np.asarray(embedding, dtype=np.float32).reshape(-1)


    if embedding.shape[0] != EXPECTED_VOICE_EMBEDDING_SIZE:

        raise HTTPException(
            status_code=500,
            detail="Unexpected voice embedding size."
        )


    norm = float(np.linalg.norm(embedding))

    if not np.all(np.isfinite(embedding)) or norm == 0:

        raise HTTPException(
            status_code=422,
            detail="Unable to generate a valid voice embedding."
        )


    return embedding / norm


@app.post("/voice/embedding")
def voice_embedding(
    voice_audio: UploadFile = File(...)
):

    start_time = time.perf_counter()


    try:

        contents = voice_audio.file.read(MAX_AUDIO_BYTES + 1)

        samples, duration = decode_audio(contents)

        embedding = generate_voice_embedding(samples)


        return {

            "success": True,

            "embedding":
                embedding.tolist(),

            "embedding_length":
                int(embedding.shape[0]),

            "duration_seconds":
                round(duration, 2),

            "sample_rate":
                VOICE_SAMPLE_RATE,

            "model_version":
                VOICE_MODEL_VERSION,

            "processing_time_ms":
                round((time.perf_counter() - start_time) * 1000, 2),

        }


    except HTTPException:

        raise


    except Exception as e:

        raise HTTPException(
            status_code=500,
            detail=str(e)
        )


# ============================================================
# VOICE RECOGNITION
# ============================================================

@app.post("/voice/recognize")
def voice_recognize(

    voice_audio: UploadFile = File(...),

    registered_voices: str = Form(...)

):

    start_time = time.perf_counter()


    try:

        try:

            registered = json.loads(registered_voices)

        except Exception:

            raise HTTPException(
                status_code=422,
                detail="Invalid registered voice data."
            )


        if not isinstance(registered, list):

            raise HTTPException(
                status_code=422,
                detail="Registered voices must be a list."
            )


        contents = voice_audio.file.read(MAX_AUDIO_BYTES + 1)

        samples, duration = decode_audio(contents)

        query_embedding = generate_voice_embedding(samples)


        best_score = -1.0

        best_person_id = None

        usable_embeddings = 0


        for registered_voice in registered:

            if not isinstance(registered_voice, dict):

                continue


            person_id = registered_voice.get("person_id")

            embedding = registered_voice.get("embedding")


            if not person_id or not isinstance(embedding, list):

                continue


            if len(embedding) != EXPECTED_VOICE_EMBEDDING_SIZE:

                continue


            try:

                stored_embedding = np.asarray(embedding, dtype=np.float32)

            except Exception:

                continue


            stored_norm = float(np.linalg.norm(stored_embedding))

            if not np.all(np.isfinite(stored_embedding)) or stored_norm == 0:

                continue


            score = float(
                np.dot(query_embedding, stored_embedding / stored_norm)
            )

            usable_embeddings += 1


            if score > best_score:

                best_score = score

                best_person_id = person_id


        processing_time_ms = round(
            (time.perf_counter() - start_time) * 1000,
            2
        )


        if best_person_id is None:

            return {

                "success": True,

                "matched": False,

                "person_id": None,

                "similarity_score": None,

                "threshold": VOICE_MATCH_THRESHOLD,

                "registered_voices": len(registered),

                "usable_embeddings": usable_embeddings,

                "message":
                    "No usable registered voice embeddings found. "
                    "Voice samples may need to be re-recorded.",

                "model_version": VOICE_MODEL_VERSION,

                "processing_time_ms": processing_time_ms,

            }


        matched = best_score >= VOICE_MATCH_THRESHOLD


        return {

            "success": True,

            "matched": matched,

            "person_id": best_person_id if matched else None,

            "similarity_score": best_score,

            "threshold": VOICE_MATCH_THRESHOLD,

            "usable_embeddings": usable_embeddings,

            "duration_seconds": round(duration, 2),

            "message": (
                "Voice matched."
                if matched
                else "Voice detected but no sufficiently similar registered voice was found."
            ),

            "model_version": VOICE_MODEL_VERSION,

            "processing_time_ms": processing_time_ms,

        }


    except HTTPException:

        raise


    except Exception as e:

        raise HTTPException(
            status_code=500,
            detail=str(e)
        )
