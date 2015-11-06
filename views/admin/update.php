<?php

use yii\helpers\Html;
use vova07\imperavi\Widget;
use kartik\datetime\DateTimePicker;
use vova07\fileapi\Widget as FileAPI;
use stepancher\content\Content;

/**
 * @var yii\web\View $this
 * @var $model stepancher\content\models\Content
 */
stepancher\content\assets\ContentAsset::register($this); //это ассет модуля, нет! мы не запихнем его ко всем ассетам

if (!$this->title) {
    $this->title = $model->isNewRecord ?
        'Создание ' . Yii::$app->getModule($moduleId)->titleEdit
        : 'Редактирование ' . Yii::$app->getModule($moduleId)->titleEdit;

    $this->params['breadcrumbs'][] =
        ['label' => Yii::$app->getModule($moduleId)->title, 'url' => \yii\helpers\Url::toRoute('/' . $moduleId . '/admin')];

    if ($model->isNewRecord) {
        $this->params['breadcrumbs'][] = Yii::t('content', 'Create content');
    } else {
        $this->params['breadcrumbs'][] = $this->title;
    }
}
?>


<?php $form = \yii\widgets\ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'
        ],
        'fieldConfig' => [
            'options' => [
                'class' => 'form-group col-sm-6'
            ]
        ],

    ]
); ?>
<div class="box">
    <div class="box-body">
        <?php
        $titleAttribute = null;
        $urlAttribute = null;

        foreach ($model->configAttributes as $attr => $value) {
            switch ($value['type']) {
                case Content::ATTR_TYPE_TITLE:
                    echo $form->field($model, $attr)->textInput(isset($value['config']) ? $value['config'] : []);
                    $titleAttribute = $attr;
                    break;
                case Content::ATTR_TYPE_STRING:
                    echo $form->field($model, $attr)->textInput(isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_INTEGER:
                    echo $form->field($model, $attr)->input('number', isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_URL:
                    echo $form->field($model, $attr, [
                        'template' => '{label}<div class="input-group"><span class="input-group-addon attached-button-chain"><i class="icon fa fa-chain"></i></span>{input}</div> {hint}{error}',
                    ])->textInput(isset($value['config']) ? $value['config'] : []);
                    $urlAttribute = $attr;
                    break;
                case Content::ATTR_TYPE_ADVANCED_TEXT:
                    echo $form->field($model, $attr, [
                        'options' => [
                            'class' => 'form-group col-sm-12'
                        ]
                    ])->widget(Widget::className(), isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_IMAGE:
                    echo $form->field($model, $attr)->widget(FileAPI::className(), isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_TEXT:
                    echo $form->field($model, $attr)->textarea(isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_DATE:
                    echo $form->field($model, $attr)->widget(DateTimePicker::classname(), isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_BOOLEAN:
                    echo $form->field($model, $attr)->checkbox();
                    break;
                case Content::ATTR_TYPE_DROPDOWN:
                    echo $form->field($model, $attr)->dropDownList(isset($value['items']) ? $value['items'] : [], isset($value['config']) ? $value['config'] : []);
                    break;
                default:
                    break;
            }
        }
        if ($titleAttribute != null && $urlAttribute != null):
            $this->registerJs('
             $(document).ready(function(){
                    $("#' . Html::getInputId($model, $titleAttribute) . '").syncTranslit({destination: "' . Html::getInputId($model, $urlAttribute) . '"});
                    $("#' . Html::getInputId($model, $urlAttribute) . '").syncTranslit({destination: "' . Html::getInputId($model, $urlAttribute) . '"});
                });
                $("span.attached-button-chain").on("click", function () {
                    if ($("#' . Html::getInputId($model, $urlAttribute) . '").attr("readonly")) {
                        $("#' . Html::getInputId($model, $urlAttribute) . '").attr("readonly", false);
                    } else {
                    $("#' . Html::getInputId($model, $urlAttribute) . '").attr("readonly", true);
                }
                });
            ');

        endif;
        ?>
        <div class="box-footer text-left">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'save-button']) ?>
        </div>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>

