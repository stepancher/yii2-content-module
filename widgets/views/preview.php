<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
?>

<div class="row article-list">
	<?= ListView::widget([
	    'layout'=>'{items}',
	    'itemView'=>'preview_item',
	    'dataProvider' => $dataProvider,
	    'itemOptions'=>['class'=>'col-sm-6 article-item'],
	])?>
</div>

<div>
	<?= \yii\widgets\LinkPager::widget([
		'pagination'=>$dataProvider->pagination,
	]); ?>
</div>
