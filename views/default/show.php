<?php
use yii\helpers\Html;
use yii\helpers\Url;

$url = substr(Url::current(), 0, strrpos(Url::current(), '/'));
?>

<div class="article-single">
	<h1 class="article-title"><?= $model->title ?></h1>

    <div class="article-text block"><?= $model->text ?></div>

	<div class="article-back"><?= Html::a('&larr; Все статьи', $url, ['class' => 'link']);?></div>

</div>
