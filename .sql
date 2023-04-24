-- Active: 1679647930098@@127.0.0.1@3306@school

DROP DATABASE school;

CREATE DATABASE school DEFAULT CHARACTER SET = "utf8mb4";

CREATE TABLE student (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255),
    last_name VARCHAR(255)
);

CREATE TABLE subject (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    short VARCHAR(255),
    name TEXT,
    rule TEXT
);

DESC subject;

INSERT INTO subject (short, name) VALUES 
    ("M", "Matematika"),
    ("Aj", "Anglický jazyk"),
    ("Čj", "Český jazyk"),
    ("Bi", "Biologie"),
    ("Ch", "Chemie"),
    ("Fy", "Fyzika")
;

CREATE TABLE `group` (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT
    subject_id INT UNSIGNED
);

--ALTER TABLE `group` ADD COLUMN subject_id INT UNSIGNED;

UPDATE `group` SET subject_id=2 WHERE id=2;

SELECT * FROM `group`;

CREATE TABLE group_student (
    group_id INT UNSIGNED,
    student_id INT UNSIGNED,
    PRIMARY KEY (group_id, student_id),
    FOREIGN KEY (group_id) REFERENCES `group`(id),
    FOREIGN KEY (student_id) REFERENCES student(id)
);

CREATE TABLE grade (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    group_id INT UNSIGNED,
    student_id INT UNSIGNED,
    `value` VARCHAR(255),
    max_value VARCHAR(255),
    desciption TEXT,
    `timestamp` TEXT,
    FOREIGN KEY (group_id, student_id) REFERENCES group_student(group_id, student_id)
);

CREATE TABLE teacher (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    short VARCHAR(255),
    title VARCHAR(255),
    first_name VARCHAR(255),
    last_name VARCHAR(255)
);

CREATE TABLE group_teacher (
    group_id INT UNSIGNED,
    teacher_id INT UNSIGNED,
    PRIMARY KEY (group_id, teacher_id),
    FOREIGN KEY (group_id) REFERENCES `group`(id),
    FOREIGN KEY (teacher_id) REFERENCES teacher(id)
);

--sample data
INSERT INTO teacher (short, first_name, last_name) VALUES 
    ("Zi", "Valérie", "Zítková"),
    ("St", "Ema", "Stašková"),
    ("Mi", "Ljuba", "Michalčíková"),
    ("Bu", "Hanuš", "Burget"),
    ("Kr", "Věnceslav", "Krpec"),
    ("Vy", "Radim", "Vymětal")
;

SELECT * FROM teacher;

INSERT INTO student (first_name, last_name) VALUES 
    ("David", "Cajthaml"),
    ("Vratislav", "Strejček"),
    ("Robin", "Valta"),
    ("Žofie", "Berková"),
    ("Anastázie", "Šubová"),
    ("Lenka", "Macečková")
;
INSERT INTO student (first_name, last_name) VALUES 
    ("Marcelos", "Marcelek")
;

SELECT * FROM student;

INSERT INTO `group` (subject_id) VALUES (1), (2), (2), (2), (3), (3), (4), (4), (5);

INSERT INTO group_teacher (group_id, teacher_id) VALUES
    (1, 1),
    (2, 2),
    (3, 1),
    (4, 2),
    (5, 2),
    (6, 2)
;

SELECT * FROM group_teacher;
SELECT * FROM `group`;

INSERT INTO group_student (group_id, student_id) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (2, 4),
    (2, 5),
    (2, 6)
;

SHOW CREATE TABLE group_student;

SELECT * FROM group_student LEFT OUTER JOIN `group` ON group_student.group_id=`group`.id
    LEFT OUTER JOIN student ON group_student.student_id=student.id;

SELECT student.first_name AS student_first_name, student.last_name AS student_last_name, teacher.first_name AS teacher_first_name, teacher.last_name AS teacher_last_name FROM student
    LEFT OUTER JOIN `group_student` ON group_student.student_id=student.id
    LEFT OUTER JOIN `group` ON group_student.group_id=`group`.id
    LEFT OUTER JOIN group_teacher ON group_teacher.group_id=`group`.id
    LEFT OUTER JOIN teacher ON group_teacher.teacher_id=teacher.id;

--ucitel-studenti
SELECT
    teacher.id AS t_id,
    teacher.first_name AS teacher_first_name,
    teacher.last_name AS teacher_last_name,
    student.id AS s_id,
    student.first_name AS student_first_name,
    student.last_name AS student_last_name
    FROM teacher
    JOIN group_teacher ON teacher.id=group_teacher.teacher_id AND teacher.id=1
    JOIN `group` ON group_teacher.group_id=`group`.id
    JOIN group_student ON `group`.id=group_student.group_id
    JOIN student ON group_student.student_id=student.id;

--pokus o ucitel-student-s-predmety
SELECT *
    teacher.id AS teacher_id,
    teacher.first_name AS teacher_first_name,
    teacher.last_name AS teacher_last_name,
    student.id AS student_id,
    student.first_name AS student_first_name,
    student.last_name AS student_last_name,
    subject.id AS subject_id,
    subject.short AS subject_short,
    subject.name AS subject_name
    FROM teacher
    JOIN group_teacher ON teacher.id=group_teacher.teacher_id --AND teacher.id=1
    JOIN `group` ON group_teacher.group_id=`group`.id
    JOIN group_student ON `group`.id=group_student.group_id
    JOIN student ON group_student.student_id=student.id
    JOIN subject ON `group`.subject_id=subject.id;

