<?php

namespace stepancher\content\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150710_085429_add_column_is_archive_in_content extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%content}}', 'is_archive', Schema::TYPE_BOOLEAN . " DEFAULT FALSE");
    }

    public function safeDown()
    {
        $this->dropColumn('{{%content}}', 'is_archive');
    }
}
