<?php

namespace stepancher\content\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150625_083545_add_column_lang_in_content extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%content}}', 'lang', Schema::TYPE_STRING . " DEFAULT 'ru'");
    }

    public function safeDown()
    {
        $this->dropColumn('{{%content}}', 'lang');
    }
}
