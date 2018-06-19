DROP DATABASE DICT;
CREATE DATABASE DICT;


CREATE TABLE EMPLOYEE(
	EMP_NO CHAR(7) NOT NULL UNIQUE,
	EMP_FIRSTNAME VARCHAR(20) NOT NULL,
	EMP_LASTNAME VARCHAR(20) NOT NULL,
	EMP_MIDDLE VARCHAR(20) NOT NULL,
	EMP_POSITION VARCHAR(50) NOT NULL,
	EMP_SALARY DECIMAL(2) NOT NULL,
	EMP_VACLEAVE_BAL INT DEFAULT 0,
	EMP_SICKLEAVE_BAL INT DEFAULT 0,
	EMP_UNDERTIME TIME,
	EMP_NOPAY DECIMAL(3) DEFAULT 0,
	PRIMARY KEY (EMP_NO)
);

CREATE TABLE LEAVES(
	LEAVE_ID INT NOT NULL UNIQUE,
	LEAVE_TYPE CHAR(1) NOT NULL,
	LEAVE_EMPLOYEE CHAR(7) NOT NULL,
	LEAVE_FROMDATE DATE NOT NULL,
	LEAVE_TODATE DATE NOT NULL,
	LEAVE_DAYS INT NOT NULL,
	PRIMARY KEY (LEAVE_ID),
	FOREIGN KEY (LEAVE_EMPLOYEE) REFERENCES EMPLOYEE (EMP_NO)
);
