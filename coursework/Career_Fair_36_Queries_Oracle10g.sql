-- Career Fair Management System
-- Final Term: 36 Numbered SQL / PL/SQL Queries
-- Oracle 10g / Oracle Database Express Edition
-- IMPORTANT: Enable DBMS_OUTPUT before running PL/SQL blocks.
-- In SQL*Plus/SQLcl: SET SERVEROUTPUT ON;
-- In Oracle APEX/Database Homepage SQL Commands, view the DBMS Output area.

-- ============================================================
-- A. BASIC PL/SQL / SQL QUERIES (1-16)
-- ============================================================

-- 1. Variable - Query 1
-- Question: Write a PL/SQL block using variables to count and display
-- the total number of student registrations for Career Fair ID 1.
DECLARE
    v_fair_id NUMBER := 1;
    v_total_registration NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_total_registration
    FROM fair_registration
    WHERE fair_id = v_fair_id;

    DBMS_OUTPUT.PUT_LINE('Career Fair ID: ' || v_fair_id);
    DBMS_OUTPUT.PUT_LINE('Total Registration: ' || v_total_registration);
END;
/

-- 2. Variable - Query 2
-- Question: Write a PL/SQL block to calculate and display the average
-- feedback rating for Career Fair ID 1.
DECLARE
    v_fair_id NUMBER := 1;
    v_average_rating NUMBER;
BEGIN
    SELECT AVG(rating) INTO v_average_rating
    FROM feedback
    WHERE fair_id = v_fair_id;

    DBMS_OUTPUT.PUT_LINE('Average Rating: ' || ROUND(v_average_rating, 2));
END;
/

-- 3. Operator Program 1 (Arithmetic)
-- Question: Calculate the total required booths for 10 companies,
-- assuming each needs 2 booths.
DECLARE
    v_companies NUMBER := 10;
    v_booths_per_company NUMBER := 2;
    v_total_booths NUMBER;
BEGIN
    v_total_booths := v_companies * v_booths_per_company;
    DBMS_OUTPUT.PUT_LINE('Total Booths Required: ' || v_total_booths);
END;
/

-- 4. Operator Program 2 (Logical)
-- Question: Check if an application is SHORTLISTED and ready for interview.
DECLARE
    v_status VARCHAR2(20) := 'SHORTLISTED';
BEGIN
    IF v_status = 'SHORTLISTED' AND LENGTH(v_status) > 0 THEN
        DBMS_OUTPUT.PUT_LINE('Ready for Interview');
    END IF;
END;
/

-- 5. Single Row Function 1
-- Question: Use UPPER and TO_CHAR to display a fair title and event date.
DECLARE
    v_title VARCHAR2(150);
    v_date VARCHAR2(30);
BEGIN
    SELECT UPPER(fair_title), TO_CHAR(event_date, 'DD-MON-YYYY')
    INTO v_title, v_date
    FROM career_fair
    WHERE fair_id = 1;

    DBMS_OUTPUT.PUT_LINE('Title: ' || v_title || ' | Date: ' || v_date);
END;
/

-- 6. Single Row Function 2
-- Question: Use INITCAP and LENGTH to display a student's formatted name
-- and name length.
DECLARE
    v_name VARCHAR2(100);
    v_len NUMBER;
BEGIN
    SELECT INITCAP(student_name), LENGTH(student_name)
    INTO v_name, v_len
    FROM student
    WHERE student_id = 1;

    DBMS_OUTPUT.PUT_LINE('Name: ' || v_name || ' | Length: ' || v_len);
END;
/

-- 7. Group Function 1
-- Question: Use COUNT and MAX to find total career fairs and latest event date.
DECLARE
    v_total NUMBER;
    v_latest DATE;
BEGIN
    SELECT COUNT(*), MAX(event_date)
    INTO v_total, v_latest
    FROM career_fair;

    DBMS_OUTPUT.PUT_LINE(
        'Total Fairs: ' || v_total ||
        ' | Latest: ' || TO_CHAR(v_latest, 'DD-MON-YYYY')
    );
END;
/

-- 8. Group Function 2
-- Question: Use MIN and AVG to display the lowest and average feedback ratings.
DECLARE
    v_min NUMBER;
    v_avg NUMBER;
BEGIN
    SELECT MIN(rating), AVG(rating)
    INTO v_min, v_avg
    FROM feedback;

    DBMS_OUTPUT.PUT_LINE('Min Rating: ' || v_min || ' | Avg: ' || ROUND(v_avg, 2));
END;
/

-- 9. Loop Program 1 (FOR Loop)
-- Question: Print booth processing numbers from 1 to 5.
BEGIN
    FOR i IN 1..5 LOOP
        DBMS_OUTPUT.PUT_LINE('Processing Booth Number: ' || i);
    END LOOP;
END;
/

-- 10. Loop Program 2 (WHILE Loop)
-- Question: Simulate printing 5 interview slots.
DECLARE
    v_counter NUMBER := 1;
BEGIN
    WHILE v_counter <= 5 LOOP
        DBMS_OUTPUT.PUT_LINE('Interview Slot ' || v_counter || ' assigned.');
        v_counter := v_counter + 1;
    END LOOP;
END;
/

-- 11. Conditional Statement 1 (IF-ELSE)
-- Question: Verify whether a company's approval status is APPROVED.
DECLARE
    v_status VARCHAR2(20) := 'APPROVED';
BEGIN
    IF v_status = 'APPROVED' THEN
        DBMS_OUTPUT.PUT_LINE('Company participation is confirmed.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('Company is pending approval.');
    END IF;
END;
/

-- 12. Conditional Statement 2 (CASE)
-- Question: Evaluate whether a booth is AVAILABLE or ASSIGNED.
DECLARE
    v_booth_status VARCHAR2(20) := 'AVAILABLE';
BEGIN
    CASE v_booth_status
        WHEN 'AVAILABLE' THEN DBMS_OUTPUT.PUT_LINE('Booth can be booked.');
        WHEN 'ASSIGNED' THEN DBMS_OUTPUT.PUT_LINE('Booth already taken.');
        ELSE DBMS_OUTPUT.PUT_LINE('Unknown status.');
    END CASE;
END;
/

-- 13. Subquery 1 (Single Row)
-- Question: Find students who graduate in the same year as Student ID 1.
SELECT student_name, graduation_year
FROM student
WHERE graduation_year = (
    SELECT graduation_year
    FROM student
    WHERE student_id = 1
);

-- 14. Subquery 2 (Multi Row)
-- Question: Find companies that have posted job vacancies.
SELECT company_name
FROM company
WHERE company_id IN (
    SELECT company_id
    FROM job_vacancy
);

-- 15. Join Query 1 (Two Tables)
-- Question: Display job positions and their corresponding company names.
SELECT c.company_name, j.position_title
FROM company c
JOIN job_vacancy j
    ON c.company_id = j.company_id;

-- 16. Join Query 2 (Three Tables)
-- Question: Display student names, fair titles, and registration statuses.
SELECT s.student_name, cf.fair_title, fr.registration_status
FROM student s
JOIN fair_registration fr
    ON s.student_id = fr.student_id
JOIN career_fair cf
    ON fr.fair_id = cf.fair_id;

-- ============================================================
-- B. ADVANCED PL/SQL QUERIES WITH EXCEPTION HANDLING (17-32)
-- ============================================================

-- 17. Stored Function 1
-- Question: Calculate and return total student registrations for a fair.
CREATE OR REPLACE FUNCTION fn_total_registration(
    p_fair_id NUMBER
) RETURN NUMBER IS
    v_total NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_total
    FROM fair_registration
    WHERE fair_id = p_fair_id;

    RETURN v_total;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN 0;
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
        RETURN -1;
END;
/

DECLARE
    v_result NUMBER;
BEGIN
    v_result := fn_total_registration(1);
    DBMS_OUTPUT.PUT_LINE('Total Registrations: ' || v_result);
END;
/

-- 18. Stored Function 2
-- Question: Calculate and return average feedback rating for a fair.
CREATE OR REPLACE FUNCTION fn_avg_rating(
    p_fair_id NUMBER
) RETURN NUMBER IS
    v_avg NUMBER;
BEGIN
    SELECT AVG(rating) INTO v_avg
    FROM feedback
    WHERE fair_id = p_fair_id;

    RETURN NVL(v_avg, 0);
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
        RETURN -1;
END;
/

DECLARE
    v_result NUMBER;
BEGIN
    v_result := fn_avg_rating(1);
    DBMS_OUTPUT.PUT_LINE('Average Rating: ' || ROUND(v_result, 2));
END;
/

-- 19. Stored Procedure 1
-- Question: Register a student for a specific career fair.
CREATE OR REPLACE PROCEDURE pr_register_student(
    p_student_id NUMBER,
    p_fair_id NUMBER
) IS
BEGIN
    INSERT INTO fair_registration (
        student_id,
        fair_id,
        registration_date,
        registration_status
    ) VALUES (
        p_student_id,
        p_fair_id,
        SYSDATE,
        'REGISTERED'
    );

    DBMS_OUTPUT.PUT_LINE('Student registered successfully.');
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        DBMS_OUTPUT.PUT_LINE('Error: Student is already registered.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- Run-safe demo with the seeded project data:
SAVEPOINT before_registration_demo;
BEGIN
    pr_register_student(5, 1);
END;
/
SELECT student_id, fair_id, registration_date, registration_status
FROM fair_registration
WHERE student_id = 5 AND fair_id = 1;
ROLLBACK TO before_registration_demo;

-- 20. Stored Procedure 2
-- Question: Assign a specific booth to a participating company.
CREATE OR REPLACE PROCEDURE pr_assign_booth(
    p_booth_id NUMBER,
    p_company_id NUMBER
) IS
BEGIN
    INSERT INTO booth_assignment (
        booth_id,
        company_id,
        assignment_date
    ) VALUES (
        p_booth_id,
        p_company_id,
        SYSDATE
    );

    UPDATE booth
    SET booth_status = 'ASSIGNED'
    WHERE booth_id = p_booth_id;

    DBMS_OUTPUT.PUT_LINE('Booth assigned successfully.');
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        DBMS_OUTPUT.PUT_LINE('Error: Booth already assigned.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- Run-safe demo: Booth 3 is AVAILABLE in the supplied sample data.
SAVEPOINT before_booth_demo;
BEGIN
    pr_assign_booth(3, 1);
END;
/
SELECT ba.booth_id, ba.company_id, ba.assignment_date, b.booth_status
FROM booth_assignment ba
JOIN booth b ON b.booth_id = ba.booth_id
WHERE ba.booth_id = 3;
ROLLBACK TO before_booth_demo;

-- 21. Table Based Record 1
-- Question: Use %ROWTYPE to display Student ID 1 details.
DECLARE
    v_student student%ROWTYPE;
BEGIN
    SELECT * INTO v_student
    FROM student
    WHERE student_id = 1;

    DBMS_OUTPUT.PUT_LINE(
        'Name: ' || v_student.student_name ||
        ' | Email: ' || v_student.email
    );
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('Error: Student not found.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 22. Table Based Record 2
-- Question: Use %ROWTYPE to display Job Vacancy ID 1 details.
DECLARE
    v_job job_vacancy%ROWTYPE;
BEGIN
    SELECT * INTO v_job
    FROM job_vacancy
    WHERE job_id = 1;

    DBMS_OUTPUT.PUT_LINE(
        'Position: ' || v_job.position_title ||
        ' | Deadline: ' || TO_CHAR(v_job.application_deadline, 'DD-MON-YYYY')
    );
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('Error: Job vacancy not found.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 23. Explicit Cursor 1
-- Question: Display approved companies for Career Fair 1.
DECLARE
    CURSOR c_company IS
        SELECT c.company_name, cp.approval_status
        FROM company c
        JOIN company_participation cp
            ON cp.company_id = c.company_id
        WHERE cp.fair_id = 1
          AND cp.approval_status = 'APPROVED';

    v_name company.company_name%TYPE;
    v_status company_participation.approval_status%TYPE;
BEGIN
    OPEN c_company;
    LOOP
        FETCH c_company INTO v_name, v_status;
        EXIT WHEN c_company%NOTFOUND;
        DBMS_OUTPUT.PUT_LINE('Company: ' || v_name || ' | Status: ' || v_status);
    END LOOP;
    CLOSE c_company;
EXCEPTION
    WHEN INVALID_CURSOR THEN
        DBMS_OUTPUT.PUT_LINE('Error: Cursor problem.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 24. Explicit Cursor 2
-- Question: Display application status for all applications from Student ID 1.
DECLARE
    CURSOR c_app IS
        SELECT a.application_id, j.position_title, a.application_status
        FROM application a
        JOIN job_vacancy j
            ON a.job_id = j.job_id
        WHERE a.student_id = 1;

    v_app_id application.application_id%TYPE;
    v_title job_vacancy.position_title%TYPE;
    v_status application.application_status%TYPE;
BEGIN
    OPEN c_app;
    LOOP
        FETCH c_app INTO v_app_id, v_title, v_status;
        EXIT WHEN c_app%NOTFOUND;
        DBMS_OUTPUT.PUT_LINE(
            'App ID: ' || v_app_id ||
            ' | Job: ' || v_title ||
            ' | Status: ' || v_status
        );
    END LOOP;
    CLOSE c_app;
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 25. Cursor Based Record 1
-- Question: Fetch and display detailed fair registration records.
DECLARE
    CURSOR c_reg IS
        SELECT s.student_name, f.fair_title, r.registration_date
        FROM fair_registration r
        JOIN student s ON r.student_id = s.student_id
        JOIN career_fair f ON r.fair_id = f.fair_id;

    v_reg_rec c_reg%ROWTYPE;
BEGIN
    OPEN c_reg;
    LOOP
        FETCH c_reg INTO v_reg_rec;
        EXIT WHEN c_reg%NOTFOUND;
        DBMS_OUTPUT.PUT_LINE(
            'Student: ' || v_reg_rec.student_name ||
            ' | Fair: ' || v_reg_rec.fair_title
        );
    END LOOP;
    CLOSE c_reg;
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 26. Cursor Based Record 2
-- Question: Fetch and display upcoming interview schedules.
DECLARE
    CURSOR c_int IS
        SELECT i.interview_id, i.interview_date, c.representative_name
        FROM interview i
        JOIN company_representative c
            ON i.representative_id = c.representative_id;

    v_int_rec c_int%ROWTYPE;
BEGIN
    OPEN c_int;
    LOOP
        FETCH c_int INTO v_int_rec;
        EXIT WHEN c_int%NOTFOUND;
        DBMS_OUTPUT.PUT_LINE(
            'Date: ' || TO_CHAR(v_int_rec.interview_date, 'DD-MON-YYYY') ||
            ' | Rep: ' || v_int_rec.representative_name
        );
    END LOOP;
    CLOSE c_int;
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/

-- 27. Row Level Trigger 1
-- Question: Prevent registration after the career-fair deadline.
CREATE OR REPLACE TRIGGER trg_check_deadline
BEFORE INSERT ON fair_registration
FOR EACH ROW
DECLARE
    v_deadline DATE;
BEGIN
    SELECT registration_deadline INTO v_deadline
    FROM career_fair
    WHERE fair_id = :NEW.fair_id;

    IF SYSDATE > v_deadline THEN
        RAISE_APPLICATION_ERROR(-20001, 'Registration deadline has passed.');
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20002, 'Fair ID not found.');
END;
/

-- 28. Row Level Trigger 2
-- Question: Allow interview insertion only for SHORTLISTED applications.
CREATE OR REPLACE TRIGGER trg_interview_check
BEFORE INSERT ON interview
FOR EACH ROW
DECLARE
    v_status VARCHAR2(30);
BEGIN
    SELECT application_status INTO v_status
    FROM application
    WHERE application_id = :NEW.application_id;

    IF v_status <> 'SHORTLISTED' THEN
        RAISE_APPLICATION_ERROR(
            -20010,
            'Only shortlisted applicants can be interviewed.'
        );
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20011, 'Application ID not found.');
END;
/

-- 29. Statement Level Trigger 1
-- Question: Log updates to APPLICATION.
-- IMPORTANT: Your uploaded report uses SYSTEM_AUDIT, not AUDIT_LOG.
CREATE OR REPLACE TRIGGER trg_audit_application
AFTER UPDATE ON application
BEGIN
    INSERT INTO system_audit (
        audit_id,
        table_name,
        action_name,
        action_date,
        action_user
    ) VALUES (
        seq_system_audit.NEXTVAL,
        'APPLICATION',
        'UPDATE',
        SYSDATE,
        USER
    );
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Audit logging failed: ' || SQLERRM);
END;
/

-- Demo
SAVEPOINT before_application_audit;
UPDATE application
SET application_status = application_status
WHERE application_id = 1;
SELECT audit_id, table_name, action_name, action_date, action_user
FROM (
    SELECT audit_id, table_name, action_name, action_date, action_user
    FROM system_audit
    WHERE table_name = 'APPLICATION'
    ORDER BY audit_id DESC
)
WHERE ROWNUM = 1;
ROLLBACK TO before_application_audit;

-- 30. Statement Level Trigger 2
-- Question: Log updates to BOOTH.
CREATE OR REPLACE TRIGGER trg_audit_booth
AFTER UPDATE ON booth
BEGIN
    INSERT INTO system_audit (
        audit_id,
        table_name,
        action_name,
        action_date,
        action_user
    ) VALUES (
        seq_system_audit.NEXTVAL,
        'BOOTH',
        'UPDATE',
        SYSDATE,
        USER
    );
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Audit logging failed: ' || SQLERRM);
END;
/

-- Demo
SAVEPOINT before_booth_audit;
UPDATE booth
SET booth_status = booth_status
WHERE booth_id = 3;
SELECT audit_id, table_name, action_name, action_date, action_user
FROM (
    SELECT audit_id, table_name, action_name, action_date, action_user
    FROM system_audit
    WHERE table_name = 'BOOTH'
    ORDER BY audit_id DESC
)
WHERE ROWNUM = 1;
ROLLBACK TO before_booth_audit;

-- 31. Package 1
-- Question: Create a fair-management package with total-registration function.
CREATE OR REPLACE PACKAGE pkg_fair_mgt IS
    FUNCTION get_total_reg(p_fair_id NUMBER) RETURN NUMBER;
END pkg_fair_mgt;
/

CREATE OR REPLACE PACKAGE BODY pkg_fair_mgt IS
    FUNCTION get_total_reg(p_fair_id NUMBER) RETURN NUMBER IS
        v_total NUMBER;
    BEGIN
        SELECT COUNT(*) INTO v_total
        FROM fair_registration
        WHERE fair_id = p_fair_id;

        RETURN v_total;
    EXCEPTION
        WHEN OTHERS THEN
            RETURN -1;
    END get_total_reg;
END pkg_fair_mgt;
/

BEGIN
    DBMS_OUTPUT.PUT_LINE(
        'Total Reg for Fair 1: ' || pkg_fair_mgt.get_total_reg(1)
    );
END;
/

-- 32. Package 2
-- Question: Create an application-management package with application-count function.
CREATE OR REPLACE PACKAGE pkg_app_mgt IS
    FUNCTION get_app_count(p_job_id NUMBER) RETURN NUMBER;
END pkg_app_mgt;
/

CREATE OR REPLACE PACKAGE BODY pkg_app_mgt IS
    FUNCTION get_app_count(p_job_id NUMBER) RETURN NUMBER IS
        v_count NUMBER;
    BEGIN
        SELECT COUNT(*) INTO v_count
        FROM application
        WHERE job_id = p_job_id;

        RETURN v_count;
    EXCEPTION
        WHEN OTHERS THEN
            RETURN -1;
    END get_app_count;
END pkg_app_mgt;
/

BEGIN
    DBMS_OUTPUT.PUT_LINE(
        'Applications for Job 1: ' || pkg_app_mgt.get_app_count(1)
    );
END;
/

-- ============================================================
-- C. DATABASE LOCKING QUERIES (33-36)
-- ============================================================

-- 33. Implicit Locking - Query 1
-- Question: Update a student's graduation year and demonstrate implicit locking.
SAVEPOINT before_implicit_student_lock;
UPDATE student
SET graduation_year = 2028
WHERE student_id = 1;
-- Oracle implicitly locks this row until COMMIT or ROLLBACK.
-- Take the screenshot before releasing the lock.
ROLLBACK TO before_implicit_student_lock;

-- 34. Implicit Locking - Query 2
-- Question: Delete a specific application record to demonstrate implicit locking.
SAVEPOINT before_implicit_application_lock;
DELETE FROM application
WHERE application_id = 3;
-- Application ID 3 has no INTERVIEW child row in the seeded data, so the delete can demonstrate the lock safely.
-- Oracle implicitly locks the affected row until COMMIT or ROLLBACK.
-- Take the screenshot before releasing the lock.
ROLLBACK TO before_implicit_application_lock;

-- 35. Explicit Locking - Query 1
-- Question: Manually lock Job Vacancy ID 2 using FOR UPDATE.
SELECT position_title, vacancy_status
FROM job_vacancy
WHERE job_id = 2
FOR UPDATE;
-- Take the screenshot while the lock is active.
ROLLBACK;

-- 36. Explicit Locking - Query 2
-- Question: Lock booth records for Fair ID 1 using FOR UPDATE NOWAIT.
SELECT booth_number, booth_status
FROM booth
WHERE fair_id = 1
FOR UPDATE NOWAIT;
-- Take the screenshot while the lock is active.
ROLLBACK;

-- END OF 36 QUERIES
