<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \stepancher\content\assets\ContentAsset;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var stepancher\content\models\Content $content
 */

$title = $title ? $title : Yii::t('content', 'Articles');
$this->title = Yii::t('content', 'Archive') . ' (' . $title . ')';
$this->params['breadcrumbs'][] = ['label'=>$title,'url'=>'/admin/content/index?type='.$type];
$this->params['breadcrumbs'][] = ['label'=>$this->title,'url'=>''];

$actionButtons = ''
    . Html::submitButton('<i class="icon fa fa-reply"></i>', ['class' => 'btn btn-sm btn-primary', 'name' => \stepancher\content\controllers\AdminController::ACTION_UNARCHIVE, 'title' => 'Восстановить выбранные записи'])
    . Html::submitButton('<i class="icon fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger isDel', 'name' => \stepancher\content\controllers\AdminController::ACTION_DELETE, 'title' => 'Удалить выбранные записи', 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')])
;
?>
<div class="content-index">

    <div class="box">
        <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => '/admin/content/group-action', 'method' => 'POST']); ?>
        <?= Html::hiddenInput('model', \stepancher\content\models\Content::className()) ?>
        <?= Html::hiddenInput('url', Yii::$app->request->url) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "<div class='box-body'>{items}</div><div class='box-footer'><div class='row'><div class='col-xs-3 text-left'>".$actionButtons."</div><div class='col-sm-6'>{summary}</div><div class='col-sm-6'>{pager}</div></div></div>",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'multiple' => true,
                    'name' => 'Content'
                ],
                [
                    'header' => Yii::t('content', 'Image'),
                    'format' => ['image',['width'=>'100']],
                    'value' => function ($model) {
                        return $model->getImageUrl() ? $model->getImageUrl() : '';
                    }
                ],
                'header',
                [
                    'attribute' => 'visible',
                    'format' => 'boolean',
                ],
                [
                    'attribute' => 'sort',
                    'format' => 'raw',
                    'value' => function($data) {
                        return ($data->sort ? $data->sort : 'Нет');
                    },
                ],
                'date_show',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{restore}{delete}',
                    'buttons' => [
                        'restore' => function($url, $model) use ($type) {
                            return Html::a( '<span class="icon fa fa-reply"></span> ', '/admin/content/unarchive?id='.$model->id.'&type='.$type, [
                                'class' => 'btn btn-sm btn-primary isDel',
                                'title' => 'Восстановить статью'
                            ]);
                        },
                        'delete' => function($url, $model) use ($type) {
                            return Html::a( '<span class="icon fa fa-trash"></span> ', $url.'&type='.$type, [
                                'class' => 'btn btn-sm btn-danger isDel',
                                'title' => 'Удалить безвозвратно'
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
        <?php \yii\bootstrap\ActiveForm::end(); ?>
    </div>

</div>
