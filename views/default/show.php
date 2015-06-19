<?php use yii\helpers\Html; ?>

<div class="article-single">
	<h1 class="article-title"><?= $model->title ?></h1>

    <div class="article-text block"><?= $model->text ?></div>

	<div class="article-back"><?= Html::a('&larr; Все статьи','/articles', ['class'=>'link']);?></div>

</div>
