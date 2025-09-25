-- Translate entities to English, refine schema
-- Changes to ER model vs screenshot:
-- 1) Introduced 'users' instead of 'employees', added 'roles_global' for global roles.
-- 2) Added 'boards' with 'board_key' and 'user_board' for membership/roles ('board_roles').
-- 3) Tasks belong to board directly, and optionally to an epic (on same board).
-- 4) Added auth_tokens for persistent login via cookies.
-- 5) Added email_log to capture formatted mails.
-- 6) No ON DELETE CASCADE: cascading is implemented via stored procedures.

CREATE TABLE IF NOT EXISTS roles_global (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar_url VARCHAR(255) NULL,
  job_title VARCHAR(255) NULL,
  global_role_id INT NOT NULL DEFAULT 2, -- 1 guest, 2 client, 3 staff, 4 admin
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (global_role_id) REFERENCES roles_global(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS boards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  board_key VARCHAR(16) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS board_roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_board (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  board_id INT NOT NULL,
  role_id INT NOT NULL,
  UNIQUE KEY uq_user_board (user_id, board_id),
  CONSTRAINT fk_ub_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_ub_board FOREIGN KEY (board_id) REFERENCES boards(id),
  CONSTRAINT fk_ub_role FOREIGN KEY (role_id) REFERENCES board_roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS statuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS priorities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS task_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS epics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  owner_user_id INT NULL,
  board_id INT NOT NULL,
  status_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_epic_owner FOREIGN KEY (owner_user_id) REFERENCES users(id),
  CONSTRAINT fk_epic_board FOREIGN KEY (board_id) REFERENCES boards(id),
  CONSTRAINT fk_epic_status FOREIGN KEY (status_id) REFERENCES statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  status_id INT NOT NULL,
  epic_id INT NULL,
  board_id INT NOT NULL,
  type_id INT NOT NULL,
  priority_id INT NOT NULL,
  author_id INT NOT NULL,
  assignee_id INT NULL,
  due_date DATE NULL,
  story_points TINYINT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_task_status FOREIGN KEY (status_id) REFERENCES statuses(id),
  CONSTRAINT fk_task_epic FOREIGN KEY (epic_id) REFERENCES epics(id),
  CONSTRAINT fk_task_board FOREIGN KEY (board_id) REFERENCES boards(id),
  CONSTRAINT fk_task_type FOREIGN KEY (type_id) REFERENCES task_types(id),
  CONSTRAINT fk_task_priority FOREIGN KEY (priority_id) REFERENCES priorities(id),
  CONSTRAINT fk_task_author FOREIGN KEY (author_id) REFERENCES users(id),
  CONSTRAINT fk_task_assignee FOREIGN KEY (assignee_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS auth_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  selector VARCHAR(24) NOT NULL UNIQUE,
  validator_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  CONSTRAINT fk_autht_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS email_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  body_html MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
