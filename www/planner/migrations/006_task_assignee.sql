DELIMITER $$

CREATE PROCEDURE sp_update_task_assignee(IN p_task_id INT, IN p_assignee_id INT)
BEGIN
UPDATE tasks SET assignee_id = p_assignee_id WHERE id = p_task_id;
END$$

DELIMITER ;
