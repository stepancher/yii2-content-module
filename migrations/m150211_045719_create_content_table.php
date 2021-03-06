<?php
use yii\db\Schema;
use yii\db\Migration;

class m150211_045719_create_content_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('content', [
            "id"            => Schema::TYPE_PK,
            "header"        => Schema::TYPE_STRING      . ' not null',
            "title"         => Schema::TYPE_STRING      . ' default null',
            "image_file"    => Schema::TYPE_STRING      . ' default null',
            "short_text"    => Schema::TYPE_TEXT        . ' null',
            "text"          => Schema::TYPE_TEXT        . ' null',
            "url"           => Schema::TYPE_TEXT        . ' null',
            "description"   => Schema::TYPE_TEXT        . ' default null',
            "keywords"      => Schema::TYPE_TEXT        . ' default null',
            "visible"       => Schema::TYPE_BOOLEAN     . ' default true',
            "sort"          => Schema::TYPE_INTEGER     . ' null',
            "create_time"   => Schema::TYPE_TIMESTAMP   . ' null default null',
            "update_time"   => Schema::TYPE_TIMESTAMP   . ' null default null',
            "date_show"     => Schema::TYPE_TIMESTAMP   . ' null',
            "date_hide"     => Schema::TYPE_TIMESTAMP   . ' null',
            "lang"          => Schema::TYPE_STRING . " DEFAULT 'ru'",
            "is_archive"    => Schema::TYPE_BOOLEAN . " DEFAULT FALSE",
            "on_main"       => Schema::TYPE_BOOLEAN . ' DEFAULT FALSE',
            "created_by"    => Schema::TYPE_INTEGER,
            "updated_by"    => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('unique_key_url', 'content', 'url', true);
    }

    public function safeDown()
    {
        $this->dropTable('content');
    }
}
