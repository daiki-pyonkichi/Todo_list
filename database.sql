-- データベースの作成
CREATE DATABASE IF NOT EXISTS todolist_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE todolist_db;

-- usersテーブルの作成
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    password VARCHAR(255),
    verification_token VARCHAR(64),
    token_expires TIMESTAMP,
    is_verified BOOLEAN DEFAULT FALSE
);

-- todosテーブルの作成
CREATE TABLE IF NOT EXISTS todos (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    task VARCHAR(255) NOT NULL,
    detail TEXT,
    deadline DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
); 