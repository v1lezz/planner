DELIMITER $$

CREATE PROCEDURE sp_create_epic(
    IN p_board_id INT,
    IN p_title VARCHAR(255),
    IN p_description TEXT,
    IN p_owner_user_id INT,
    IN p_status_id TINYINT
)
BEGIN
INSERT INTO epics (title, description, owner_user_id, board_id, status_id)
VALUES (p_title, p_description, p_owner_user_id, p_board_id, p_status_id);
SELECT LAST_INSERT_ID() AS new_epic_id;
END$$

DELIMITER ;
