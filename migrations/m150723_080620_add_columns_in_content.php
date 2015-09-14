<?php


use yii\db\Schema;
use yii\db\Migration;

class m150723_080620_add_columns_in_content extends Migration
{
    public function up()
    {
        $this->addColumn('{{%content}}', 'on_main', Schema::TYPE_BOOLEAN . ' DEFAULT false');
        $this->addColumn('{{%content}}', 'created_by', Schema::TYPE_INTEGER);
        $this->addColumn('{{%content}}', 'updated_by', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%content}}', 'on_main');
        $this->dropColumn('{{%content}}', 'created_by');
        $this->dropColumn('{{%content}}', 'updated_by');
    }
}
