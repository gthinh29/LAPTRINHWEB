CREATE DATABASE BlogDB;
GO

USE BlogDB;
GO


CREATE TABLE posts(
	id INT PRIMARY KEY IDENTITY(1,1),
	title NVARCHAR(225),
	content NVARCHAR(MAX),
	created_at DATETIME DEFAULT GETDATE(),
	username NVARCHAR(50) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL, -- Nên l?u tr? m?t kh?u ?ã ???c hash
);


INSERT INTO posts(username, password) VALUES ('admin', '123');
INSERT INTO posts(username, password) VALUES ('user1', 'password123');