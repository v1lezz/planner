DELIMITER $$

-- Function: does user have access to board?
CREATE FUNCTION fn_user_can_board(u_id INT, b_id INT) RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE g_role TINYINT;
  SELECT global_role_id INTO g_role FROM users WHERE id = u_id;
  IF g_role = 4 THEN
    RETURN 1;
  END IF;
  IF EXISTS (SELECT 1 FROM user_board WHERE user_id = u_id AND board_id = b_id) THEN
    RETURN 1;
  END IF;
  RETURN 0;
END$$

-- Function: human readable task key
CREATE FUNCTION fn_task_key(t_id INT) RETURNS VARCHAR(64)
READS SQL DATA
BEGIN
  DECLARE k VARCHAR(16);
  DECLARE bid INT;
  SELECT board_id INTO bid FROM tasks WHERE id = t_id;
  SELECT board_key INTO k FROM boards WHERE id = bid;
  RETURN CONCAT(k, '-', t_id);
END$$

-- View: full task with names
CREATE OR REPLACE VIEW v_task_full AS
SELECT
  t.id, t.title, t.description, t.status_id, s.name AS status_name,
  t.type_id, ty.name AS type_name,
  t.priority_id, p.name AS priority_name, p.sort_order AS priority_sort,
  t.epic_id, e.title AS epic_title,
  t.board_id, b.name AS board_name, b.board_key,
  t.author_id, a.full_name AS author_name,
  t.assignee_id, asg.full_name AS assignee_name,
  t.due_date, t.story_points, t.created_at, t.updated_at,
  fn_task_key(t.id) AS task_key
FROM tasks t
JOIN statuses s ON s.id = t.status_id
JOIN task_types ty ON ty.id = t.type_id
JOIN priorities p ON p.id = t.priority_id
JOIN boards b ON b.id = t.board_id
LEFT JOIN epics e ON e.id = t.epic_id
JOIN users a ON a.id = t.author_id
LEFT JOIN users asg ON asg.id = t.assignee_id$$

-- View: epic summary
CREATE OR REPLACE VIEW v_epic_summary AS
SELECT e.id, e.title, e.board_id, b.name AS board_name,
       COUNT(t.id) AS tasks_total,
       SUM(CASE WHEN t.status_id = 5 THEN 1 ELSE 0 END) AS tasks_done
FROM epics e
JOIN boards b ON b.id = e.board_id
LEFT JOIN tasks t ON t.epic_id = e.id
GROUP BY e.id, e.title, e.board_id, b.name$$

-- Stored procedure: register user
CREATE PROCEDURE sp_register_user(IN p_name VARCHAR(255), IN p_email VARCHAR(255), IN p_hash VARCHAR(255), IN p_role TINYINT)
BEGIN
  INSERT INTO users(full_name, email, password_hash, global_role_id) VALUES (p_name, p_email, p_hash, p_role);
  SELECT LAST_INSERT_ID() AS new_user_id;
END$$

-- Stored procedure: create task
CREATE PROCEDURE sp_create_task(
  IN p_board_id INT, IN p_epic_id INT, IN p_title VARCHAR(255), IN p_desc TEXT,
  IN p_type_id TINYINT, IN p_priority_id TINYINT, IN p_status_id TINYINT,
  IN p_author_id INT, IN p_assignee_id INT, IN p_due DATE, IN p_sp TINYINT
)
BEGIN
  INSERT INTO tasks(board_id, epic_id, title, description, type_id, priority_id, status_id, author_id, assignee_id, due_date, story_points)
  VALUES (p_board_id, p_epic_id, p_title, p_desc, p_type_id, p_priority_id, p_status_id, p_author_id, p_assignee_id, p_due, p_sp);
  SELECT LAST_INSERT_ID() AS new_task_id;
END$$

-- Update task status
CREATE PROCEDURE sp_update_task_status(IN p_task_id INT, IN p_status_id TINYINT)
BEGIN
  UPDATE tasks SET status_id = p_status_id WHERE id = p_task_id;
END$$

-- Bulk update status
CREATE PROCEDURE sp_bulk_update_task_status(IN p_board_id INT, IN p_ids_csv TEXT, IN p_status_id TINYINT)
BEGIN
  SET @sql = CONCAT('UPDATE tasks SET status_id = ? WHERE board_id = ? AND id IN (', p_ids_csv, ')');
  PREPARE stmt FROM @sql;
  SET @s = p_status_id; SET @b = p_board_id;
  EXECUTE stmt USING @s, @b;
  DEALLOCATE PREPARE stmt;
END$$

-- Bulk delete
CREATE PROCEDURE sp_bulk_delete_tasks(IN p_board_id INT, IN p_ids_csv TEXT)
BEGIN
  SET @sql = CONCAT('DELETE FROM tasks WHERE board_id = ? AND id IN (', p_ids_csv, ')');
  PREPARE stmt FROM @sql;
  SET @b = p_board_id;
  EXECUTE stmt USING @b;
  DEALLOCATE PREPARE stmt;
END$$

-- Cascade delete epic -> tasks then epic (manual, not FK cascade)
CREATE PROCEDURE sp_delete_epic_cascade(IN p_epic_id INT)
BEGIN
  DELETE FROM tasks WHERE epic_id = p_epic_id;
  DELETE FROM epics WHERE id = p_epic_id;
END$$

-- Cascade delete board: tasks -> epics -> memberships -> board
CREATE PROCEDURE sp_delete_board_cascade(IN p_board_id INT)
BEGIN
  DELETE FROM tasks WHERE board_id = p_board_id;
  DELETE FROM epics WHERE board_id = p_board_id;
  DELETE FROM user_board WHERE board_id = p_board_id;
  DELETE FROM boards WHERE id = p_board_id;
END$$

-- Add user to board
CREATE PROCEDURE sp_add_user_to_board(IN p_user_id INT, IN p_board_id INT, IN p_role_id TINYINT)
BEGIN
  INSERT INTO user_board(user_id, board_id, role_id) VALUES (p_user_id, p_board_id, p_role_id)
  ON DUPLICATE KEY UPDATE role_id = VALUES(role_id);
END$$

-- Log email
CREATE PROCEDURE sp_log_email(IN p_to VARCHAR(255), IN p_subject VARCHAR(255), IN p_html MEDIUMTEXT)
BEGIN
  INSERT INTO email_log(recipient, subject, body_html) VALUES (p_to, p_subject, p_html);
END$$

DELIMITER ;
