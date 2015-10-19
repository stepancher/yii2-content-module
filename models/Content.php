<?php

namespace stepancher\content\models;

use common\models\User;
use mkv\metatag\behaviors\MetatagBehavior;
use Yii;
use vova07\fileapi\behaviors\UploadBehavior;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

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
 * @property string $lang
 * @property string $on_main
 * @property string $created_by
 * @property string $updated_by
 */
class Content extends \yii\db\ActiveRecord
{
    public $preview_url;

    /**
     * The ID of this module
     * @var string
     */
    public $moduleId;

    /**
     * @param array $config - Name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        $this->moduleId = isset($config['id']) ? $config['id'] : 'content';
        parent::__construct();
    }

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
                        'path' => \Yii::$app->getModule($this->moduleId)->imageDir.'/',
                        'tempPath' => \Yii::$app->getModule($this->moduleId)->imageDir.'/temp',
                        'url' => \Yii::$app->getModule($this->moduleId)->imageUrl.'/'
                    ]
                ]
            ],
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
        $rules = [
            [['header','url'], 'required', 'message' => \Yii::t('content', 'Cannot be blank')],
            [['url'], 'unique', 'message' => \Yii::t('content', 'Must be ubique')],
            [['short_text', 'keywords', 'text', 'url', 'description'], 'string'],
            [['visible', 'on_main'], 'boolean'],
            [['sort'], 'integer', 'message' => \Yii::t('content', 'Must be an integer')],
            [['create_time', 'update_time','date_show', 'date_hide', 'created_by', 'updated_by'], 'safe'],
            [['header', 'title'], 'string', 'max' => 250, 'tooLong' => \Yii::t('content', 'maximum character', ['n' => 250])],
            ['image_file', 'safe'/*, 'skipOnEmpty' => true*/]

        ];

        if(\Yii::$app->getModule($this->moduleId)->useI18n) {
            $rules[] = ['lang', 'safe'];
        }

        return $rules;
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
            'lang' => \Yii::t('content', 'Language'),
            'on_main' => \Yii::t('content', 'Show on main'),
            'created_by' => \Yii::t('content', 'Created By'),
            'updated_by' => \Yii::t('content', 'Updated By'),
        ];
    }

    /**
     * Перед валидацией если short_text пустой отрезаем первые shortTextLength символов от основного текста
     * @return bool
     */
    public function beforeValidate()
    {
        $module = Yii::$app->getModule($this->moduleId);
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

        // Сбрасываем кэш
        if(Yii::$app->getModule($this->moduleId)->useCache) {
            TagDependency::invalidate(Yii::$app->cache, $this->className());
        }

        $time = new \DateTime();
        $time = $time->format('Y-m-d H:i:s');

        $this->created_by = $this->created_by ? $this->created_by : Yii::$app->user->id;
        $this->updated_by = Yii::$app->user->id;
        if ($this->isNewRecord) {
            $this->create_time = $time;
            $this->update_time = $time;
            $this->date_show = $this->date_show ? $this->date_show : $time;
            $this->sort = $this->sort ? $this->sort : 1000;
        } else {
            $this->update_time = $time;
        }
        return true;
    }

    /**
     * Возвращает url картинки
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_file ? '/upload/content/'.$this->image_file : null;
    }

    /**
     * Возвращает автора статьи
     * @return \common\models\User
     */
    public function getUser()
    {
        return $this
            ->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        // Сбрасываем кэш
        if(Yii::$app->getModule($this->moduleId)->useCache) {
            TagDependency::invalidate(Yii::$app->cache, $this->className());
        }

        return parent::beforeDelete();
    }

    /**
     * Возвращает настройки для атрибутов
     * @return array
     */
    public function getConfigAttributes()
    {
        return [
            'id' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_HIDE,
                'visible' => false
            ],
            'header' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_TITLE,
                'config' => ['maxlength' => 255]
            ],
            'url' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_URL,
                'visible' => false,
                'config' => [
                    'readonly'=>true
                ]
            ],
            'short_text' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_ADVANCED_TEXT,
                'config' => [
                    'settings' => ArrayHelper::merge(isset(Yii::$app->params['imperaviSettings']) ? Yii::$app->params['imperaviSettings'] : [], [
                        'imageManagerJson' => '/admin/' . $this->moduleId . '/images-get',
                        'imageUpload' => '/admin/' . $this->moduleId . '/image-upload',
                        'fileUpload' => '/admin/' . $this->moduleId . '/file-upload',
                        'fileManagerJson' => '/admin/' . $this->moduleId . '/files-get',
                    ]),
                    'options' => [
                        'id' => 'content-short_text-' . Yii::$app->security->generateRandomString(6)
                    ]
                ],
                'visible' => false
            ],
            'text' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_ADVANCED_TEXT,
                'config' => [
                    'settings' => ArrayHelper::merge(isset(Yii::$app->params['imperaviSettings']) ? Yii::$app->params['imperaviSettings'] : [], [
                        'imageManagerJson' => '/admin/' . $this->moduleId . '/images-get',
                        'imageUpload' => '/admin/' . $this->moduleId . '/image-upload',
                        'fileUpload' => '/admin/' . $this->moduleId . '/file-upload',
                        'fileManagerJson' => '/admin/' . $this->moduleId . '/files-get',
                    ]),
                    'options' => [
                        'id' => 'content-text-' . Yii::$app->security->generateRandomString(6)
                    ]
                ],
                'visible' => false
            ],
            'title' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_STRING,
                'config' => ['maxlength' => 255],
                'visible' => false
            ],
            'description' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_TEXT,
                'visible' => false
            ],
            'keywords' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_TEXT,
                'visible' => false
            ],
            'image_file' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_IMAGE,
                'config' => [
                    'settings' => [
                        'url' => ['/' . $this->moduleId . '/fileapi-upload'],
                        'maxSize'=>'1048576',
                        'imageTransform'=> [
                            'maxWidth'=> '177',
                            'maxHeight'=> '1000'
                        ]
                    ],
                ]
            ],
            'sort' => ['type' => \stepancher\content\Content::ATTR_TYPE_INTEGER],
            'date_show' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_DATE,
                'config' => [
                    'options' => ['placeholder' => 'Введите время события ...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'showMeridian' => true
                    ]
                ]
            ],
            'date_hide' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_DATE,
                'config' => [
                    'options' => ['placeholder' => 'Введите время события...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'showMeridian' => true,
                    ]
                ],
                'visible' => false
            ],
            'created_by' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_DROPDOWN,
                'config' => ['prompt' => '---'],
                'items' => \common\models\User::getAllToList(),
                'visible' => false
            ],
            'lang' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_DROPDOWN,
                'items' => \Yii::$app->getModule($this->moduleId)->languages,
                'visible' => false
            ],
            'visible' => ['type' => \stepancher\content\Content::ATTR_TYPE_BOOLEAN],
            'on_main' => [
                'type' => \stepancher\content\Content::ATTR_TYPE_BOOLEAN,
                'visible' => false
            ],
        ];
    }

    /**
     * Возвращает настройку для атрибута
     * @param $attr
     * @return mixed
     */
    public function getConfigAttribute($attr)
    {
        $attributes = self::getConfigAttributes();
        return isset($attributes[$attr]) ? $attributes[$attr] : null;
    }
}
