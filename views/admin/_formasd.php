<?php

use yii\helpers\Html;
use vova07\imperavi\Widget;
use kartik\datetime\DateTimePicker;
use vova07\fileapi\Widget as FileAPI;

/* @var $this yii\web\View */
/* @var $model backend\models\Blocks */
/* @var $form yii\widgets\ActiveForm */
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
                    ],
                    'options' => [
                        'id' => 'content-short_text-' . Yii::$app->security->generateRandomString(6)
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
                    ],
                    'options' => [
                        'id' => 'content-text-' . Yii::$app->security->generateRandomString(6)
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
                    ],
                    'options' => [
                        'id' => 'content-date_show-' . Yii::$app->security->generateRandomString(6)
                    ]
                ]); ?>
                <?= $form->field($model, 'date_hide')->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'Введите время события...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'showMeridian' => true,
                    ],
                    'options' => [
                        'id' => 'content-date_hide-' . Yii::$app->security->generateRandomString(6)
                    ]
                ]); ?>

                <?php if(\Yii::$app->getModule("content")->types): ?>
                    <?= $form->field($model, 'type')->dropDownList(\stepancher\content\models\Content::getTypes()) ?>
                <?php endif; ?>

                <?= $form->field($model, 'created_by')->dropDownList(\common\models\User::getAllToList(), ['prompt' => '---']) ?>

                <?php if(\Yii::$app->getModule("content")->useI18n): ?>
                    <?= $form->field($model, 'lang')->dropDownList(Yii::$app->params['languages']) ?>
                <?php endif; ?>

                <?= Html::activeCheckbox($model, 'visible'); ?>
                <?= Html::error($model, 'visible'); ?>

                <?= Html::activeCheckbox($model, 'on_main'); ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('content', 'Create') : Yii::t('content', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php \yii\widgets\ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
