-- FINAL TERM ADBMS COURSEWORK HELPER
-- Run these examples in Oracle 10g/Oracle environment, NOT in MySQL.
-- Use two Oracle sessions/users when demonstrating locks.

-- ============================================================
-- 1) IMPLICIT LOCKING EXAMPLE 1
-- Session A:
UPDATE booth
SET booth_status = 'ASSIGNED'
WHERE booth_id = 3;
-- Do not COMMIT yet. Oracle automatically locks the modified row.
-- Session B can attempt to update the same row and will wait.
-- Release with:
COMMIT;

-- 2) IMPLICIT LOCKING EXAMPLE 2
-- Session A:
DELETE FROM session_attendance
WHERE student_id = 1 AND session_id = 1;
-- Keep transaction open for the screenshot/demo.
ROLLBACK;

-- ============================================================
-- 3) EXPLICIT LOCKING EXAMPLE 1 - SELECT ... FOR UPDATE NOWAIT
SELECT booth_id, fair_id, booth_number, booth_status
FROM booth
WHERE booth_id = 1
FOR UPDATE NOWAIT;
-- Release with COMMIT or ROLLBACK.
ROLLBACK;

-- 4) EXPLICIT LOCKING EXAMPLE 2 - LOCK TABLE
LOCK TABLE company_participation IN EXCLUSIVE MODE NOWAIT;
-- While this lock is active, demonstrate another session trying to modify it.
ROLLBACK;

-- ============================================================
-- TRANSACTION / SAVEPOINT DEMO
SAVEPOINT before_booth_update;

UPDATE booth
SET booth_status = 'ASSIGNED'
WHERE booth_id = 3;

SAVEPOINT after_booth_update;

INSERT INTO booth_assignment(booth_id, company_id, assignment_date)
VALUES (3, 3, SYSDATE);

-- Roll back only the assignment insert, retaining the booth update:
ROLLBACK TO after_booth_update;

-- Or roll back everything after the first savepoint:
ROLLBACK TO before_booth_update;

-- Or make current transaction permanent:
COMMIT;
