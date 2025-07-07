<?php
use Phalcon\Db\Adapter\Pdo\AbstractPdo;

class CreateTopics
{
    public function up(AbstractPdo $db)
    {
        $dialect = $db->getDialect()->getDialectType();
        if ($dialect === 'sqlite') {
            $sql = 'CREATE TABLE IF NOT EXISTS topics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                url VARCHAR(2048) NOT NULL,
                category VARCHAR(60) NOT NULL,
                published_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME NULL
            )';
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS topics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                url VARCHAR(2048) NOT NULL,
                category VARCHAR(60) NOT NULL,
                published_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        }
        $db->execute($sql);
    }
}
