DELIMITER $$

CREATE PROCEDURE sp_update_task_type(IN p_task_id INT, IN p_type_id TINYINT)
BEGIN
UPDATE tasks SET type_id = p_type_id WHERE id = p_task_id;
END$$

CREATE PROCEDURE sp_update_task_priority(IN p_task_id INT, IN p_priority_id TINYINT)
BEGIN
UPDATE tasks SET priority_id = p_priority_id WHERE id = p_task_id;
END$$

DELIMITER ;
