DELIMITER //

CREATE TRIGGER check_booking_limit
BEFORE INSERT ON bookings
FOR EACH ROW
BEGIN
  -- Check if the user has more than two bookings
  IF (SELECT COUNT(*) FROM bookings WHERE user_id = NEW.user_id) >= 2 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='student'THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Booking limit exceeded';
  END IF;
  
  IF (SELECT COUNT(*) FROM bookings WHERE user_id = NEW.user_id) >= 1 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='teacher'  THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Booking limit exceeded';
  END IF;
  
  IF (SELECT COUNT(*) FROM bookings WHERE user_id = NEW.user_id) >= 1 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='handler'  THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Booking limit exceeded';
  END IF;


  IF EXISTS ( SELECT 1 FROM lends
    WHERE user_id = NEW.user_id
      AND return_date IS NULL
      AND lend_date <= DATE_SUB(NOW(), INTERVAL 1 WEEK)
  ) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overdue book not returned';
  END IF;
END //
DELIMITER ;

SET GLOBAL EVENT_SCHEDULER=ON;

CREATE EVENT delete_old_bookings
ON SCHEDULE EVERY 1 DAY
DO DELETE FROM bookings where booking_date <= DATE_SUB(NOW(),INTERVAL 1 WEEK);

DELIMITER //
CREATE TRIGGER check_lending_limit
BEFORE INSERT ON lends
FOR EACH ROW
BEGIN
  -- Check if the user has more than two bookings
  IF (SELECT COUNT(*) FROM lends WHERE user_id = NEW.user_id AND lend_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)) >= 2 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='student'THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lending limit exceeded';
  END IF;
  
  IF (SELECT COUNT(*) FROM lends WHERE user_id = NEW.user_id AND lend_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)) >= 1 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='teacher'  THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lending limit exceeded';
  END IF;
  
  IF (SELECT COUNT(*) FROM lends WHERE user_id = NEW.user_id AND lend_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)) >= 1 AND (SELECT user_role from users WHERE user_id = NEW.user_id) ='handler'  THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lending limit exceeded';
  END IF;


  IF EXISTS ( SELECT 1 FROM lends
    WHERE user_id = NEW.user_id
      AND return_date IS NULL
      AND lend_date <= DATE_SUB(NOW(), INTERVAL 1 WEEK)
  ) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overdue book not returned';
  END IF;
  
  IF (SELECT quantity FROM has WHERE book_id = NEW.book_id AND school_id = (SELECT school_id FROM users WHERE user_id = NEW.user_id))>0 THEN
	UPDATE has SET quantity = quantity-1 WHERE book_id = NEW.book_id AND school_id = (SELECT school_id FROM users WHERE user_id = NEW.user_id);
  ELSE 
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No copies available right now';
  END IF;
	
  
END //
DELIMITER ;


