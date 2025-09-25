DELIMITER $$

-- создать доску
CREATE PROCEDURE sp_create_board(IN p_name VARCHAR(255), IN p_key VARCHAR(16))
BEGIN
INSERT INTO boards(name, board_key) VALUES (p_name, p_key);
SELECT LAST_INSERT_ID() AS new_board_id;
END$$

-- обновить доску
CREATE PROCEDURE sp_update_board(IN p_id INT, IN p_name VARCHAR(255), IN p_key VARCHAR(16))
BEGIN
UPDATE boards SET name = p_name, board_key = p_key WHERE id = p_id;
END$$

-- удалить участника (отвязать пользователя от доски)
CREATE PROCEDURE sp_remove_user_from_board(IN p_user_id INT, IN p_board_id INT)
BEGIN
DELETE FROM user_board WHERE user_id = p_user_id AND board_id = p_board_id;
END$$

DELIMITER ;
