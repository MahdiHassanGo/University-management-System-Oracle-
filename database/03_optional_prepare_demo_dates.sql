-- OPTIONAL: Prepare existing sample data for a live demo when old dates have already passed.
-- Run only if your current Career Fair / Job deadlines are in the past.
-- It preserves IDs and relationships and only moves dates forward.

UPDATE career_fair
SET event_date = TRUNC(SYSDATE) + 30 + (fair_id * 7),
    registration_deadline = TRUNC(SYSDATE) + 20 + (fair_id * 7),
    start_time = TRUNC(SYSDATE) + 30 + (fair_id * 7) + 9/24,
    end_time = TRUNC(SYSDATE) + 30 + (fair_id * 7) + 17/24;

UPDATE job_vacancy j
SET application_deadline = (
    SELECT cf.registration_deadline - 2
    FROM career_fair cf
    WHERE cf.fair_id = j.fair_id
);

UPDATE fair_session fs
SET start_time = (
    SELECT cf.event_date + 11/24
    FROM career_fair cf
    WHERE cf.fair_id = fs.fair_id
);

COMMIT;

SELECT fair_id, fair_title, event_date, registration_deadline
FROM career_fair
ORDER BY fair_id;

SELECT job_id, position_title, application_deadline, vacancy_status
FROM job_vacancy
ORDER BY job_id;
