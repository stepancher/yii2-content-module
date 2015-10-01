<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row">
	<div class="col-xs-4 article-image">
		<img src="<?=\Yii::$app->getModule("content")->imageUrl?>/<?= $model->image_file ?>" />
	</div>
	<div class="col-xs-8 article-content">
		<h3 class="article-title"><?= Html::a($model->header, Url::toRoute(preg_replace('/\?(.*)/i', '', Url::current()).'/'.$model->url)); ?></h3>
		<div class="article-text"><?= $model->short_text ?></div>
		<div class="article-more"><?= Html::a('<span>Читать далее</span> &rarr;', Url::toRoute(preg_replace('/\?(.*)/i', '', Url::current()).'/'.$model->url), ['class'=>'a']); ?></div>
	</div>
</div>
