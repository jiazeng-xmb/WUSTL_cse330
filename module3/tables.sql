mysql> show tables;
+---------------------+
| Tables_in_mod3Group |
+---------------------+
| comments            |
| likes               |
| stories             |
| users               |
+---------------------+
4 rows in set (0.00 sec)



create table code:

CREATE TABLE students(
    id mediumint(10) unsigned not null,
    first_name varchar(50) not null,
	last_name varchar(50) not null,
    email_address varchar(50) not null,
    primary key (id)
)engine = InnoDB default character set = utf8 collate = utf8_general_ci;

CREATE TABLE departments(
    school_code enum('L','B','A','F','E','T','I','W','S','U','M') not null DEFAULT 'L',
    dept_id tinyint(3) unsigned NOT NULL,
    abbreviation varchar(9) NOT NULL,
    dept_name varchar(200) NOT NULL,
    PRIMARY KEY (school_code,dept_id)
)engine = InnoDB default character set = utf8 collate = utf8_general_ci;

CREATE TABLE courses(
    school_code enum('L','B','A','F','E','T','I','W','S','U','M') not null DEFAULT 'L',
    dept_id tinyint(3) unsigned NOT NULL,
    course_code char(5) NOT NULL,
    name varchar(150) NOT NULL,
    PRIMARY KEY (school_code,dept_id, course_code),
    FOREIGN KEY(school_code,dept_id) REFERENCES departments(school_code,dept_id)
)engine = InnoDB default character set = utf8 collate = utf8_general_ci;

CREATE TABLE grades(
    pk_grade_ID int(10) unsigned NOT NULL AUTO_INCREMENT,
    student_id mediumint(10) unsigned not null,
    grade decimal(5,2) DEFAULT NULL,
    school_code enum('L','B','A','F','E','T','I','W','S','U','M') not null DEFAULT 'L',
    dept_id tinyint(3) unsigned NOT NULL,
    course_code char(5) NOT NULL,
    PRIMARY KEY(pk_grade_ID),
    FOREIGN KEY (student_id) REFERENCES students (id),
    FOREIGN KEY(school_code, dept_id, course_code) REFERENCES courses(school_code, dept_id, course_code)
)engine = InnoDB default character set = utf8 collate = utf8_general_ci;