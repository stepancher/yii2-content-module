<?php

use yii\helpers\Html;
use yii\grid\GridView;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */



foreach($dataProvider->getModels() as $content):
    echo Html::a($content->header,\yii\helpers\Url::toRoute(\yii\helpers\Url::current().'/'.$content->url))."<br>";
endforeach;

?>
