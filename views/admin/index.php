<?php

use yii\helpers\Html;
use yii\grid\GridView;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var stepancher\content\models\Content $content
 */
$this->title = Yii::t('content', 'Content');
$this->params['breadcrumbs'][] = ['label'=>$this->title,'url'=>''];
?>
<div class="content-index">
    <p>
        <?= Html::a(Yii::t('content', 'Create content', [
          'modelClass' => 'content',
        ]), \yii\helpers\Url::toRoute(['create']), ['class' => 'btn btn-success']) ?>
    </p>

    <div id="content-list">
        <div class="box">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "<div class='box-body'>{items}</div><div class='box-footer'><div class='row'><div class='col-sm-6'>{summary}</div><div class='col-sm-6'>{pager}</div></div></div>",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                [
                    'attribute'=>'header',
                    'format'=>'raw',
                    'value'=>function($data){
                        return Html::a($data->header,\yii\helpers\Url::toRoute(['/content/admin/update','id'=>$data->id]));
                    },
                ],
                'create_time',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',

                ],
            ],
        ]); ?>
        </div>
    </div>

</div>
