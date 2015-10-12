<?php

use yii\helpers\Html;
use vova07\imperavi\Widget;
use kartik\datetime\DateTimePicker;
use vova07\fileapi\Widget as FileAPI;
use stepancher\content\Content;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var stepancher\content\models\Content $content
 */
stepancher\content\assets\ContentAsset::register($this); //это ассет модуля, нет! мы не запихнем его ко всем ассетам
$this->registerJs('
 $(document).ready(function(){
        $("#content-header").syncTranslit({destination: "content-url"});
        $("#content-url").syncTranslit({destination: "content-url"});
    });
');

$this->title = $model->isNewRecord ? 'Создание раздела "' . Yii::$app->getModule($this->context->module->id)->title . '"' : 'Редактирование раздела "' . Yii::$app->getModule($this->context->module->id)->title . '"';
$this->params['breadcrumbs'][] =
    ['label' => Yii::$app->getModule($this->context->module->id)->title, 'url' => \yii\helpers\Url::toRoute('/content/admin')];

if ($model->isNewRecord) {
    $this->params['breadcrumbs'][] = Yii::t('content', 'Create content');
} else {
    $this->params['breadcrumbs'][] = $this->title;
}
?>

<div class="box">
    <div class="box-body">
         <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?php
        foreach($model->configAttributes as $attr => $value) {
            switch($value['type']) {
                case Content::ATTR_TYPE_STRING:
                    echo $form->field($model, $attr)->textInput(isset($value['config']) ? $value['config'] : []);
                    break;
                case Content::ATTR_TYPE_ADVANCED_TEXT:
                    echo $form->field($model, $attr)->widget(Widget::className(), isset($value['config']) ? $value['config'] : []);
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
                    echo Html::activeCheckbox($model, $attr);
                    echo Html::error($model, $attr);
                    break;
                case Content::ATTR_TYPE_DROPDOWN:
                    echo $form->field($model, $attr)->dropDownList(isset($value['items']) ? $value['items'] : [], isset($value['config']) ? $value['config'] : []);
                    break;
                default: break;
            }
        }
        ?>

         <div class="form-group">
             <?= Html::submitButton($model->isNewRecord ? Yii::t('content', 'Create') : Yii::t('content', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         </div>
 
         <?php \yii\widgets\ActiveForm::end(); ?>

    </div>
</div>
