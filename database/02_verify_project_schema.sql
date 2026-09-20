-- Run in Oracle SQL Commands while logged in as CF_OWNER.
SELECT table_name
FROM user_tables
ORDER BY table_name;

SELECT sequence_name
FROM user_sequences
ORDER BY sequence_name;

SELECT USER AS connected_schema FROM dual;

SELECT COUNT(*) AS students FROM student;
SELECT COUNT(*) AS fairs FROM career_fair;
SELECT COUNT(*) AS companies FROM company;
SELECT COUNT(*) AS jobs FROM job_vacancy;
SELECT COUNT(*) AS applications FROM application;
