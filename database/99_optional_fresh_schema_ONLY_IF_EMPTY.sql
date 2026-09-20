-- Career Fair Management System - Oracle 10g Fresh Setup
-- Run this ONLY in a fresh/empty project schema.
-- If your tables and sample data already exist, DO NOT run this file.

-- ============================================================
-- 1) TABLES (final schema aligned with the report + 36 queries)
-- ============================================================

CREATE TABLE administrator (
    administrator_id NUMBER PRIMARY KEY,
    administrator_name VARCHAR2(100) NOT NULL,
    email VARCHAR2(100) UNIQUE NOT NULL,
    phone_number VARCHAR2(20)
);

CREATE TABLE career_fair (
    fair_id NUMBER PRIMARY KEY,
    administrator_id NUMBER NOT NULL,
    fair_title VARCHAR2(150) NOT NULL,
    event_date DATE,
    venue VARCHAR2(150),
    start_time DATE,
    end_time DATE,
    description VARCHAR2(500),
    registration_deadline DATE,
    CONSTRAINT fk_fair_admin FOREIGN KEY (administrator_id)
        REFERENCES administrator(administrator_id)
);

CREATE TABLE student (
    student_id NUMBER PRIMARY KEY,
    student_name VARCHAR2(100) NOT NULL,
    email VARCHAR2(100) UNIQUE,
    phone_number VARCHAR2(20),
    department VARCHAR2(100),
    graduation_year NUMBER,
    resume_info VARCHAR2(200)
);

CREATE TABLE skill (
    skill_id NUMBER PRIMARY KEY,
    skill_name VARCHAR2(100) UNIQUE NOT NULL
);

CREATE TABLE student_skill (
    student_id NUMBER,
    skill_id NUMBER,
    CONSTRAINT pk_student_skill PRIMARY KEY (student_id, skill_id),
    CONSTRAINT fk_ss_student FOREIGN KEY (student_id) REFERENCES student(student_id),
    CONSTRAINT fk_ss_skill FOREIGN KEY (skill_id) REFERENCES skill(skill_id)
);

CREATE TABLE fair_registration (
    student_id NUMBER,
    fair_id NUMBER,
    registration_date DATE,
    registration_status VARCHAR2(30),
    CONSTRAINT pk_fair_registration PRIMARY KEY (student_id, fair_id),
    CONSTRAINT fk_fr_student FOREIGN KEY (student_id) REFERENCES student(student_id),
    CONSTRAINT fk_fr_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id)
);

CREATE TABLE company (
    company_id NUMBER PRIMARY KEY,
    company_name VARCHAR2(150) NOT NULL,
    industry_type VARCHAR2(100),
    address VARCHAR2(200),
    email VARCHAR2(100) UNIQUE,
    phone_number VARCHAR2(20),
    website VARCHAR2(200)
);

CREATE TABLE company_participation (
    company_id NUMBER,
    fair_id NUMBER,
    company_registration_date DATE,
    approval_status VARCHAR2(20),
    CONSTRAINT pk_company_participation PRIMARY KEY (company_id, fair_id),
    CONSTRAINT fk_cp_company FOREIGN KEY (company_id) REFERENCES company(company_id),
    CONSTRAINT fk_cp_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id)
);

CREATE TABLE company_representative (
    representative_id NUMBER PRIMARY KEY,
    company_id NUMBER NOT NULL,
    representative_name VARCHAR2(100),
    designation VARCHAR2(100),
    email VARCHAR2(100) UNIQUE,
    phone_number VARCHAR2(20),
    CONSTRAINT fk_rep_company FOREIGN KEY (company_id) REFERENCES company(company_id)
);

CREATE TABLE booth (
    booth_id NUMBER PRIMARY KEY,
    fair_id NUMBER NOT NULL,
    booth_number VARCHAR2(30),
    location VARCHAR2(150),
    booth_status VARCHAR2(30),
    CONSTRAINT fk_booth_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id)
);

CREATE TABLE booth_assignment (
    booth_id NUMBER PRIMARY KEY,
    company_id NUMBER NOT NULL,
    assignment_date DATE,
    CONSTRAINT fk_ba_booth FOREIGN KEY (booth_id) REFERENCES booth(booth_id),
    CONSTRAINT fk_ba_company FOREIGN KEY (company_id) REFERENCES company(company_id)
);

CREATE TABLE job_vacancy (
    job_id NUMBER PRIMARY KEY,
    company_id NUMBER NOT NULL,
    fair_id NUMBER NOT NULL,
    position_title VARCHAR2(150),
    employment_type VARCHAR2(50),
    job_description VARCHAR2(500),
    application_deadline DATE,
    vacancy_status VARCHAR2(30),
    CONSTRAINT fk_job_company FOREIGN KEY (company_id) REFERENCES company(company_id),
    CONSTRAINT fk_job_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id)
);

CREATE TABLE job_required_skill (
    job_id NUMBER,
    skill_id NUMBER,
    CONSTRAINT pk_job_required_skill PRIMARY KEY (job_id, skill_id),
    CONSTRAINT fk_jrs_job FOREIGN KEY (job_id) REFERENCES job_vacancy(job_id),
    CONSTRAINT fk_jrs_skill FOREIGN KEY (skill_id) REFERENCES skill(skill_id)
);

CREATE TABLE application (
    application_id NUMBER PRIMARY KEY,
    student_id NUMBER NOT NULL,
    job_id NUMBER NOT NULL,
    application_date DATE,
    submitted_resume VARCHAR2(200),
    cover_letter VARCHAR2(500),
    application_status VARCHAR2(30),
    CONSTRAINT fk_app_student FOREIGN KEY (student_id) REFERENCES student(student_id),
    CONSTRAINT fk_app_job FOREIGN KEY (job_id) REFERENCES job_vacancy(job_id)
);

CREATE TABLE interview (
    interview_id NUMBER PRIMARY KEY,
    application_id NUMBER NOT NULL,
    representative_id NUMBER NOT NULL,
    interview_date DATE,
    interview_time VARCHAR2(20),
    location_or_link VARCHAR2(200),
    interview_stage VARCHAR2(50),
    interview_status VARCHAR2(30),
    interview_result VARCHAR2(30),
    CONSTRAINT fk_int_app FOREIGN KEY (application_id) REFERENCES application(application_id),
    CONSTRAINT fk_int_rep FOREIGN KEY (representative_id) REFERENCES company_representative(representative_id)
);

CREATE TABLE fair_session (
    session_id NUMBER PRIMARY KEY,
    fair_id NUMBER NOT NULL,
    session_title VARCHAR2(150),
    speaker VARCHAR2(100),
    venue VARCHAR2(150),
    start_time DATE,
    CONSTRAINT fk_session_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id)
);

CREATE TABLE session_attendance (
    student_id NUMBER,
    session_id NUMBER,
    attendance_status VARCHAR2(30),
    check_in_time DATE,
    CONSTRAINT pk_session_attendance PRIMARY KEY (student_id, session_id),
    CONSTRAINT fk_sa_student FOREIGN KEY (student_id) REFERENCES student(student_id),
    CONSTRAINT fk_sa_session FOREIGN KEY (session_id) REFERENCES fair_session(session_id)
);

CREATE TABLE feedback (
    feedback_id NUMBER PRIMARY KEY,
    student_id NUMBER NOT NULL,
    fair_id NUMBER NOT NULL,
    submission_date DATE,
    rating NUMBER(2),
    comments VARCHAR2(300),
    CONSTRAINT fk_feedback_student FOREIGN KEY (student_id) REFERENCES student(student_id),
    CONSTRAINT fk_feedback_fair FOREIGN KEY (fair_id) REFERENCES career_fair(fair_id),
    CONSTRAINT ck_feedback_rating CHECK (rating BETWEEN 1 AND 5)
);

CREATE TABLE system_audit (
    audit_id NUMBER PRIMARY KEY,
    table_name VARCHAR2(50),
    action_name VARCHAR2(200),
    action_date DATE,
    action_user VARCHAR2(100)
);

-- ============================================================
-- 2) SEQUENCES
-- ============================================================
CREATE SEQUENCE seq_administrator START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_career_fair START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_student START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_skill START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_company START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_representative START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_booth START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_job START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_application START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_interview START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_session START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_feedback START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE seq_system_audit START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

-- ============================================================
-- 3) SAMPLE DATA FROM THE REPORT
-- ============================================================

-- ADMINISTRATOR
INSERT INTO administrator VALUES (seq_administrator.NEXTVAL, 'Nadia Rahman', 'nadia.admin@example.edu', '01710000001');
INSERT INTO administrator VALUES (seq_administrator.NEXTVAL, 'Imran Hossain', 'imran.admin@example.edu', '01710000002');
INSERT INTO administrator VALUES (seq_administrator.NEXTVAL, 'Farzana Akter', 'farzana.admin@example.edu', '01710000003');
INSERT INTO administrator VALUES (seq_administrator.NEXTVAL, 'Mahmud Karim', 'mahmud.admin@example.edu', '01710000004');
INSERT INTO administrator VALUES (seq_administrator.NEXTVAL, 'Sharmeen Sultana', 'sharmeen.admin@example.edu', '01710000005');

-- CAREER FAIR
INSERT INTO career_fair VALUES (seq_career_fair.NEXTVAL,1,'Annual Career Fair 2026',TRUNC(SYSDATE)+30,'University Campus',TRUNC(SYSDATE)+30+9/24,TRUNC(SYSDATE)+30+17/24,'Annual university career fair',TRUNC(SYSDATE)+20);
INSERT INTO career_fair VALUES (seq_career_fair.NEXTVAL,2,'Technology Internship Fair',TRUNC(SYSDATE)+45,'Multipurpose Hall',TRUNC(SYSDATE)+45+10/24,TRUNC(SYSDATE)+45+16/24,'Internship-focused technology fair',TRUNC(SYSDATE)+35);
INSERT INTO career_fair VALUES (seq_career_fair.NEXTVAL,3,'Business and Finance Career Expo',TRUNC(SYSDATE)+60,'Auditorium',TRUNC(SYSDATE)+60+9/24,TRUNC(SYSDATE)+60+16/24,'Recruitment event for business and finance students',TRUNC(SYSDATE)+50);
INSERT INTO career_fair VALUES (seq_career_fair.NEXTVAL,4,'Engineering Career Day',TRUNC(SYSDATE)+75,'Engineering Building',TRUNC(SYSDATE)+75+9/24,TRUNC(SYSDATE)+75+17/24,'Career event for engineering graduates',TRUNC(SYSDATE)+65);
INSERT INTO career_fair VALUES (seq_career_fair.NEXTVAL,5,'Graduate Recruitment Fair',TRUNC(SYSDATE)+90,'University Convention Center',TRUNC(SYSDATE)+90+10/24,TRUNC(SYSDATE)+90+17/24,'General graduate recruitment event',TRUNC(SYSDATE)+80);

-- STUDENT
INSERT INTO student VALUES (seq_student.NEXTVAL,'Arif Hasan','arif@student.example.edu','01810000001','Computer Science',2027,'student1_resume.pdf');
INSERT INTO student VALUES (seq_student.NEXTVAL,'Sadia Islam','sadia@student.example.edu','01810000002','Software Engineering',2026,'student2_resume.pdf');
INSERT INTO student VALUES (seq_student.NEXTVAL,'Nafis Ahmed','nafis@student.example.edu','01810000003','Information Systems',2027,'student3_resume.pdf');
INSERT INTO student VALUES (seq_student.NEXTVAL,'Maliha Noor','maliha@student.example.edu','01810000004','Electrical Engineering',2026,'student4_resume.pdf');
INSERT INTO student VALUES (seq_student.NEXTVAL,'Rafi Khan','rafi@student.example.edu','01810000005','Business Administration',2027,'student5_resume.pdf');

-- SKILL
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Oracle SQL');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'PL/SQL');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Java');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Data Analysis');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Communication');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Networking');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Project Management');
INSERT INTO skill VALUES (seq_skill.NEXTVAL,'Financial Analysis');

-- STUDENT SKILL
INSERT INTO student_skill VALUES (1,1);
INSERT INTO student_skill VALUES (1,2);
INSERT INTO student_skill VALUES (1,3);
INSERT INTO student_skill VALUES (2,3);
INSERT INTO student_skill VALUES (2,5);
INSERT INTO student_skill VALUES (3,4);
INSERT INTO student_skill VALUES (3,5);
INSERT INTO student_skill VALUES (4,6);
INSERT INTO student_skill VALUES (4,7);
INSERT INTO student_skill VALUES (5,5);
INSERT INTO student_skill VALUES (5,7);
INSERT INTO student_skill VALUES (5,8);

-- FAIR REGISTRATION
INSERT INTO fair_registration VALUES (1,1,TRUNC(SYSDATE)-5,'REGISTERED');
INSERT INTO fair_registration VALUES (2,1,TRUNC(SYSDATE)-4,'ATTENDED');
INSERT INTO fair_registration VALUES (3,1,TRUNC(SYSDATE)-3,'REGISTERED');
INSERT INTO fair_registration VALUES (4,2,TRUNC(SYSDATE)-4,'REGISTERED');
INSERT INTO fair_registration VALUES (5,2,TRUNC(SYSDATE)-3,'ATTENDED');
INSERT INTO fair_registration VALUES (1,2,TRUNC(SYSDATE)-2,'REGISTERED');
INSERT INTO fair_registration VALUES (2,3,TRUNC(SYSDATE)-2,'REGISTERED');
INSERT INTO fair_registration VALUES (3,3,TRUNC(SYSDATE)-1,'REGISTERED');
INSERT INTO fair_registration VALUES (4,4,TRUNC(SYSDATE)-1,'REGISTERED');
INSERT INTO fair_registration VALUES (5,5,TRUNC(SYSDATE),'REGISTERED');

-- COMPANY
INSERT INTO company VALUES (seq_company.NEXTVAL,'TechNova Ltd.','Software','City Center','career@technova.example','02-9000001','https://technova.example');
INSERT INTO company VALUES (seq_company.NEXTVAL,'DataBridge Analytics','Data Analytics','City Center','jobs@databridge.example','02-9000002','https://databridge.example');
INSERT INTO company VALUES (seq_company.NEXTVAL,'FinEdge Services','Financial Services','City Center','hr@finedge.example','02-9000003','https://finedge.example');
INSERT INTO company VALUES (seq_company.NEXTVAL,'NetWorks Solutions','Telecommunication','City Center','talent@networks.example','02-9000004','https://networks.example');
INSERT INTO company VALUES (seq_company.NEXTVAL,'PeopleFirst Ltd.','Human Resources','City Center','career@peoplefirst.example','02-9000005','https://peoplefirst.example');

-- COMPANY PARTICIPATION
INSERT INTO company_participation VALUES (1,1,TRUNC(SYSDATE)-10,'APPROVED');
INSERT INTO company_participation VALUES (2,1,TRUNC(SYSDATE)-9,'APPROVED');
INSERT INTO company_participation VALUES (3,1,TRUNC(SYSDATE)-8,'PENDING');
INSERT INTO company_participation VALUES (4,2,TRUNC(SYSDATE)-8,'APPROVED');
INSERT INTO company_participation VALUES (5,2,TRUNC(SYSDATE)-7,'APPROVED');
INSERT INTO company_participation VALUES (1,2,TRUNC(SYSDATE)-6,'APPROVED');
INSERT INTO company_participation VALUES (2,3,TRUNC(SYSDATE)-5,'APPROVED');
INSERT INTO company_participation VALUES (3,3,TRUNC(SYSDATE)-4,'APPROVED');
INSERT INTO company_participation VALUES (4,4,TRUNC(SYSDATE)-3,'APPROVED');
INSERT INTO company_participation VALUES (5,5,TRUNC(SYSDATE)-2,'APPROVED');

-- REPRESENTATIVE
INSERT INTO company_representative VALUES (seq_representative.NEXTVAL,1,'Farhan Karim','HR Manager','farhan@technova.example','01720000001');
INSERT INTO company_representative VALUES (seq_representative.NEXTVAL,2,'Tasnia Rahman','Talent Acquisition Lead','tasnia@databridge.example','01720000002');
INSERT INTO company_representative VALUES (seq_representative.NEXTVAL,3,'Adnan Chowdhury','Recruitment Manager','adnan@finedge.example','01720000003');
INSERT INTO company_representative VALUES (seq_representative.NEXTVAL,4,'Sara Ahmed','HR Business Partner','sara@networks.example','01720000004');
INSERT INTO company_representative VALUES (seq_representative.NEXTVAL,5,'Kabir Hossain','Senior Recruiter','kabir@peoplefirst.example','01720000005');

-- BOOTH
INSERT INTO booth VALUES (seq_booth.NEXTVAL,1,'A-01','Ground Floor East','ASSIGNED');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,1,'A-02','Ground Floor East','ASSIGNED');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,1,'A-03','Ground Floor West','AVAILABLE');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,2,'B-01','First Floor North','ASSIGNED');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,2,'B-02','First Floor North','ASSIGNED');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,2,'B-03','First Floor South','ASSIGNED');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,3,'C-01','Auditorium Lobby','AVAILABLE');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,3,'C-02','Auditorium Lobby','AVAILABLE');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,4,'D-01','Engineering Lobby','AVAILABLE');
INSERT INTO booth VALUES (seq_booth.NEXTVAL,5,'E-01','Convention Hall','AVAILABLE');

-- BOOTH ASSIGNMENT
INSERT INTO booth_assignment VALUES (1,1,TRUNC(SYSDATE)-4);
INSERT INTO booth_assignment VALUES (2,2,TRUNC(SYSDATE)-4);
INSERT INTO booth_assignment VALUES (4,4,TRUNC(SYSDATE)-3);
INSERT INTO booth_assignment VALUES (5,5,TRUNC(SYSDATE)-3);
INSERT INTO booth_assignment VALUES (6,1,TRUNC(SYSDATE)-2);

-- JOB VACANCY
INSERT INTO job_vacancy VALUES (seq_job.NEXTVAL,1,1,'Junior Database Developer','FULL_TIME','Develop and maintain Oracle database modules.',TRUNC(SYSDATE)+15,'OPEN');
INSERT INTO job_vacancy VALUES (seq_job.NEXTVAL,2,1,'Software Engineering Intern','INTERNSHIP','Assist the application development team.',TRUNC(SYSDATE)+16,'OPEN');
INSERT INTO job_vacancy VALUES (seq_job.NEXTVAL,4,2,'Network Engineering Intern','INTERNSHIP','Support network operations and monitoring.',TRUNC(SYSDATE)+25,'OPEN');
INSERT INTO job_vacancy VALUES (seq_job.NEXTVAL,5,2,'HR Graduate Trainee','FULL_TIME','Support recruitment and employee engagement.',TRUNC(SYSDATE)+26,'OPEN');
INSERT INTO job_vacancy VALUES (seq_job.NEXTVAL,2,3,'Business Data Analyst','FULL_TIME','Prepare business dashboards and analytical reports.',TRUNC(SYSDATE)+40,'OPEN');

-- JOB REQUIRED SKILL
INSERT INTO job_required_skill VALUES (1,1);
INSERT INTO job_required_skill VALUES (1,2);
INSERT INTO job_required_skill VALUES (2,3);
INSERT INTO job_required_skill VALUES (2,5);
INSERT INTO job_required_skill VALUES (3,6);
INSERT INTO job_required_skill VALUES (3,5);
INSERT INTO job_required_skill VALUES (4,5);
INSERT INTO job_required_skill VALUES (4,7);
INSERT INTO job_required_skill VALUES (5,4);
INSERT INTO job_required_skill VALUES (5,8);

-- APPLICATION
INSERT INTO application VALUES (seq_application.NEXTVAL,1,1,TRUNC(SYSDATE)-2,'arif_resume.pdf','Interested in the database developer role.','SHORTLISTED');
INSERT INTO application VALUES (seq_application.NEXTVAL,2,2,TRUNC(SYSDATE)-2,'sadia_resume.pdf','Interested in the software internship.','UNDER_REVIEW');
INSERT INTO application VALUES (seq_application.NEXTVAL,3,1,TRUNC(SYSDATE)-1,'nafis_resume.pdf','Applying for the database role.','SUBMITTED');
INSERT INTO application VALUES (seq_application.NEXTVAL,4,3,TRUNC(SYSDATE)-1,'maliha_resume.pdf','Interested in networking and infrastructure.','SHORTLISTED');
INSERT INTO application VALUES (seq_application.NEXTVAL,5,4,TRUNC(SYSDATE),'rafi_resume.pdf','Interested in graduate trainee role.','SHORTLISTED');

-- INTERVIEW
INSERT INTO interview VALUES (seq_interview.NEXTVAL,1,1,TRUNC(SYSDATE)+20,'10:00','Room 401','Technical','SCHEDULED','PENDING');
INSERT INTO interview VALUES (seq_interview.NEXTVAL,1,1,TRUNC(SYSDATE)+22,'14:00','Room 402','HR','SCHEDULED','PENDING');
INSERT INTO interview VALUES (seq_interview.NEXTVAL,4,4,TRUNC(SYSDATE)+30,'11:00','Online Link','Technical','SCHEDULED','PENDING');
INSERT INTO interview VALUES (seq_interview.NEXTVAL,4,4,TRUNC(SYSDATE)+32,'15:00','Room 501','Final','SCHEDULED','PENDING');
INSERT INTO interview VALUES (seq_interview.NEXTVAL,5,5,TRUNC(SYSDATE)+31,'12:00','Room 301','HR','SCHEDULED','PENDING');

-- FAIR SESSION
INSERT INTO fair_session VALUES (seq_session.NEXTVAL,1,'CV Writing for Fresh Graduates','Dr. Rafiq Ahmed','Auditorium',TRUNC(SYSDATE)+30+11/24);
INSERT INTO fair_session VALUES (seq_session.NEXTVAL,1,'Interview Preparation','Ms. Tania Kabir','Seminar Room 1',TRUNC(SYSDATE)+30+14/24);
INSERT INTO fair_session VALUES (seq_session.NEXTVAL,2,'Careers in Technology','Mr. Hasan Ali','Multipurpose Hall',TRUNC(SYSDATE)+45+12/24);
INSERT INTO fair_session VALUES (seq_session.NEXTVAL,3,'Careers in Finance and Analytics','Ms. Farah Noor','Auditorium',TRUNC(SYSDATE)+60+12/24);
INSERT INTO fair_session VALUES (seq_session.NEXTVAL,4,'Engineering Innovation Careers','Engr. Kamal Uddin','Engineering Building',TRUNC(SYSDATE)+75+13/24);

-- SESSION ATTENDANCE
INSERT INTO session_attendance VALUES (1,1,'REGISTERED',NULL);
INSERT INTO session_attendance VALUES (2,1,'ATTENDED',TRUNC(SYSDATE)+30+10.9/24);
INSERT INTO session_attendance VALUES (3,2,'REGISTERED',NULL);
INSERT INTO session_attendance VALUES (4,3,'REGISTERED',NULL);
INSERT INTO session_attendance VALUES (5,3,'ATTENDED',TRUNC(SYSDATE)+45+11.9/24);

-- FEEDBACK
INSERT INTO feedback VALUES (seq_feedback.NEXTVAL,1,1,TRUNC(SYSDATE)+31,5,'Excellent fair and useful sessions.');
INSERT INTO feedback VALUES (seq_feedback.NEXTVAL,2,1,TRUNC(SYSDATE)+31,4,'Good employer participation.');
INSERT INTO feedback VALUES (seq_feedback.NEXTVAL,3,1,TRUNC(SYSDATE)+31,4,'The event was well organized.');
INSERT INTO feedback VALUES (seq_feedback.NEXTVAL,4,2,TRUNC(SYSDATE)+46,5,'Very useful technology sessions.');
INSERT INTO feedback VALUES (seq_feedback.NEXTVAL,5,2,TRUNC(SYSDATE)+46,4,'Helpful for graduate job preparation.');

-- SYSTEM AUDIT
INSERT INTO system_audit VALUES (seq_system_audit.NEXTVAL,'SYSTEM','Initial schema verification',SYSDATE,USER);
INSERT INTO system_audit VALUES (seq_system_audit.NEXTVAL,'ADMINISTRATOR','Initial sample data loaded',SYSDATE,USER);
INSERT INTO system_audit VALUES (seq_system_audit.NEXTVAL,'STUDENT','Initial sample data loaded',SYSDATE,USER);
INSERT INTO system_audit VALUES (seq_system_audit.NEXTVAL,'COMPANY','Initial sample data loaded',SYSDATE,USER);
INSERT INTO system_audit VALUES (seq_system_audit.NEXTVAL,'CAREER_FAIR','Initial sample data loaded',SYSDATE,USER);

COMMIT;

-- ============================================================
-- 4) QUICK VERIFICATION
-- ============================================================
SELECT COUNT(*) AS students FROM student;
SELECT COUNT(*) AS fairs FROM career_fair;
SELECT COUNT(*) AS companies FROM company;
SELECT COUNT(*) AS applications FROM application;
SELECT COUNT(*) AS interviews FROM interview;
SELECT COUNT(*) AS feedback_rows FROM feedback;
