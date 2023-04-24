-- Active: 1679647930098@@127.0.0.1@3306@user_accounts

DROP DATABASE user_accounts;

CREATE DATABASE user_accounts DEFAULT CHARACTER SET = "utf8mb4";

--table for user accounts
CREATE TABLE user_account (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255),
    username VARCHAR(255),
    `password` VARCHAR(255),
    `timestamp` TEXT,
    user_type VARCHAR(255),
    `user_id` INT UNSIGNED
);

INSERT INTO user_account (email, username, `password`, user_type, user_id) VALUES 
    ("david@example.com", "davidek", "cici123", "student", 1)
;

INSERT INTO user_account (email, username, `password`, user_type, user_id) VALUES 
    ("ema@example.com", "staska", "emasta", "teacher", 2)
;

INSERT INTO user_account (email, username, `password`, user_type, user_id) VALUES 
    ("marcel_na_googlu@gmail.com", "marcel", "mrci", "student", 7),
    ("vrtacka@example.com", "vrtik", "vvv", "student", 2),
    ("valca@example.com", "valca", "vali", "teacher", 1)
;

SELECT * FROM user_account WHERE username="davidek" AND password="cici123";