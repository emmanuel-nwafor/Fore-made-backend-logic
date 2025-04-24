-- Create a users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

-- Seed some users
INSERT INTO users (name, email, password) VALUES
('John Doe', 'john@example.com', 'hashed_pass_1'),
('Jane Smith', 'jane@example.com', 'hashed_pass_2');
