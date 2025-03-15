<?php

require_once __DIR__ . '/../utils/CustomThrow.php';

class ArticlesDatabase {

    private static ?\mysqli $conn = null;

    protected static function connection(): \mysqli {
        if (self::$conn === null) {
            self::$conn = new \mysqli("localhost", "root", "");

            CustomThrow::exceptionWithCondition(
                self::$conn->connect_error,
                "Connection failed: " . self::$conn->connect_error
            );

            // create database if not exist
            self::$conn->query("CREATE DATABASE IF NOT EXISTS articlesdb");
            self::$conn->select_db("articlesdb");

            // create all tables if not exist
            $queries = [
                "CREATE TABLE IF NOT EXISTS authors (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    gender ENUM('Male', 'Female', 'Other') NOT NULL,
                    age INT CHECK (age > 0),
                    country VARCHAR(100) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS providers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    link VARCHAR(500) UNIQUE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS articles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    link VARCHAR(500) NOT NULL UNIQUE,
                    year VARCHAR(4) NOT NULL,
                    author_id INT,
                    provider_id INT,
                    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
                    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )"
            ];
        
            foreach ($queries as $query) {
                CustomThrow::exceptionWithCondition(
                    !self::$conn->query($query),
                    "Error creating table: " . self::$conn->error
                );
            }
        }

        return self::$conn;
    }
}