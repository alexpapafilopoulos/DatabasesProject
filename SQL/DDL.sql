DROP SCHEMA IF EXISTS `library`;
CREATE SCHEMA `library` ;

USE library;

CREATE TABLE schools(
school_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
school_name VARCHAR(50) NOT NULL,
street_name VARCHAR(50) NOT NULL,
apt_number INT UNSIGNED NOT NULL,
city VARCHAR(50) NOT NULL,
state VARCHAR(50) NOT NULL,
zip_code INT UNSIGNED NOT NULL CHECK(length(zip_code)=5),
email_address VARCHAR(50) NOT NULL UNIQUE,
PRIMARY KEY(school_id)
)ENGINE=InnoDB;

CREATE TABLE books(
book_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
isbn VARCHAR(13) NOT NULL UNIQUE CHECK (length(isbn)=13),
title VARCHAR(50) NOT NULL,
pages INT UNSIGNED NOT NULL,
summary VARCHAR(1000) NOT NULL,
book_language VARCHAR(50) NOT NULL,
picture LONGBLOB,
PRIMARY KEY(book_id)
)ENGINE=InnoDB;

CREATE TABLE users(
user_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
username VARCHAR(25) NOT NULL UNIQUE,
first_name VARCHAR(25) NOT NULL,
last_name VARCHAR(25) NOT NULL,
pass VARCHAR(25) NOT NULL,
user_role ENUM ("student","teacher","handler","principal","manager") NOT NULL,
date_of_birth date NOT NULL,
school_id INT UNSIGNED NOT NULL,
activ INT CHECK (activ>=0 AND activ<=3),
PRIMARY KEY(user_id),
CONSTRAINT fk_school_id FOREIGN KEY(school_id) REFERENCES schools (school_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE lends(
user_id INT UNSIGNED NOT NULL,
book_id INT UNSIGNED NOT NULL,
lend_date date NOT NULL,
return_date date,
handler_id INT UNSIGNED NOT NULL,
PRIMARY KEY(user_id,book_id),
CONSTRAINT fk_luser_id FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
CONSTRAINT fk_lbook_id FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_lhandler_id FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK(lend_date <= return_date) 
)ENGINE=InnoDB;

CREATE TABLE bookings(
user_id INT UNSIGNED NOT NULL,
book_id INT UNSIGNED NOT NULL,
booking_date date NOT NULL,
PRIMARY KEY(user_id,book_id),
CONSTRAINT fk_buser_id FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
CONSTRAINT fk_bbook_id FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE  CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE phone_number(
school_id INT UNSIGNED NOT NULL,
ph_number CHAR(10) NOT NULL UNIQUE CHECK(length(ph_number)=10),
PRIMARY KEY(school_id,ph_number),
CONSTRAINT fk_phschool_id FOREIGN KEY (school_id) REFERENCES schools (school_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;


CREATE TABLE rates(
user_id INT UNSIGNED NOT NULL,
book_id INT UNSIGNED NOT NULL,
review VARCHAR(1000) ,
likert INT UNSIGNED NOT NULL CHECK (likert>0 and likert<6),
approved INT UNSIGNED NOT NULL CHECK (approved>=0 and approved<=1),
PRIMARY KEY(user_id,book_id),
CONSTRAINT fk_ruser_id FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
CONSTRAINT fk_rbook_id FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;




CREATE TABLE has(
book_id INT UNSIGNED NOT NULL,
school_id INT UNSIGNED NOT NULL,
quantity INT UNSIGNED NOT NULL,
PRIMARY KEY(school_id,book_id),
CONSTRAINT fk_hschool_id FOREIGN KEY (school_id) REFERENCES schools (school_id) ON DELETE RESTRICT ON UPDATE CASCADE,
CONSTRAINT fk_hbook_id FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE author(
author_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
author_first_name VARCHAR(25) NOT NULL,
author_last_name VARCHAR(25) NOT NULL,
PRIMARY KEY (author_id)
)ENGINE=InnoDB;

CREATE TABLE category(
category_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
category_name VARCHAR(25) NOT NULL UNIQUE,
PRIMARY KEY (category_id)
)ENGINE=InnoDB;

CREATE TABLE publisher(
publisher_id INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
publisher_name VARCHAR(25) NOT NULL UNIQUE,
PRIMARY KEY (publisher_id)
)ENGINE=InnoDB;

CREATE TABLE key_words(
book_id INT UNSIGNED NOT NULL,
word VARCHAR(15) NOT NULL,
PRIMARY KEY(book_id,word),
CONSTRAINT fk_wbook_id FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE writes(
book_id INT UNSIGNED NOT NULL,
author_id INT UNSIGNED NOT NULL,
PRIMARY KEY(book_id,author_id),
CONSTRAINT fk_abook_id FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_author_id FOREIGN KEY (author_id) REFERENCES author(author_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE publishes(
book_id INT UNSIGNED NOT NULL,
publisher_id INT UNSIGNED NOT NULL,
PRIMARY KEY(book_id,publisher_id),
CONSTRAINT fk_pbook_id FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_publisher_id FOREIGN KEY (publisher_id) REFERENCES publisher(publisher_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE TABLE belongs(
book_id INT UNSIGNED NOT NULL,
category_id INT UNSIGNED NOT NULL,
PRIMARY KEY(book_id,category_id),
CONSTRAINT fk_belbook_id FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_category_id FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE INDEX idx_userinfo ON users(username,first_name,last_name);
CREATE INDEX idx_book_title ON books(title);
CREATE INDEX idx_authorinfo ON author(author_first_name,author_last_name);
CREATE INDEX idx_category_name ON category(category_name);

