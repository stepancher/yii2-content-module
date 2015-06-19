<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */



$model = $dataProvider->getModels();

foreach($model as $content):?>
    <h3><?=$content->header?>---</h3>
    <p><?=$content->title?></p>
<?php endforeach; ?>