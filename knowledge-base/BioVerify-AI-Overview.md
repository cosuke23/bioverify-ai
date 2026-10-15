
---
title: BioVerify AI and MEPCOM Knowledge Overview
aliases:
  - BioVerify AI Project Documentation
  - MEPCOM AI Knowledge Base
  - Philippine Coast Guard MEPCOM Reference
tags:
  - bioverify-ai
  - project
  - philippine-coast-guard
  - mepcom
  - marine-environmental-protection
  - hrmis
  - artificial-intelligence
  - rag
  - obsidian
status: active
created: 2026-10-06
updated: 2026-10-13
---

# BioVerify AI and MEPCOM Knowledge Overview

## 1. Purpose of This Knowledge Base

This knowledge base documents the BioVerify AI software project and provides institutional context about the Philippine Coast Guard (PCG), particularly the Marine Environmental Protection Command (MEPCOM).

It is intended to help developers, authorized users, and the local AI assistant understand:

- The purpose and architecture of BioVerify AI.
- The relationship between Laravel, Python, biometric recognition, and RAG.
- The responsibilities and operational context of MEPCOM.
- Marine environmental protection terminology and activities.
- Personnel management, organizational assignments, training, and HR analytics relevant to the user's MEPCOM HRMIS project.
- How to retrieve reliable information from Obsidian using ChromaDB and a local language model.
- Which statements are verified facts, project-specific information, or proposed future features.

The knowledge base is a technical and educational reference. It is not a substitute for official PCG directives, approved doctrines, applicable laws, command decisions, or authorized operational procedures.

## 2. Project Identity

### 2.1 BioVerify AI

BioVerify AI is a local-first Laravel application that combines biometric identity verification with an AI assistant and a searchable knowledge base.

Its technical goals include:

1. Manage person records.
2. Register facial images and facial embeddings.
3. Register voice recordings and speaker embeddings.
4. Support facial and voice recognition workflows.
5. Maintain recognition history.
6. Provide an AI chat interface called Pong AI.
7. Retrieve relevant technical and institutional information from Markdown documentation.
8. Run language-model inference locally where practical.

### 2.2 MEPCOM institutional context

The Marine Environmental Protection Command is a command of the Philippine Coast Guard responsible for marine environmental protection functions.

MEPCOM's work includes pollution prevention, enforcement of applicable marine pollution regulations, oil spill response, environmental investigation and monitoring, contingency planning, technical support, training, and coordination with relevant stakeholders.

Official website:
https://mepcom.coastguard.gov.ph/

Official functions page:
https://mepcom.coastguard.gov.ph/transparency-seal/about-us/functions/

### 2.3 Relationship between the projects

BioVerify AI is a software development project. The MEPCOM HRMIS is a personnel-management and analytics project associated with the user's development work.

Do not assume that BioVerify AI is an officially deployed PCG or MEPCOM system, that it has access to official personnel records, or that the two projects share a production database.

Any future integration must be explicitly authorized, designed, tested, and documented.

## 3. Philippine Coast Guard Context

### 3.1 The Philippine Coast Guard

The Philippine Coast Guard is a maritime service responsible for functions established under Philippine law, including maritime safety, maritime security, search and rescue, maritime law enforcement, and marine environmental protection.

MEPCOM's work forms part of the broader maritime responsibilities of the PCG.

Relevant official reference:
https://mepcom.coastguard.gov.ph/

### 3.2 Marine Environmental Protection Command

**MEPCOM** stands for Marine Environmental Protection Command. The official command website uses the term "Marine Environmental Protection Command."

The Command's general institutional purpose is to protect the marine environment, prevent and address marine pollution, support compliance with relevant regulations, and contribute to the preservation of Philippine marine and coastal resources.

MEPCOM activities may involve:

- Pollution prevention and regulatory enforcement.
- Marine pollution incident response.
- Oil and hazardous and noxious substances (HNS) spill preparedness.
- Inspections and environmental compliance assessments.
- Investigation of pollution incidents.
- Marine water quality assessment and environmental monitoring.
- Laboratory analysis and research.
- Oil spill response planning and exercises.
- Training, public awareness, and technical assistance.
- Coordination with government agencies, local government units, industry, and other stakeholders.

The exact authority, scope, and procedure for a particular activity must be checked against applicable laws, official PCG issuances, current doctrines, and approved plans.

## 4. MEPCOM Functions and Areas of Work

The official MEPCOM functions page is the primary starting point for understanding the Command's stated responsibilities.

Source:
https://mepcom.coastguard.gov.ph/transparency-seal/about-us/functions/

### 4.1 Marine pollution prevention and enforcement

MEPCOM performs marine environmental protection enforcement functions, including enforcement of applicable PCG-promulgated rules and regulations concerning marine pollution and relevant MARPOL requirements.

Activities may include:

- Inspections and surveillance.
- Identifying potential pollution violations.
- Documenting observed environmental deficiencies.
- Supporting investigations and enforcement actions.
- Assessing compliance with applicable pollution prevention requirements.
- Providing technical assistance to other Coast Guard units.

An AI assistant must not independently declare a vessel, company, or person guilty of an offense. It may summarize documented findings or explain relevant requirements, but official determinations belong to authorized personnel and processes.

### 4.2 Oil spill and HNS response

MEPCOM has responsibilities concerning the abatement, containment, and recovery of oil spills, HNS, and other marine pollutants.

Important concepts:

- **Oil spill:** An unintended or unlawful release of oil into the environment.
- **HNS:** Hazardous and noxious substances that can pose environmental or safety risks.
- **Containment:** Measures intended to limit the spread of a pollutant.
- **Recovery:** Collection or removal of spilled material using suitable methods.
- **Cleanup:** Actions taken to remove pollutants and address affected areas.
- **Incident documentation:** Records of observations, response actions, resources, timelines, and outcomes.

A response depends on the type of substance, incident conditions, environmental sensitivity, available resources, and applicable plans.

Pong AI can explain these concepts and help organize documentation. It must not replace the designated incident commander or provide invented operational orders.

### 4.3 Contingency planning

MEPCOM prepares or supports the preparation of oil spill and HNS spill contingency plans and evaluates relevant preparedness arrangements within its authority.

Plans may establish:

- Responsibilities and coordination arrangements.
- Incident reporting procedures.
- Response organization.
- Resource and equipment requirements.
- Notification and escalation procedures.
- Response and recovery considerations.
- Training and exercise requirements.
- Documentation and post-incident review.

Use the current approved contingency plan for any real incident. A general AI-generated explanation is not an approved response plan.

Reference:
https://mepcom.coastguard.gov.ph/

### 4.4 MARPOL and pollution prevention

MARPOL is the international convention addressing pollution from ships.

It is relevant to marine environmental protection because it establishes requirements concerning pollution from ships, subject to applicable provisions, amendments, implementation, and national enforcement arrangements.

When explaining MARPOL, Pong AI should:

1. Identify the applicable topic or annex when known.
2. Distinguish international requirements from Philippine implementing rules.
3. Verify current provisions before giving compliance advice.
4. Avoid inventing penalties, exemptions, deadlines, or inspection requirements.
5. Cite official or authoritative sources when available.

### 4.5 Environmental monitoring and laboratory work

MEPCOM's stated functions include laboratory operations, analysis of marine pollutants, research, environmental monitoring, and assessment of marine water quality.

These activities may support:

- Pollution identification.
- Incident investigation.
- Environmental baseline development.
- Assessment of affected areas.
- Research on pollution prevention and response methods.
- Technical reporting and evidence-based decision-making.

Scientific results must be interpreted according to the actual sampling method, laboratory procedure, analytical results, and relevant technical standards.

### 4.6 Training and environmental awareness

MEPCOM conducts or supports training, lectures, drills, exercises, and public information activities related to marine environmental protection.

Potential subject areas include:

- Marine pollution prevention.
- Oil spill response awareness.
- HNS incident preparedness.
- Environmental compliance.
- Pollution reporting and documentation.
- Operational readiness.
- Coordination between relevant agencies and stakeholders.

Training requirements, course names, qualification rules, and completion criteria must be sourced from current official records or approved instructions.

### 4.7 Inter-agency coordination

Marine environmental incidents can require coordination among Coast Guard units, government agencies, local government units, industry, laboratories, private responders, and other relevant organizations.

The responsible parties depend on the incident, jurisdiction, applicable plans, and authorized command arrangements.

Pong AI may help explain documented roles and prepare draft correspondence or reports, but it must not represent a draft as an issued order or an approved official communication.

## 5. MEPCOM Organizational Knowledge

### 5.1 Understanding organizational structure

Pong AI must understand the difference between:

- The Philippine Coast Guard as an organization.
- MEPCOM as a command.
- Staff offices and functional services.
- Operational or specialized units.
- Regional or local assignments.
- Individual personnel and their positions.
- Temporary assignments and permanent organizational appointments.

A person's rank, position, assigned unit, and command relationship are different data concepts.

For example, a person's rank does not necessarily identify their current staff position or unit assignment.

### 5.2 Unit names and organizational changes

Names and arrangements of units may change through official orders and organizational reforms.

The user's previous MEPCOM-related software context includes the following names:

- MEPG
- MEPLEF
- NOCOP
- MSFIS
- MEPRCGS
- MEPTDI

These names are recorded here as organizational terms encountered in the project context, not as a guaranteed current or complete organizational chart.

MEPCOM publicly reported the activation of the Marine Environmental Protection Regulatory Compliance and Governance Service (MEPRCGS) in May 2026, announced in July 2026.

Reference:
https://mepcom.coastguard.gov.ph/

Before creating an official unit dropdown, assigning personnel, generating an organizational chart, or interpreting an acronym, verify the current approved organization and terminology.

### 5.3 Organizational hierarchy in software

A personnel system may need to represent relationships such as:

```text
Philippine Coast Guard
└── Marine Environmental Protection Command
    ├── Staff offices and functional services
    ├── Specialized units
    ├── Operational or response elements
    └── Personnel assignments
```

This is a conceptual illustration, not an authoritative current organizational chart.

The actual hierarchy should be driven by approved organizational data, not by a hard-coded assumption in an AI prompt.

## 6. MEPCOM HRMIS Context

### 6.1 Purpose

The MEPCOM Human Resource Management Information System (HRMIS) is a software project intended to support personnel information management and administrative workflows.

The related HR analytics work focuses on personnel distribution, service information, unit assignments, ranks, academic and training records, and management reporting.

This software context is separate from the official operational responsibilities of MEPCOM.

### 6.2 Personnel information

A personnel system may need to maintain or reference:

- Personnel identifiers.
- Name and basic information.
- Rank or rate.
- Service details.
- Organizational assignment.
- Position or duty assignment.
- Academic and training records.
- Qualification and course information.
- Application or administrative status.
- Relevant approval history.

Only fields actually implemented and authorized in the system should be treated as part of its live schema.

### 6.3 Registration workflow

The MEPCOM HRMIS development context includes a multi-step personnel registration process:

1. Basic Information.
2. Service Details.
3. Unit Assignment.
4. Security and Verification.

Each step should validate its required fields, preserve previously entered data, and prevent unauthorized access to personal information.

The workflow describes the software design, not necessarily an official PCG administrative procedure.

### 6.4 Unit assignment

The HRMIS design includes a hierarchical unit-selection concept:

Command → Category → Unit → Sub Unit

The dropdown values should come from the current authorized organizational reference data.

Changing a unit should update dependent selections. The application should prevent invalid combinations, such as assigning a sub-unit that does not belong to the selected parent unit.

### 6.5 Rank and rate classification

The user's HRMIS development context includes officer ranks from Ensign through Admiral and enlisted rates from ASN through FMCPO.

The complete list, naming conventions, order, and applicability must be verified against current official PCG personnel references before being used in production.

Do not treat a sample dropdown or a previous implementation as proof of an authoritative rank structure.

### 6.6 HR analytics

Potential analytical dimensions include:

- Personnel counts by unit.
- Officer and non-officer distribution.
- Rank or rate distribution.
- Personnel status.
- Academic qualifications.
- Training and course participation.
- Fiscal-year trends.
- Unit staffing distribution.
- Administrative application outcomes.

Analytics should distinguish actual database values from derived classifications.

Charts should include a clear reporting period, appropriate filters, and definitions for the values displayed.

### 6.7 Administrative decisions

An HRMIS may support review, approval, rejection, and status tracking for administrative workflows.

The system should record who performed an action, when it occurred, and any required justification or audit metadata.

Pong AI may explain workflow logic or summarize authorized data. It must not approve, reject, or change a real personnel record unless an explicitly authorized workflow permits the action and the user has the necessary permissions.

## 7. BioVerify AI Technical Architecture

### 7.1 Technology stack

| Component | Technology | Responsibility |
|---|---|---|
| Web application | Laravel | Routing, authentication, validation, and business logic |
| Application language | PHP | Server-side logic |
| Relational database | MySQL/MariaDB | People, biometric records, and logs |
| UI templates | Blade | Server-rendered pages |
| Styling | Tailwind CSS | User interface styling |
| Asset pipeline | Vite | Frontend asset processing |
| Biometric service | Python and FastAPI | Biometric processing endpoints |
| Face recognition | InsightFace `buffalo_l` | Facial detection and embeddings |
| Local LLM runtime | Ollama | Local language-model inference |
| Language model | `llama3.2:1b` | Local response generation |
| Text embedding model | `nomic-embed-text` | Semantic document embeddings |
| Vector store | ChromaDB | Vector retrieval |
| Knowledge management | Obsidian | Markdown documentation |
| Agent framework | Hermes Agent | Possible future agent workflows |

### 7.2 Project directories

Project root:

`C:\xampp\htdocs\bioverify-ai`

```text
bioverify-ai/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── knowledge-base/
│   ├── BioVerify AI Overview.md
│   ├── MEPCOM Institutional Reference.md
│   ├── MEPCOM HRMIS.md
│   ├── Architecture.md
│   ├── Database Schema.md
│   ├── Face Recognition.md
│   ├── Voice Recognition.md
│   ├── Pong AI.md
│   ├── RAG Pipeline.md
│   ├── Security and Privacy.md
│   └── Troubleshooting.md
├── rag-ai/
│   ├── scripts/
│   ├── chroma_db/
│   └── requirements.txt
├── python-ai/
│   ├── .venv/
│   └── main.py
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
└── storage/
```

This is a recommended documentation layout. Verify the actual repository before assuming every example file or directory exists.

### 7.3 Laravel responsibilities

Laravel is responsible for:

- Serving application pages.
- Validating user input.
- Enforcing authentication and authorization.
- Managing people and biometric records.
- Calling the Python AI service.
- Recording recognition events.
- Serving the Pong AI chat interface.
- Integrating document retrieval and local model responses.

### 7.4 Python service responsibilities

The Python service handles biometric operations exposed through its API.

The previously developed service uses InsightFace `buffalo_l` and CPU execution for facial recognition.

The actual endpoints, request fields, response schema, and model-loading behavior must be confirmed in `python-ai/main.py`.

### 7.5 Database context

The Laravel application uses the `bioverify_ai` database.

Known application tables include:

- `people`
- `person_faces`
- `person_voices`
- `recognition_logs`

`people` stores person records.

`person_faces` stores face sample metadata, image paths, and face embeddings.

`person_voices` stores voice sample metadata, audio paths, and intended speaker embedding data.

`recognition_logs` stores biometric recognition activity.

The exact columns, indexes, constraints, and relationships must be verified against the current migrations.

### 7.6 Face recognition

The face recognition workflow includes:

1. Receive an image.
2. Decode the image.
3. Detect the face.
4. Extract an embedding.
5. Compare it with registered embeddings.
6. Calculate similarity.
7. Apply the configured threshold.
8. Return the result to Laravel.

The current development workflow uses 512-dimensional face embeddings.

A similarity score is not automatically a calibrated probability of identity. Recognition thresholds must be evaluated using representative samples and false acceptance and rejection tests.

### 7.7 Voice recognition

The voice recognition objective is to compare a submitted voice sample with registered speaker embeddings.

The intended workflow includes audio validation, preprocessing, embedding extraction, comparison, threshold evaluation, and result logging.

The application has developed voice recording and playback features. Recording functionality should not be confused with a completed speaker-recognition pipeline.

Verify the actual voice model and endpoint before describing the feature as fully operational.

## 8. Pong AI Assistant

### 8.1 Purpose

Pong AI is the conversational assistant for the BioVerify AI project and its indexed knowledge base.

Its intended responsibilities include:

- Explaining the application architecture.
- Helping troubleshoot Laravel, PHP, Python, and database errors.
- Explaining biometric workflows and their limitations.
- Retrieving relevant project documentation.
- Explaining MEPCOM institutional context using approved reference material.
- Supporting documentation and development tasks.
- Helping interpret HRMIS software design when the relevant project notes are indexed.

### 8.2 Local models

The planned or configured local models are:

- Ollama `llama3.2:1b` for language generation.
- Ollama `nomic-embed-text` for text embeddings.

Confirm that both models are installed and available in the active Ollama environment before assuming they are operational.

### 8.3 Chat interfaces

The Laravel application includes a dedicated AI chat page and a floating chat interface.

The floating chat is designed to use a compact panel opened from a button at the bottom-right of the screen.

The chat interface sends a message to the Laravel AI chat endpoint, receives a response, and displays it to the user.

The actual response contract must match the controller implementation.

### 8.4 How Pong AI should answer MEPCOM questions

When asked about MEPCOM, Pong AI should:

1. Identify the subject: mission, pollution response, MARPOL, personnel administration, training, organization, or software.
2. Search the relevant Obsidian notes.
3. Prefer official PCG and MEPCOM sources for institutional facts.
4. Distinguish current official information from historical or project-specific information.
5. Explain technical terms in clear language.
6. Cite sources when available.
7. State when a fact requires verification.
8. Avoid inventing official policies, organizational structures, or procedures.
9. Avoid exposing confidential personnel or biometric information.
10. Separate a proposed software feature from an existing implementation.

## 9. Retrieval-Augmented Generation (RAG)

### 9.1 Purpose

Retrieval-Augmented Generation supplies relevant documentation to the language model before it generates a response.

This is particularly important for a small local model because the model may not reliably know detailed project-specific facts or current institutional information without supporting context.

### 9.2 Source documents

Potential RAG source material includes:

- BioVerify AI documentation.
- MEPCOM institutional references.
- MEPCOM HRMIS project notes.
- Database schema documentation.
- API references.
- Biometric implementation notes.
- Troubleshooting notes.
- Approved public institutional documents.
- Authorized internal documents, subject to access restrictions.

### 9.3 Indexing workflow

1. Read approved Markdown files.
2. Extract headings and document text.
3. Split text into suitable chunks.
4. Generate embeddings using the configured embedding model.
5. Store vectors, text chunks, and metadata in ChromaDB.
6. Make the indexed collection available for retrieval.

### 9.4 Retrieval workflow

1. Receive a question.
2. Embed the question.
3. Retrieve semantically relevant chunks.
4. Filter or rank results when configured.
5. Construct a prompt with the retrieved context.
6. Generate a response using the local language model.
7. Return the answer and source references where implemented.

### 9.5 Recommended chunk metadata

Store metadata such as:

- `source`: Relative document path.
- `title`: Document title.
- `section`: Heading name.
- `chunk_index`: Chunk order.
- `document_type`: Technical, institutional, policy, or project reference.
- `source_authority`: Official, project documentation, or secondary reference.
- `updated_at`: Last documented update.
- `classification`: Approved public or authorized restricted category.

Metadata should be based on real source properties, not guessed by the model.

### 9.6 Knowledge freshness

Official organizational information can change. Re-index updated documents and remove stale chunks when source files are renamed, deleted, or replaced.

For current PCG organization, leadership, policies, or procedures, verify the latest authoritative source instead of relying solely on old indexed content.

## 10. Source Authority and Evidence Rules

Pong AI must distinguish between different kinds of knowledge.

### Level 1: Official authority

Examples:

- Current laws and regulations.
- Official PCG issuances.
- Approved contingency plans and doctrines.
- Official MEPCOM organizational information.
- Official personnel rules and authorized reference lists.

Use these for claims about legal authority, official requirements, organizational structure, and formal procedures.

### Level 2: Official public information

Examples:

- Official MEPCOM website pages.
- Official PCG announcements.
- Published public information materials.
- Official reports and public notices.

These can establish what the institution publicly reports, but an announcement does not necessarily replace a formal order or policy.

### Level 3: Project implementation records

Examples:

- Laravel source code.
- Database migrations.
- Route definitions.
- Controller logic.
- Configuration files with secrets removed.
- HRMIS design notes.

These establish how the software is designed or implemented, not what official PCG policy requires.

### Level 4: Working notes and proposals

Examples:

- Draft workflows.
- Proposed features.
- Personal development notes.
- AI-generated drafts.
- Unverified organizational assumptions.

Use these as working context, not as authoritative evidence.

### Conflict rule

If project notes conflict with current official sources, identify the conflict and verify the applicable authority. Do not silently present the older or less authoritative statement as current fact.

## 11. Privacy and Security

### 11.1 Biometric data

Facial images, voice recordings, biometric embeddings, and associated identity records require appropriate safeguards.

- Do not index raw biometric samples in the general Obsidian knowledge base.
- Do not place passwords, access tokens, or application secrets in Markdown files.
- Restrict access to biometric files and database records.
- Apply authorization checks to sensitive operations.
- Minimize personal data in logs.
- Define retention and deletion procedures.
- Evaluate recognition accuracy and spoofing risks.
- Avoid presenting biometric matches as infallible identity proof.

### 11.2 Personnel data

Personnel records can contain confidential or sensitive information.

- Index only documents approved for the intended audience.
- Exclude personal records from general-purpose RAG collections unless an authorized, access-controlled design explicitly permits them.
- Do not disclose personnel details in a response to an unauthorized user.
- Apply access restrictions at retrieval time, not just in the chat interface.
- Keep audit trails for sensitive data access where required.

### 11.3 AI boundaries

Pong AI is an assistant, not an official decision-making authority.

It may summarize, explain, search, and draft. It must not independently issue orders, make formal disciplinary findings, approve personnel actions, or declare legal violations.

## 12. Development Environment

### Laravel project

`C:\xampp\htdocs\bioverify-ai`

Start the development server:

```powershell
cd C:\xampp\htdocs\bioverify-ai
php artisan serve
```

Default local address:

`http://127.0.0.1:8000`

### Access control

Personnel management, enrollment, PDFs, Ask Pong and the face recognition test page require an administrator sign-in at `/login`. Create an administrator (the password is prompted, minimum 12 characters):

```powershell
php artisan admin:create
```

If the hidden password prompt does not accept input in your terminal, let the command generate a strong password and print it once:

```powershell
php artisan admin:create --name="Your Name" --email=you@example.com --generate-password
```

Personnel verify themselves at `/biometric-login`. A successful face or voice match opens only that person's record (`/recognized-person/{id}`) for 15 minutes; inactive personnel are never matched. Similarity scores are shown only to signed-in administrators. Recognition is limited to 10 attempts per minute per IP address.

Face images and voice recordings are stored on the private disk (`storage\app\private\people`) and served through authorized routes. On an existing installation, move older samples off the public disk once:

```powershell
php artisan biometrics:make-private
```

### Python virtual environment

`C:\xampp\htdocs\bioverify-ai\python-ai\.venv`

Activate it:

```powershell
cd C:\xampp\htdocs\bioverify-ai
.\python-ai\.venv\Scripts\Activate.ps1
```

Start FastAPI in development mode:

```powershell
python -m uvicorn main:app --app-dir python-ai --host 127.0.0.1 --port 8001 --reload
```

Health endpoint:

`http://127.0.0.1:8001/health`

This service handles both face and voice recognition. Voice uses the WeSpeaker ResNet34-LM ONNX model through onnxruntime (PyTorch is not used because Windows Smart App Control blocks its DLLs). The model file is not in git; download it once to:

`python-ai\models\voxceleb_resnet34_LM.onnx`

from `https://huggingface.co/Wespeaker/wespeaker-resnet34-LM` (SHA-256 `7bb2f06e9df17cdf1ef14ee8a15ab08ed28e8d0ef5054ee135741560df2ec068`). FFmpeg must be installed for audio decoding. After changing the voice model, regenerate stored embeddings with `php artisan voices:reembed`.

### RAG project

`C:\xampp\htdocs\bioverify-ai\rag-ai`

One process serves Ask Pong chat, PDF indexing and PDF questions on port 8002, so that only one process opens ChromaDB (`rag-ai\chroma_db`). Do not run a second process against that folder while it is running.

Start it:

```powershell
cd C:\xampp\htdocs\bioverify-ai
.\rag-ai\.venv\Scripts\python.exe rag-ai\app\chat_api.py
```

Health endpoint: `http://127.0.0.1:8002/health`

After editing notes in `knowledge-base`, rebuild the notes index through the running service:

```powershell
Invoke-RestMethod -Method Post http://127.0.0.1:8002/notes/reindex
```

PDF uploads are indexed by a Laravel queue job, so a queue worker must be running (`composer dev` starts one):

```powershell
php artisan queue:work --tries=1
```

Deleting a PDF from its page removes the stored file, its database row and its chunks from the index, so Pong stops citing it.

Retrieval uses nomic-embed-text with task prefixes (`search_query:` / `search_document:`) and drops chunks whose cosine distance is above a cutoff (notes 0.40, PDFs 0.38, set with `NOTES_MAX_DISTANCE` / `PDF_MAX_DISTANCE`), so unrelated PDFs are not added to answers. If the embedding model or prefixes change, rebuild both indexes through the running service:

```powershell
Invoke-RestMethod -Method Post http://127.0.0.1:8002/notes/reindex
Invoke-RestMethod -Method Post http://127.0.0.1:8002/documents/reembed
```

Face enrollment rejects unclear or small faces and a face already enrolled under a different person. Every face and voice attempt, matched or not, is written to `recognition_logs`.

### Obsidian vault

`C:\xampp\htdocs\bioverify-ai\knowledge-base`

This directory is the source of Markdown documentation for human reference and the RAG pipeline.

## 13. Troubleshooting Guidelines

### Face recognition does not match

Check image quality, face detection, embedding dimensions, model consistency, registered samples, similarity calculations, and threshold calibration.

### Python service is unavailable

Check that the correct virtual environment is active, dependencies are installed, and the service is listening on the configured port.

### AI chat fails

Verify the Laravel route, controller response, CSRF handling, Ollama availability, and any configured RAG service connection.

### RAG gives inaccurate answers

Check source coverage, indexing freshness, chunk quality, metadata, retrieval relevance, and whether the model receives the retrieved text.

### MEPCOM answer may be outdated

Check the date and authority of the source. Consult current official PCG or MEPCOM material and relevant approved issuances.

### HRMIS organizational values appear incorrect

Compare dropdown values and database records against the current authorized organizational reference. Do not automatically reuse historical unit names.

## 14. Recommended Knowledge-Base Structure

Create separate notes for more effective retrieval:

- [[MEPCOM Institutional Reference]]
- [[MEPCOM HRMIS]]
- [[Marine Environmental Protection]]
- [[Oil Spill and HNS Response]]
- [[MARPOL Reference]]
- [[MEPCOM Organizational Structure]]
- [[BioVerify AI Architecture]]
- [[Database Schema]]
- [[Face Recognition]]
- [[Voice Recognition]]
- [[Pong AI]]
- [[RAG Pipeline]]
- [[Security and Privacy]]
- [[Troubleshooting]]
- [[Development Setup]]

Each note should contain its own frontmatter, clear headings, source links, and an update date.

## 15. Official Reference Links

### MEPCOM

- Official website: https://mepcom.coastguard.gov.ph/
- Functions: https://mepcom.coastguard.gov.ph/transparency-seal/about-us/functions/

### Further authoritative references to maintain

- Current PCG laws and legal issuances.
- Current MEPCOM organizational orders.
- Approved marine environmental protection doctrine.
- Approved oil spill and HNS contingency plans.
- Official personnel regulations and rank/rate references.
- Authorized training and qualification references.

Add exact document titles, issuing authorities, dates, and official links when verified.

## 16. Development Roadmap

### BioVerify AI

- [x] Laravel application established.
- [x] MySQL database and core biometric tables established.
- [x] Python FastAPI service established.
- [x] InsightFace integration developed.
- [x] Voice recording interface developed.
- [ ] Validate face recognition thresholds.
- [ ] Complete and validate speaker recognition.
- [ ] Harden authentication and biometric data access.
- [ ] Verify Pong AI chat integration.
- [ ] Verify end-to-end RAG retrieval and response generation.
- [ ] Add source-aware answers and regression tests.

### MEPCOM knowledge

- [ ] Create a detailed institutional reference.
- [ ] Document marine environmental protection concepts.
- [ ] Add verified organizational references.
- [ ] Document the HRMIS software separately from official policy.
- [ ] Add current authoritative public documents.
- [ ] Record source dates and update stale information.
- [ ] Review indexed content for privacy and access restrictions.

### RAG and agent capabilities

- [ ] Verify the indexing pipeline.
- [ ] Verify ChromaDB collection configuration.
- [ ] Validate embedding model consistency.
- [ ] Test retrieval relevance.
- [ ] Ensure source metadata reaches Pong AI.
- [ ] Establish access controls for restricted content.
- [ ] Evaluate Hermes Agent capabilities and permissions.
- [ ] Evaluate future integrations, including Discord, only after defining security controls.

## 17. Final Operating Principle

BioVerify AI supplies the technical platform. The Obsidian knowledge base supplies structured project and institutional context. RAG retrieves relevant documentation. Pong AI explains that context in a conversational form.

For MEPCOM-related answers, the assistant must distinguish official institutional facts from software implementation details and unverified notes. It should use authoritative sources for official claims, protect sensitive information, and clearly state uncertainty when evidence is incomplete.
