<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_081812_add_permissions extends Migration
{
    private $permissions = [
        'r_content' => 'Просмотр статей',
        'w_content' => 'Редкатирование статей',
    ];

    public function safeUp()
    {
        $roleModer = Yii::$app->authManager->getRole('moderator');
        $roleAdmin = Yii::$app->authManager->getRole('admin');
        $roleDev = Yii::$app->authManager->getRole('developer');

        foreach($this->permissions as $name => $desc) {
            $perm = Yii::$app->authManager->createPermission($name);
            $perm->description = $desc;
            Yii::$app->authManager->add($perm);

            if($roleAdmin) Yii::$app->authManager->addChild($roleAdmin, $perm);
            if($roleModer) Yii::$app->authManager->addChild($roleModer, $perm);
            if($roleDev) Yii::$app->authManager->addChild($roleDev, $perm);
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
