<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_081811_add_permissions extends Migration
{
    private $permissions = [
        'r_content' => 'Просмотр статей',
        'w_content' => 'Редкатирование статей',
    ];

    public function safeUp()
    {
        foreach($this->permissions as $name => $desc) {
            $perm = Yii::$app->authManager->createPermission($name);
            $perm->description = $desc;
            Yii::$app->authManager->add($perm);
        }
    }

    public function safeDown()
    {
        foreach($this->permissions as $name => $desc) {
            $perm = Yii::$app->authManager->getPermission($name);
            if ($perm) {
                Yii::$app->authManager->remove($perm);
            }
        }
    }
}
