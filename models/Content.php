<?php

namespace stepancher\content\models;

use Faker\Provider\cs_CZ\DateTime;
use Yii;
use vova07\fileapi\behaviors\UploadBehavior;

/**
 * This is the model class for table "content".
 *
 * @property integer $id
 * @property string $header
 * @property string $title
 * @property string $image_file
 * @property string $short_text
 * @property string $text
 * @property string $url
 * @property string $description
 * @property boolean $visible
 * @property string $create_time
 * @property string $update_time
 * @property string $date_show
 * @property string $date_hide
 * @property string $keywords
 * @property string $sort
 * @property string $type
 */
class Content extends \yii\db\ActiveRecord
{
    public $preview_url;

    public function behaviors()
    {
        return [
            'uploadBehavior' => [
                'class' => UploadBehavior::className(),
                'attributes' => [
  /*                  'preview_url' => [
                        'path' => \Yii::$app->getModule("content")->imageDir.'/vova/previews',
                        'tempPath' => \Yii::$app->getModule("content")->imageDir.'/vova/temp/previews',
                        'url' => \Yii::$app->getModule("content")->imageUrl.'/vova/previews'
                    ],
  */                  'image_file' => [
                        'path' => \Yii::$app->getModule("content")->imageDir.'/',
                        'tempPath' => \Yii::$app->getModule("content")->imageDir.'/temp',
                        'url' => \Yii::$app->getModule("content")->imageUrl.'/'
                    ]
                ]
            ]
        ];
    }



    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * Правила валидирования
     */
    public function rules()
    {
        return [
            [['header','url'], 'required', 'message' => \Yii::t('content', 'Cannot be blank')],
            [['url'], 'unique', 'message' => \Yii::t('content', 'Must be ubique')],
            [['short_text', 'keywords', 'text', 'url', 'description'], 'string'],
            [['visible'], 'boolean'],
            [['sort'], 'integer', 'message' => \Yii::t('content', 'Must be an integer')],
            [['create_time', 'update_time','date_show', 'date_hide', 'type'], 'safe'],
            [['header', 'title'], 'string', 'max' => 250, 'tooLong' => \Yii::t('content', 'maximum character', ['n' => 250])],
            ['image_file', 'safe'/*, 'skipOnEmpty' => true*/]

        ];
    }

    /** Валидатор дат "текущая наименьшая"
     * @param $attribute  поле проверяемой даты
     * @param $params
     */
    public function dateTodayMin($attribute, $params)
    {
        if ($this->$attribute) {
            $datetime1 = date_create(date('Y-m-d H:i:s'));
            $datetime2 = date_create($this->$attribute);

            if ($datetime1 > $datetime2) {
                $this->addError($attribute, 'Минимальная дата текущая');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header' => \Yii::t('content', 'Header'),
            'title' => \Yii::t('content', 'Title'),
            'short_text' => \Yii::t('content', 'Short text'),
            'text' => \Yii::t('content', 'Text'),
            'url' => \Yii::t('content', 'Url'),
            'description' => \Yii::t('content', 'Description'),
            'visible' => \Yii::t('content', 'Visible'),
            'create_time' => \Yii::t('content', 'Create Time'),
            'update_time' => \Yii::t('content', 'Update Time'),
            'image_file' => \Yii::t('content', 'Image File'),
            'keywords' => \Yii::t('content', 'Keywords'),
            'sort' => \Yii::t('content', 'Sort'),
            'date_show' => \Yii::t('content', 'Date show'),
            'date_hide' => \Yii::t('content', 'Date hide'),
            'type' => \Yii::t('content', 'Type'),
        ];
    }

    /**
     * Перед валидацией если short_text пустой отрезаем первые shortTextLength символов от основного текста
     * @return bool
     */
    public function beforeValidate()
    {
        $module = Yii::$app->getModule('content');
        if (trim($this->short_text) === '')
            $this->short_text = $module->subString($this->text, $module->shortTextLength);

        return true;
    }

    /**
     * Перед сохранением задаем время создания/изменения
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $time = new \DateTime();
        $time = $time->format('Y-m-d H:i:s');
        if ($this->isNewRecord) {
            $this->create_time = $time;
            $this->update_time = $time;
        } else {
            $this->update_time = $time;
        }



        return true;
    }
}
