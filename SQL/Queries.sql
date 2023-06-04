USE library;
/* USER QUERIES */

/*3.3.1*/

DELIMITER //
CREATE PROCEDURE `SchoolBooks`(IN usern varchar(50))
BEGIN
CREATE TEMPORARY TABLE school_books
(title varchar(50),author_last_name varchar(50), category_name varchar(50),quantity INT UNSIGNED)
AS
SELECT bk.title , aw.author_last_name , cbe.category_name, bk.quantity   FROM (
SELECT b.title , b.book_id, k.quantity FROM books as b INNER JOIN (
SELECT h.book_id,h.quantity FROM has as h  INNER JOIN (
SELECT u.school_id FROM users as u WHERE username = usern)
as m
ON m.school_id = h.school_id) as k
ON k.book_id = b.book_id) as bk
INNER JOIN ( SELECT a.author_last_name, w.book_id FROM (author a INNER JOIN writes w ON a.author_id = w.author_id)) as aw
ON aw.book_id = bk.book_id
INNER JOIN (SELECT c.category_name, be.book_id FROM (category c INNER JOIN belongs be ON c.category_id = be.category_id)) as cbe 
ON aw.book_id = cbe.book_id;
END;
//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `FilteredSchoolBooks`(IN titl varchar(50),IN authorn varchar(50),IN cat varchar(50))
BEGIN
IF titl="" AND authorn ="" AND cat ='All' THEN
SELECT * FROM school_books
GROUP BY title;

ELSEIF  titl ="" AND authorn ="" THEN
SELECT * FROM school_books  WHERE category_name = cat
GROUP BY title;

ELSEIF cat='All' AND titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn
GROUP BY title;

ELSEIF cat='All' AND authorn =""THEN
SELECT * FROM school_books  WHERE title = titl
GROUP BY title;

ELSEIF titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND category_name=cat
GROUP BY title;

ELSEIF authorn ="" THEN
SELECT * FROM school_books  WHERE title = titl AND category_name=cat
GROUP BY title;

ELSEIF cat='All' THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND title=titl
GROUP BY title;

ELSE
SELECT * FROM school_books  WHERE author_last_name = authorn AND title=titl AND category_name=cat
GROUP BY title;
END IF;
END; //
DELIMITER ;

/*3.3.2*/
DELIMITER //
CREATE PROCEDURE `MyLoans` (IN usern VARCHAR(50))
BEGIN
SELECT b.title,k.lend_date,k.return_date
FROM books b INNER JOIN
(SELECT l.book_id,l.return_date,l.lend_date FROM lends l INNER JOIN
(SELECT user_id from users WHERE (username=usern))
AS f
ON f.user_id=l.user_id
)AS k
ON b.book_id=k.book_id;
END;
//
DELIMITER ;

/*HANDLER QUERIES*/

/*3.2.1*/

DELIMITER //
CREATE PROCEDURE `AvailableFilteredSchoolBooks`(IN titl varchar(50),IN authorn varchar(50),IN cat varchar(50),IN availability varchar(50))
BEGIN
IF availability="Everything" THEN
IF titl="" AND authorn ="" AND cat ='All' THEN
SELECT * FROM school_books
GROUP BY title;

ELSEIF  titl ="" AND authorn ="" THEN
SELECT * FROM school_books  WHERE category_name = cat
GROUP BY title;

ELSEIF cat='All' AND titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn
GROUP BY title;

ELSEIF cat='All' AND authorn =""THEN
SELECT * FROM school_books  WHERE title = titl
GROUP BY title;

ELSEIF titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND category_name=cat
GROUP BY title;

ELSEIF authorn ="" THEN
SELECT * FROM school_books  WHERE title = titl AND category_name=cat
GROUP BY title;

ELSEIF cat='All' THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND title=titl
GROUP BY title;

ELSE
SELECT * FROM school_books  WHERE author_last_name = authorn AND title=titl AND category_name=cat
GROUP BY title;
END IF;

ELSE
IF titl="" AND authorn ="" AND cat ='All' THEN
SELECT * FROM school_books WHERE quantity>=1
GROUP BY title;

ELSEIF  titl ="" AND authorn ="" THEN
SELECT * FROM school_books  WHERE category_name = cat AND quantity>=1
GROUP BY title;

ELSEIF cat='All' AND titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND quantity>=1
GROUP BY title;

ELSEIF cat='All' AND authorn =""THEN
SELECT * FROM school_books  WHERE title = titl AND quantity>=1
GROUP BY title;

ELSEIF titl ="" THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND category_name=cat AND quantity>=1
GROUP BY title;

ELSEIF authorn ="" THEN
SELECT * FROM school_books  WHERE title = titl AND category_name=cat AND quantity>=1
GROUP BY title;

ELSEIF cat='All' THEN
SELECT * FROM school_books  WHERE author_last_name = authorn AND title=titl AND quantity>=1
GROUP BY title;

END IF;
END IF;
END; //
DELIMITER ;

/*3.2.2*/
DELIMITER //
CREATE PROCEDURE Delayeded(IN handler_username VARCHAR(50),IN student_username VARCHAR(50))
BEGIN
IF student_username ="" THEN
SELECT u.user_id,u.first_name,u.last_name,k.book_id,(DATEDIFF(NOW(), lend_date)-7) as Delay FROM users as u INNER JOIN(
SELECT l.user_id,l.lend_date,l.book_id FROM lends as l WHERE (return_date IS NULL AND lend_date <= DATE_SUB(NOW(), INTERVAL 1 WEEK))
)AS k
ON k.user_id=u.user_id
INNER JOIN users ub ON ub.school_id=u.school_id WHERE ub.username=handler_username ;

ELSE
SELECT u.username,u.user_id,u.first_name,u.last_name,k.book_id,(DATEDIFF(NOW(), lend_date)-7) as Delay FROM users as u INNER JOIN(
SELECT l.user_id,l.lend_date,l.book_id FROM lends as l WHERE (return_date IS NULL AND lend_date <= DATE_SUB(NOW(), INTERVAL 1 WEEK))
)AS k
ON k.user_id=u.user_id
INNER JOIN users ub ON ub.school_id=u.school_id AND u.username=student_username WHERE ub.username=handler_username ;
END IF;

END
 // 
 DELIMITER ;
 
 /*3.2.3*/
 
 DELIMITER //
 CREATE PROCEDURE User_Average_Rating (IN selected_id INT)
 BEGIN 
 SELECT AVG(likert) FROM rates WHERE (user_id=selected_id AND approved=1);
 END;
 // 
 DELIMITER ;
 
 
DELIMITER //
 CREATE PROCEDURE Category_Average_Rating (IN cat VARCHAR(50))
 BEGIN 
 SELECT AVG(r.likert) FROM rates r INNER JOIN
 (SELECT b.book_id FROM books b INNER JOIN
 (SELECT be.book_id FROM belongs be INNER JOIN
 (SELECT c.category_id FROM category c WHERE (c.category_name=cat)
 )as g
 ON g.category_id=be.category_id
 )as f
 ON f.book_id=b.book_id
 )as l
 ON l.book_id=r.book_id WHERE r.approved=1 ;
 END;
 // 
 DELIMITER ;
 
 /*MANAGER QUERIES*/
 
 /*3.1.1*/
 
 DELIMITER //
CREATE PROCEDURE `SchoolLendings`(IN yearr INT , IN monthh INT)
BEGIN
IF yearr=0 AND monthh=0 THEN
SELECT s.school_name,s.school_id,COUNT(s.school_id) as cnt
FROM schools s INNER JOIN ( SELECT school_id
FROM users u INNER JOIN lends l ON u.user_id = l.user_id) as ul
ON ul.school_id = s.school_id
GROUP BY s.school_id;

ELSEIF yearr=0 THEN
SELECT s.school_name,s.school_id,COUNT(s.school_id) as cnt
FROM schools s INNER JOIN ( SELECT u.school_id
FROM users u INNER JOIN (SELECT * FROM lends WHERE MONTH(lend_date)=monthh) l ON u.user_id = l.user_id) as ul
ON ul.school_id = s.school_id
GROUP BY s.school_id;

ELSEIF monthh=0 THEN
SELECT s.school_name,s.school_id,COUNT(s.school_id) as cnt
FROM schools s INNER JOIN ( SELECT u.school_id
FROM users u INNER JOIN (SELECT * FROM lends WHERE YEAR(lend_date)=yearr) l ON u.user_id = l.user_id) as ul
ON ul.school_id = s.school_id
GROUP BY s.school_id;

ELSE 
SELECT s.school_name,s.school_id,COUNT(s.school_id) as cnt
FROM schools s INNER JOIN ( SELECT u.school_id
FROM users u INNER JOIN (SELECT * FROM lends WHERE YEAR(lend_date)=yearr AND MONTH(lend_date)=monthh) l ON u.user_id = l.user_id) as ul
ON ul.school_id = s.school_id
GROUP BY s.school_id;

END IF;
END; //
DELIMITER ;


/*3.1.2*/
DELIMITER //
CREATE PROCEDURE `CategoryWriters`(IN cat varchar(50))
BEGIN
SELECT DISTINCT a.author_first_name, a.author_last_name , a.author_id
FROM author as a INNER JOIN (SELECT author_id FROM writes  as w
INNER JOIN (
SELECT book_id FROM belongs
WHERE category_id = (SELECT category_id FROM category WHERE category_name = cat)
) as b
ON b.book_id = w.book_id
) as s
ON a.author_id = s.author_id;
END;
//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `CategoryLenders`(IN cat varchar(50))
BEGIN
SELECT DISTINCT u.user_id, u.first_name , u.last_name
FROM (SELECT user_id,first_name,last_name FROM users WHERE user_role= "teacher" OR user_role="principal") as u INNER JOIN (SELECT user_id FROM lends  as l 
INNER JOIN (
SELECT book_id FROM belongs
WHERE category_id = (SELECT category_id FROM category WHERE category_name = cat)
) as b
ON b.book_id = l.book_id
WHERE (DATEDIFF(NOW(),lend_date)<=365)
) as s
ON u.user_id = s.user_id;
END;
//
DELIMITER ;

/* 3.1.3 */
CREATE VIEW teacher_lends
(first_name,last_name,user_id,book_id)
AS
SELECT u.first_name,u.last_name,l.user_id, l.book_id
FROM lends l INNER JOIN users u
ON l.user_id = u.user_id
WHERE u.user_role ="teacher";

SELECT t.first_name,t.last_name,t.user_id, COUNT(t.user_id) AS 'lendings', DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),u.date_of_birth)), '%Y' ) + 0 AS "age" 
FROM teacher_lends t INNER JOIN users u
ON t.user_id = u.user_id AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),u.date_of_birth)), '%Y' ) + 0 < 40
GROUP BY user_id
ORDER BY lendings DESC
LIMIT 3;

/*3.1.4*/

CREATE VIEW lended_authors
(author_first_name, author_last_name,author_id)
AS
SELECT DISTINCT a.author_first_name, a.author_last_name, a.author_id
FROM author a INNER JOIN (
SELECT DISTINCT w.book_id, w.author_id
FROM writes w INNER JOIN lends l
ON w.book_id = l.book_id
) b
ON a.author_id = b.author_id
ORDER BY author_id;

SELECT a.author_first_name , a.author_last_name ,a.author_id
FROM author as a
LEFT JOIN lended_authors as lended
ON lended.author_id = a.author_id
WHERE lended.author_id IS NULL;

/*3.1.5*/
SELECT  u.first_name,u.last_name,u.school_id,countsa,m.first_name,m.last_name,m.school_id FROM users u INNER JOIN(
SELECT l.handler_id,count(l.handler_id) as countsa FROM lends l WHERE (DATEDIFF(NOW(),lend_date)<=365) GROUP BY l.handler_id HAVING (count(l.handler_id)>=20) 
)as k
ON k.handler_id=u.user_id INNER JOIN  (SELECT a.first_name,a.last_name,a.school_id,countsb FROM users a INNER JOIN(
SELECT n.handler_id,count(n.handler_id) as countsb FROM lends n WHERE (DATEDIFF(NOW(),lend_date)<=365) GROUP BY n.handler_id HAVING (count(n.handler_id)>=20)) as h ON h.handler_id=a.user_id) as m
ON countsa = countsb
WHERE u.last_name != m.last_name
group by countsa;

/*3.1.6*/
SELECT c1.category_name, c2.category_name, COUNT(*) AS pair_count
FROM belongs b1
INNER JOIN belongs b2 ON b1.book_id = b2.book_id AND b1.category_id < b2.category_id
INNER JOIN category c1 ON b1.category_id = c1.category_id
INNER JOIN category c2 ON b2.category_id = c2.category_id
INNER JOIN lends l ON b1.book_id = l.book_id
GROUP BY c1.category_name, c2.category_name
ORDER BY pair_count DESC
LIMIT 3;

/*3.1.7*/
CREATE VIEW books_written
(author_first_name, author_last_name, author_id, Written)
AS
SELECT a.author_first_name, a.author_last_name, w.author_id, COUNT(w.author_id) as "Written" 
FROM writes w
INNER JOIN author a
ON a.author_id = w.author_id
GROUP BY author_id
ORDER BY author_id;

SELECT author_first_name,author_last_name , author_id ,written 
FROM books_written 
WHERE (SELECT MAX(written) FROM books_written)-written>=5;