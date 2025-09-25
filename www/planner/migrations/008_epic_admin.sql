DELIMITER $$

CREATE PROCEDURE sp_update_epic(
    IN p_id INT,
    IN p_title VARCHAR(255),
    IN p_description TEXT,
    IN p_owner_user_id INT,
    IN p_status_id TINYINT
)
BEGIN
UPDATE epics
SET title = p_title,
    description = p_description,
    owner_user_id = p_owner_user_id,
    status_id = p_status_id
WHERE id = p_id;
END$$

DELIMITER ;
