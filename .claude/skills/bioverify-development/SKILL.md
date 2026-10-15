\---

name: bioverify-development

description: Develop, debug, test, and maintain the BioVerify AI Laravel application, Python biometric services, facial and voice recognition, Ask Pong AI, and RAG knowledge base. Use when working on this project's code or troubleshooting its features.

\---



\# BioVerify AI Development Skill



\## Project Environment



\- Root: `C:\\xampp\\htdocs\\bioverify-ai`

\- Backend: Laravel and PHP

\- Database: MySQL/MariaDB, database `bioverify\_ai`

\- Frontend: Blade, Tailwind CSS, JavaScript

\- AI backend: Python and FastAPI

\- Facial recognition: InsightFace `buffalo\_l`

\- Voice recognition: Python audio processing and speaker embeddings

\- RAG: ChromaDB and the existing `rag-ai` directory

\- Knowledge base: `knowledge-base`

\- Assistant: Ask Pong AI with microphone-based speech input

\- Python environment: `python-ai\\.venv`



\## Mandatory Workflow



1\. Inspect relevant existing files before modifying code.

2\. Understand current routes, database relationships, APIs, and frontend interactions.

3\. Identify the root cause of errors before implementing a fix.

4\. Preserve working features and avoid unnecessary refactoring.

5\. Provide complete code for requested files and their exact paths.

6\. Include required PowerShell commands and practical verification steps.

7\. State assumptions and distinguish tested functionality from untested code.



\## Laravel and Database



\- Follow Laravel conventions.

\- Validate requests and enforce authentication and authorization.

\- Preserve existing tables, columns, relationships, and route names.

\- Inspect migration history before proposing schema changes.

\- Never reset or drop tables as a routine troubleshooting step.

\- Explain potentially destructive operations and request approval before performing them.

\- Handle file uploads securely and use appropriate Laravel storage conventions.



\## Facial Recognition



\- Preserve compatibility with the existing InsightFace pipeline.

\- Ensure registration and verification use consistent image preprocessing and embedding generation.

\- Handle missing faces, multiple faces, poor-quality images, and invalid input.

\- Evaluate similarity thresholds using genuine matches and non-matching samples.

\- Do not claim identity verification is reliable without adequate testing.



\## Voice Recognition



\- Preserve existing recording, preview, playback, and save functionality.

\- Keep audio preprocessing consistent between enrollment and verification.

\- Check sample rate, format, duration, silence, and recording quality.

\- Distinguish speaker verification from speech-to-text transcription.

\- Never remove working microphone features to fix unrelated JavaScript problems.



\## Ask Pong AI and RAG



\- Preserve floating chat controls and microphone-based speech input.

\- Prevent duplicate event listeners and conflicting JavaScript handlers.

\- Handle loading states, API errors, and failed responses.

\- Preserve existing PDF ingestion, retrieval, and ChromaDB functionality.

\- Inspect existing integrations before changing endpoints or response formats.



\## Python and FastAPI



\- Use the existing `python-ai\\.venv` environment.

\- Verify dependencies and installed package versions before upgrading anything.

\- Check API schemas, timeouts, service URLs, port conflicts, and error responses.

\- Keep secrets in environment configuration rather than frontend code.

\- Provide Windows-compatible setup, startup, and testing commands.



\## Security



\- Protect biometric images, audio recordings, embeddings, and authentication sessions.

\- Apply authorization, rate limiting, secure uploads, and appropriate data-retention policies.

\- Consider liveness detection and presentation-attack prevention.

\- Never log passwords, API secrets, or unnecessary biometric data.

\- Treat biometric similarity as probabilistic, not absolute proof of identity.



\## User Interface



\- Maintain the existing professional blue, white, and slate design.

\- Use the existing Tailwind CSS and Blade conventions.

\- Preserve responsive layouts, navigation, sidebars, and floating AI controls.

\- Ensure visual changes do not break forms, buttons, recording, or API requests.

\- Provide complete updated views when requested.



\## Debugging and Testing



For every issue:



1\. Explain the likely cause based on available evidence.

2\. Identify the affected files and dependencies.

3\. Provide the smallest safe correction.

4\. Supply complete code or a clear patch.

5\. Give commands to reproduce and verify the result.

6\. Explain expected output and next diagnostic steps if the problem persists.



Use Laravel tests, route checks, browser network inspection, FastAPI health checks, and biometric match/non-match tests where appropriate.



Never claim to have executed commands, inspected inaccessible files, or passed tests without evidence.



\## Response Style



Use clear, practical English. Give direct solutions, exact file paths, complete code when requested, and step-by-step Windows instructions.



Prioritize working implementations over mockups and placeholders.



\## Final Rule



Treat BioVerify AI as an existing project. Understand before changing, preserve working features, fix root causes, protect biometric data, and verify each implementation before moving to the next task.

