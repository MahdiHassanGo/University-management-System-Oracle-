-- Career Fair Management System - PHP Web Support for Oracle 10g XE
-- Run this ONCE while logged in as CF_OWNER.
-- This does NOT replace or recreate the 18 academic tables.

CREATE TABLE app_user (
    user_id NUMBER PRIMARY KEY,
    username VARCHAR2(120) UNIQUE NOT NULL,
    password_hash VARCHAR2(255) NOT NULL,
    role VARCHAR2(30) NOT NULL,
    related_id NUMBER NOT NULL,
    display_name VARCHAR2(120) NOT NULL,
    is_active NUMBER(1) DEFAULT 1 NOT NULL,
    CONSTRAINT ck_app_user_role CHECK (role IN ('ADMIN','STUDENT','COMPANY_REP')),
    CONSTRAINT ck_app_user_active CHECK (is_active IN (0,1))
);

CREATE SEQUENCE seq_app_user START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

INSERT INTO app_user VALUES (
    seq_app_user.NEXTVAL,
    'admin@example.com',
    '$2y$12$Brd1oZfWi.VBQ0TiaW1MuuRmMp1GGXvrpzXfkn/d7aGMzCU/5UU8a',
    'ADMIN', 1, 'Administrator', 1
);

INSERT INTO app_user VALUES (
    seq_app_user.NEXTVAL,
    'student@example.com',
    '$2y$12$4Lln6uJmTdeNov7RzKJmP.X3nDGNPeOdqQvtqgX6.raqCUgMcw7Bm',
    'STUDENT', 1, 'Demo Student', 1
);

INSERT INTO app_user VALUES (
    seq_app_user.NEXTVAL,
    'company@example.com',
    '$2y$12$JWYxi5MLW8VpM.rxsL5rW.XYxrmwqI59RflBhCIXKBJGnfgGt.zwa',
    'COMPANY_REP', 1, 'Company Representative', 1
);

COMMIT;

SELECT user_id, username, role, related_id, display_name, is_active
FROM app_user
ORDER BY user_id;
