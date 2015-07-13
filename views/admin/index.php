<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \stepancher\content\assets\ContentAsset;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var stepancher\content\models\Content $content
 */

ContentAsset::register($this);

$this->title = ($title) ? $title : Yii::t('content', 'Content');
$this->params['breadcrumbs'][] = ['label'=>$this->title,'url'=>''];

$actionButtons = ''
    . Html::submitButton('<i class="icon fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger isDel', 'name' => \stepancher\content\controllers\AdminController::ACTION_ARCHIVE, 'title' => 'Удалить выбранные записи', 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')])
;

$archiveButton = Html::a('<i class="icon fa fa-trash"></i>Корзина', '/admin/content/archives', ['class' => 'btn btn-danger']);

$tabs = array();
foreach ($dataProviders as $title => $dataProvider) {
    $tabs[] = [
        'label' => $title,
        'content' => GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "<div class='box-body'>{items}</div><div class='box-footer'><div class='row'><div class='col-xs-3 text-left'>".$actionButtons."</div><div class='col-sm-6'>{summary}</div><div class='col-sm-6'>{pager}</div><div class='col-xs-3 text-right'>".$archiveButton."</div></div></div>",
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
                        return $model->getImageUrl() ? $model->getImageUrl() : 'no_image';
                    }
                ],
                [
                    'attribute'=>'header',
                    'format'=>'raw',
                    'value'=>function($data) use($type) {
                        return Html::a($data->header,\yii\helpers\Url::toRoute(['/content/admin/update','id'=>$data->id,'type'=>$type]));
                    },
                ],
                [
                    'attribute' => 'visible',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Html::checkbox('visible', $data->visible, ['class' => 'visible_checkbox','data-id' => $data->id]);
                    }
                ],
                [
                    'attribute' => 'sort',
                    'format' => 'raw',
                    'value' => function($data) {
                        $sort = Html::button('<i class="icon icon fa fa-edit">'.($data->sort ? $data->sort : 'Нет').'</i>', [
                            'title' => 'Редактировать',
                            'class' => 'sort_input'
                        ]);

                        $input = Html::input('number', 'sort', ($data->sort ? $data->sort : ''), [
                            'class' => 'sort_change input-sm',
                            'style' => 'cursor: pointer; width: 70px;',
                            'data-id' => $data->id
                        ]);

                        $button = Html::a('OK', '', ['class' => 'sort_button btn btn-success btn-sm']);

                        return $sort.$input.$button;
                    },
                ],
                'date_show',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{delete}',
                    'buttons' => [
                        'delete' => function($url, $model) use($type) {
                            return Html::a( '<span class="icon fa fa-trash"></span> ', '/admin/content/archive?id='.$model->id.'&type='.$type, [
                                'class' => 'btn btn-sm btn-danger isDel'
                            ]);
                        }
                    ],
                ],
            ],
        ]),
//        'active' => true
    ];
}
?>
<div class="content-index">
    <p>
        <?= Html::a(Yii::t('content', 'Create content', [
          'modelClass' => 'content',
        ]), \yii\helpers\Url::toRoute(['create?type='.$type]), ['class' => 'btn btn-success']) ?>
    </p>
    
    <div id="content-list">
        <div class="row">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => '/admin/content/group-action', 'method' => 'POST']); ?>
                <?= Html::hiddenInput('model', \stepancher\content\models\Content::className()) ?>
                <?= Html::hiddenInput('url', Yii::$app->request->url) ?>
                <?= \yii\bootstrap\Tabs::widget([
                    'items' => $tabs,
                    'itemOptions' => ['class' => 'panel']
                ]) ?>
            <?php \yii\bootstrap\ActiveForm::end(); ?>
        </div>
    </div>

</div>
