INSERT INTO roles_global (id,name) VALUES (1,'guest'),(2,'client'),(3,'staff'),(4,'admin')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO board_roles (id,name) VALUES (1,'viewer'),(2,'reporter'),(3,'developer'),(4,'manager')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO statuses (id,name) VALUES (1,'Backlog'),(2,'To Do'),(3,'In Progress'),(4,'Review'),(5,'Done')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO priorities (id,name,sort_order) VALUES 
 (1,'Low',1),(2,'Medium',2),(3,'High',3),(4,'Critical',4)
ON DUPLICATE KEY UPDATE name=VALUES(name), sort_order=VALUES(sort_order);

INSERT INTO task_types (id,name) VALUES (1,'Task'),(2,'Story'),(3,'Bug'),(4,'Spike')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Admin user
INSERT INTO users(full_name,email,password_hash,global_role_id) VALUES
('Admin User','admin@example.com','$2y$10$tsBylA8ZbTkoiy5yAGlR8uhfPqYVGpEUHSoLd9O8xTYKKk1eq/zUS',4)
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);
-- Password above is bcrypt of 'admin123'

-- Sample board
INSERT INTO boards(name, board_key) VALUES ('Demo Project','DEMO')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Make admin a manager on the Demo board
INSERT INTO user_board(user_id, board_id, role_id)
SELECT u.id, b.id, 4 FROM users u, boards b WHERE u.email='admin@example.com' AND b.board_key='DEMO'
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- Sample epic & tasks
INSERT INTO epics(title, description, owner_user_id, board_id, status_id)
SELECT 'MVP Epic', 'Build MVP backlog', u.id, b.id, 2 FROM users u, boards b WHERE u.email='admin@example.com' AND b.board_key='DEMO';

INSERT INTO tasks(board_id, epic_id, title, description, type_id, priority_id, status_id, author_id, assignee_id)
SELECT b.id, e.id, 'Set up project', 'Init repo and structure', 1, 2, 2, u.id, u.id
FROM boards b, epics e, users u WHERE b.board_key='DEMO' AND e.board_id=b.id AND u.email='admin@example.com';

INSERT INTO tasks(board_id, epic_id, title, description, type_id, priority_id, status_id, author_id, assignee_id)
SELECT b.id, e.id, 'Implement auth', 'Login + remember me', 1, 3, 3, u.id, u.id
FROM boards b, epics e, users u WHERE b.board_key='DEMO' AND e.board_id=b.id AND u.email='admin@example.com';
