<?php

use yii\helpers\Html;
use vova07\imperavi\Widget;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use vova07\fileapi\Widget as FileAPI;
use yii\web\JsExpression;

$content = Yii::$app->getModule('content')->model('Content');

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


$this->title = $model->isNewRecord ? Yii::t('content', 'Create content') : Yii::t('content', 'Update content');
$this->params['breadcrumbs'][] =
    ['label' => Yii::t('content', 'Content'), 'url' => \yii\helpers\Url::toRoute('/content/admin')];

if ($model->isNewRecord) {
    $this->params['breadcrumbs'][] = Yii::t('content', 'Create content');
} else {
    $this->params['breadcrumbs'][] = Yii::t('content', 'Update content', ['header' => $model->header]);
}
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-body">
         <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
         <?= $form->field($model, 'header')->textInput(['maxlength' => 255]) ?>
 
         <?= $form->field($model, 'short_text')->widget(Widget::className(), [
             'settings' => [
                 'lang' => 'ru',
                 'minHeight' => 200,
                 'imageManagerJson' => '/admin/content/images-get',
                 'imageUpload' => '/admin/content/image-upload',
                 'plugins' => [
                     'imagemanager',
                     'clips',
                     'fullscreen'
                 ]
             ]
 
         ]);
         ?>
         <?= $form->field($model, 'text')->widget(Widget::className(), [
             'settings' => [
                 'lang' => 'ru',
                 'minHeight' => 200,
                 'imageManagerJson' => '/admin/content/images-get',
                 'imageUpload' => '/admin/content/image-upload',
                 'plugins' => [
                     'imagemanager',
                     'clips',
                     'fullscreen'
                 ]
             ]
 
         ]);
         ?>
         <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
 
 
         <?= $form->field($model, 'description')->textarea(); ?>
         <?= $form->field($model, 'keywords')->textarea(); ?>
 
 
         <?php echo $form->field($model, 'image_file')->widget(
             FileAPI::className(),
             [
 
                 'settings' => [
                     'url' => ['/content/fileapi-upload'],
                     'maxSize'=>'1048576',
                     'imageTransform'=> [
                        'maxWidth'=> '177',
                         'maxHeight'=> '1000'
                     ]
                 ],
               /*  'callbacks' => [
                     'select' => [
                         new JsExpression('function (evt, data) {' .
                             'var errors = data.other[0].errors;'.
                                 'if( errors ){'.
                                     '$(".field-content-image_file").addClass(\'has-error\');'.
                                     '$(".field-content-image_file > .help-block").html(errors.maxSize);'.
                                 '}'.
                             '}')
                     ]
                 ],*/
             ]
         );
         ?>
         <?= $form->field($model, 'url')->textInput() ?>
         <?= $form->field($model, 'sort')->textInput() ?>
 
         <?= $form->field($model, 'date_show')->widget(DateTimePicker::classname(), [
             'options' => ['placeholder' => 'Введите время события ...'],
             'pluginOptions' => [
                 'format' => 'yyyy-mm-dd hh:ii:ss',
                 'autoclose' => true,
                 'todayBtn' => true,
                 'showMeridian' => true
             ]
         ]); ?>
         <?= $form->field($model, 'date_hide')->widget(DateTimePicker::classname(), [
             'options' => ['placeholder' => 'Введите время события...'],
             'pluginOptions' => [
                 'format' => 'yyyy-mm-dd hh:ii:ss',
                 'autoclose' => true,
                 'todayBtn' => true,
                 'showMeridian' => true,
             ]
         ]); ?>

        <?php if(\Yii::$app->getModule("content")->types): ?>
            <?= $form->field($model, 'type')->dropDownList($types) ?>
        <?php endif; ?>

        <?php if(\Yii::$app->getModule("content")->useI18n): ?>
            <?= $form->field($model, 'lang')->dropDownList(Yii::$app->params['languages']) ?>
        <?php endif; ?>

         <?= Html::activeCheckbox($model, 'visible'); ?>
         <?= Html::error($model, 'visible'); ?>
 
         <div class="form-group">
             <?= Html::submitButton($model->isNewRecord ? Yii::t('content', 'Create') : Yii::t('content', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         </div>
 
         <?php \yii\widgets\ActiveForm::end(); ?>

      </div>
     </div>
    </div>
</div>
