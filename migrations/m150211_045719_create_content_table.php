<?php
namespace stepancher\content\migrations;
use yii\db\Schema;
use yii\db\Migration;

class m150211_045719_create_content_table extends Migration
{
    public function up()
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
        ], $tableOptions);
        $this->createIndex('unique_key_url','content','url', true);
    }

    public function down()
    {

        $this->dropTable('content');
        echo "m150211_045719_create_content_table таблица content была успешно удалена\n";
        return true;
    }
}
