# Patient Reported Outcomes (PRO) API

---

## Requirements

- PHP 8.4+
- Composer
- MySQL 8+
- Or: Docker + Docker Compose

---

## Local Setup (NOT using Docker)

**1. Clone and install dependencies**
```bash
git clone https://github.com/ChristianJ1999-cronos/tti-backend-assessment.git
cd tti-backend-assessment
composer install
```

**2. Configure the environment**
```bash
cp .env.example  .env
php artisan key:generate
```

**3. Update `.env` with your database credentials**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tti_assessment
DB_USERNAME=root
DB_PASSWORD=your_password
```

**4. Run migrations and seed sample data**
```bash
php artisan migrate:fresh --seed
```

**5. Start the server**
```bash
php artisan serve
```
API is available at 'http://127.0.0.1:8000'

---

### Local Setup (USING Docker)
```bash
cp .env.example  .env
docker-compose up --build -d
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```

API is available at 'http://localhost:8000'

---

## Running Tests
```bash
composer test
```

---

## API Endpoints

Method | Endpoint                                               | Description
POST   | 'api/patients'                                         | Create a new patient
POST   | 'api/instruments'                                      | Create a new instrument with questions
POST   | 'api/patients/{patientId}/submissions'                 | Submit a completed instrument for a patient
GET    | 'api/patients/{patientId}/submissions'                 | List all submissions (paginated, newest first)
GET    | 'api/patients/{patientId}/submissions/{submissionID}'  | Get one single submission with all the answers
GET    | 'api/patients/{patientId}/summary'                     | Summary of total of stats

## Creating a patient
```json
POST /api/patients
{
    "name": "Bruca Banner",
    "date_of_birth": "1998-08-15",
    "mrn": "MRN-001"
}
```

### Example: Create an Instrument
```json
POST /api/instruments
{
    "title": "Pain Assessment",
    "description": "Daily pain tracking",
    "questions": [
        { "prompt": "Rate your pain", "response_type": "scale_1_5", "order": 1 },
        { "prompt": "Any nausea?", "response_type": "yes_no", "order": 2 },
        { "prompt": "How is your appetite?", "response_type": "free_text", "order": 3 }
    ]
}
```

### Example: Submit an Instrument
```json
POST /api/patients/1/submissions
{
    "instrument_id": 1,
    "answers": [
        { "question_id": 1, "value": 3 },
        { "question_id": 2, "value": true },
        { "question_id": 3, "value": "I have been able to eat more everyday." }
    ]
}
```

### Example: Get Summary
```
GET /api/patients/1/summary?instrument_id=1
```

---

## Design Decisions

**Used a schema of 5 tables** - Patients, Instruments, Questions, Submissions, and Answers are separated. Adding the Questions and Answers table for better structure and separation. Making the Submissions table a tracker for when the instrument was submitted, the Questions table is used to be able to save the questions for every Instrument as well as the type of question (scale_1_5, yes_no, free_text), and the Answer table becoming all the answers which patients submitted saved in this table. Doing so allows for different Instruments to have different amount of questions as well as multiple submissions per day and being able to track with questions belong to what submission. 

**Custom AllQuestionsAnswered validation rule** — Ensures every question in the instrument is answered before a submission is accepted. This runs at the Form Request layer so the controller stays clean.

**Per-question dynamic validation** — Answer values are validated against their question's response_type at submission time. scale_1_5 enforces integer 1-5, yes_no enforces boolean, free_text allows empty strings.

**DB::transaction on submission creation** — The submission record and all answer records are created atomically. If any answer fails to save, the entire submission is rolled back.

**Eloquent Resources for all responses** — Consistent JSON structure throughout all endpoints.

**RefreshDatabase in tests** - Each test is ran against a clean database state hence tests are submitted and deleted right after to keep database clean from dummy data.


## What I would improve with more time

- Add profiles/authentication to have seemless access of information for the patient or the doctor.
- Creating more Instruments with more questions.
- Incorporate more test cases: ensuring unique value enforces only one unique value, making sure unique ids are working perfectly.
- Add eager loading specifically on the submissions list endpoint. Especially if answers are ever included in the list response. This would avoid having a N+1 query.
