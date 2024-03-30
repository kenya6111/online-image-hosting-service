<?php
    
namespace Database\Migrations;
    
use Database\SchemaMigration;
    
class CreateImagesTable implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return ["CREATE TABLE images (
            id INT AUTO_INCREMENT NOT NULL primary key,
            title VARCHAR(256),
            file_path VARCHAR(256),
            file_name VARCHAR(256),
            url varchar(256),
            delete_url varchar(256),
            view_count int,
            mine_type varchar(256),
            expired_date DATETIME,
            size int,
            uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );"];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return ["DROP TABLE images;"];
    }
}