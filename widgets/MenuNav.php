<?php

namespace stepancher\content\widgets;

use Yii;
use yii\bootstrap\Widget;

class MenuNav extends Widget
{
    public function run()
    {
        $list = \Yii::$app->getModule("content")->types ? \Yii::$app->getModule("content")->types : null;

        return $this->render('menu_nav', [
            'list' => $list
        ]);
    }
}