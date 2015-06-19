<?php

namespace stepancher\content\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150615_061235_add_column_in_content extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%content}}', 'type', Schema::TYPE_STRING);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%content}}', 'type');
    }
}
