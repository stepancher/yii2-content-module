<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = \Yii::t('auth','articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Статьи</h1>

<div>
    <?= stepancher\content\widgets\ContentOutput::widget(['moduleId' => $moduleId]) ?>
</div>
