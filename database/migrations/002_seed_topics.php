<?php
use Phalcon\Db\Adapter\Pdo\AbstractPdo;

class SeedTopics
{
    public function up(AbstractPdo $db)
    {
        $dialect = $db->getDialect()->getDialectType();
        for ($i = 1; $i <= 50; $i++) {
            if ($dialect === 'sqlite') {
                $sql = "INSERT INTO topics (title, url, category, published_at, created_at, updated_at) VALUES ('Topic {$i}','http://example.com/{$i}','news',datetime('now'),datetime('now'),datetime('now'))";
            } else {
                $sql = "INSERT INTO topics (title, url, category, published_at, created_at, updated_at) VALUES ('Topic {$i}','http://example.com/{$i}','news',NOW(),NOW(),NOW())";
            }
            $db->execute($sql);
        }
    }
}
