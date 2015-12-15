<?php

use yii\helpers\Html;
use stepancher\content\assets\ContentAsset;
use stepancher\content\Content;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var stepancher\content\models\Content $content
 */

ContentAsset::register($this);

$title = Yii::$app->getModule($this->context->module->id)->title;
$this->title = Yii::t('content', 'Archive') . ' (' . $title . ')';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => '/admin/'.$this->context->module->id.'/index'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ''];

/* @var \stepancher\content\models\Content $module */
$module = Yii::$app->getModule($this->context->module->id)->model('Content', ['id' => $this->context->module->id]);

?>
<?php
// Default settings
$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    [
        'class' => 'kartik\grid\ActionColumn',
        'options' => ['style' => 'width:100px'],

        'dropdown' => false,
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'template' => '{unarchive} {delete}',
        'buttons' => [
            'unarchive' => function($url, $model) {
                return Html::a('<span class="icon fa fa-reply"></span> ', $url, [
                    'class' => 'btn btn-sm btn-primary',
                    'title' => 'Восстановить',
                ]);
            },
            'delete' => function($url, $model) {
                return Html::a('<span class="icon fa fa-trash"></span> ', $url, [
                    'class' => 'btn btn-sm btn-danger',
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                ]);
            }
        ]
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'order' => DynaGrid::ORDER_FIX_LEFT,
        'multiple' => true,
    ],
];

// Attribute settings
foreach($module->attributes as $attr => $i) {
    $value = $module->getConfigAttribute($attr);
    if($value) {
        switch ($value['type']) {
            case Content::ATTR_TYPE_IMAGE:
                $columns[] = [
                    'attribute' => $attr,
                    'format' => ['image', ['width' => '100']],
                    'value' => function ($model) {
                        return $model->getImageUrl() ? $model->getImageUrl() : '';
                    },
                    'filter' => false,
                    'visible' => isset($value['visible']) ? $value['visible'] : true
                ];
                break;
            case Content::ATTR_TYPE_DATE:
                $columns[] = [
                    'attribute' => $attr,
                    'format' => ['raw', ['width' => '100']],
                    'value' => function ($data) use ($attr) {
                        return Yii::$app->formatter->asDateTime($data->$attr, Yii::$app->formatter->dateFormat) . ' <small style="color:gray;">' . Yii::$app->formatter->asDateTime($data->$attr, Yii::$app->formatter->timeFormat) . '</small>';
                    },
                    'filter' => false,
                    'visible' => isset($value['visible']) ? $value['visible'] : true
                ];
                break;
            case Content::ATTR_TYPE_BOOLEAN:
                $columns[] = [
                    'attribute' => $attr,
                    'format' => 'raw',
                    'value' => function($data) use ($attr) {
                        return Html::checkbox($attr, $data->$attr);
                    },
                    'filter' => false,
                    'visible' => isset($value['visible']) ? $value['visible'] : true
                ];
                break;
            case Content::ATTR_TYPE_DROPDOWN:
                $columns[] = [
                    'attribute' => $attr,
                    'value' => function ($data) use ($attr, $value) {
                        return isset($value['items'][$data->$attr]) ? $value['items'][$data->$attr] : '';
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => $value['items'],
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => ['placeholder' => '---'],
                    'visible' => isset($value['visible']) ? $value['visible'] : true
                ];
                break;
            case Content::ATTR_TYPE_HIDE:
                $columns[] = [
                    'attribute' => $attr,
                    'visible' => false
                ];
                break;
            default:
                $columns[] = [
                    'attribute' => $attr,
                    'visible' => isset($value['visible']) ? $value['visible'] : true
                ];
                break;
        }
    }
}

if (class_exists('\stepancher\adminlteTheme\config\AnminLteThemeConfig')) {
    DynaGrid::begin(\yii\helpers\ArrayHelper::merge(\stepancher\adminlteTheme\config\AnminLteThemeConfig::getDefaultConfigDynagrid(), [
            'columns' => $columns,
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'after' => Html::a('<i class="icon glyphicon fa fa-reply"></i> &nbsp&nbspВосстановить', '#', ['data-classname' => $module::className(), 'data-action' => \stepancher\content\controllers\AdminController::ACTION_UNARCHIVE, 'class' => 'btn btn-primary btn-multiple', 'title' => 'Восстановить выбранные записи']) .
                        Html::a('<i class="icon glyphicon fa fa-trash"></i> &nbsp&nbspУдалить', '#', ['data-classname' => $module::className(), 'data-action' => \stepancher\content\controllers\AdminController::ACTION_DELETE, 'class' => 'btn btn-danger btn-multiple', 'title' => 'Удалить выбранные записи'])
                        . '<div class="pull-right">{pager}</div>',
                ],
                'options' => ['id' => 'grid', 'data-url' => '/admin/' . $this->context->module->id . '/group-action'],
            ],
            'options' => ['id' => 'dynagrid-archive-' . $this->context->module->id],
        ]
    ));
} else {
    DynaGrid::begin([
            'columns' => $columns,
            'toggleButtonGrid' => [
                'label' => '<i class="glyphicon glyphicon-wrench"></i> &nbsp&nbspНастройки',
            ],
            'toggleButtonFilter' => [
                'label' => '<i class="glyphicon glyphicon-filter"></i> &nbsp&nbsp Фильтры',
            ],
            'toggleButtonSort' => [
                'label' => '<i class="glyphicon glyphicon-sort"></i> &nbsp&nbsp Сортировка',
            ],
            'storage' => DynaGrid::TYPE_DB,
//            'theme' => 'panel-default',
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'toolbar' => [
                    [
                        'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Сбросить', [''], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => 'Обновить'])
                    ], [
                        'content' => '{dynagridFilter}{dynagridSort}{dynagrid}{toggleData}',
                    ],
                    '{export}',
                ],
                'export' => [
                    'label' => 'Экспорт'
                ],
                'panel' => [
//                    'heading' => false,
                    'footer' => false,
                    'after' => Html::a('<i class="icon glyphicon fa fa-reply"></i> &nbsp&nbspВосстановить', '#', ['data-classname' => $module::className(), 'data-action' => \stepancher\content\controllers\AdminController::ACTION_UNARCHIVE, 'class' => 'btn btn-primary btn-multiple', 'title' => 'Восстановить выбранные записи']) .
                        Html::a('<i class="icon glyphicon fa fa-trash"></i> &nbsp&nbspУдалить', '#', ['data-classname' => $module::className(), 'data-action' => \stepancher\content\controllers\AdminController::ACTION_DELETE, 'class' => 'btn btn-danger btn-multiple', 'title' => 'Удалить выбранные записи'])
                        . '<div class="pull-right">{pager}</div>',
                ],
                'options' => ['id' => 'grid', 'data-url' => '/admin/' . $this->context->module->id . '/group-action'],
            ],
            'options' => ['id' => 'dynagrid-archive-' . $this->context->module->id],
        ]
    );
}
DynaGrid::end();
?>
