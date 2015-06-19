<?php

namespace stepancher\content;

use yii\base\InvalidConfigException;


/**
 * Class Content - модуль для добавления статей на сайт, подробнее читай README.md
 * @package stepancher\content
 */
class Content extends \yii\base\Module
{

    /**
     * @var array List Chars for russian word
     */
    private static $rustable =array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',  'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );

    public $controllerNamespace = 'stepancher\content\controllers';

    /**
     * @var string $imageDir - Папка для хранения картинок
     */
    public $imageDir;

    /**
     * @var string $imageUrl - Путь до картинки для веба
     */
    public $imageUrl;

    /**
     * @var string $types - Типы статей
     */
    public $types = null;

    /**
     * @var array Model classes, e.g., ["Content" => "content\models\Content"]
     * Usage:
     *   $user = Yii::$app->getModule("content")->model("Content", $config);
     *   (equivalent to)
     *   $user = new content\models\Content($config);
     *
     * The model classes here will be merged with/override the [[getDefaultModelClasses()|default ones]]
     */
    public $modelClasses = [] ;

    /**
     * @var array Storage for models based on $modelClasses
     */
    protected  $_models;


    /**
     * @var int Short text length
     */
    public $shortTextLength = 250;
    public function init()
    {
        parent::init();
        $this->checkModuleProperties();
        $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        if (empty(\Yii::$app->i18n->translations['content'])) {
            \Yii::$app->i18n->translations['content'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/messages',
                //'forceTranslation' => true,
            ];
        }
    }

    /**
     * Check for valid module properties
     */
    protected function checkModuleProperties()
    {
        $className = get_called_class();
        if(!$this->imageDir){
            throw new InvalidConfigException("{$className}: \$imageDir must be defined");
        }
        $this->imageDir = \Yii::getAlias($this->imageDir);

        if(!file_exists($this->imageDir)){
            throw new InvalidConfigException("{$className}: Directory {$this->imageDir} is not exist");
        }

        if(!is_writable($this->imageDir)){
            throw new InvalidConfigException("{$className}: Directory {$this->imageDir} must be writable");
        }
    }

    /**
     * Get object instance of model
     *
     * @param string $name
     * @param array  $config
     * @return ActiveRecord
     */
    public function model($name, $config = [])
    {
        // return object if already created
        if (!empty($this->_models[$name])) {
            return $this->_models[$name];
        }
        // create model and return it
        $className = $this->modelClasses[ucfirst($name)];
        $this->_models[$name] = \Yii::createObject(array_merge(["class" => $className], $config));
        return $this->_models[$name];
    }

    /**
     * Get default model classes
     */
    protected function getDefaultModelClasses()
    {
        // use single quotes so nothing gets escaped
        return [
            'Content'       => 'stepancher\content\models\Content',
        ];
    }


    /**
     * @param null $str
     * @param string $spacechar
     * @return mixed|null|string
     */
    public static function rus2trans($str = null, $spacechar = '_')
    {
        if ($str)
        {
            $str = strtolower(strtr($str, self::$rustable));
            $str = preg_replace('~[^-a-z0-9_]+~u', $spacechar, $str);
            $str = trim($str, $spacechar);
            return $str;
        } else {
            return;
        }
    }

    /**
     * Substring string by space
     * @param $str
     * @param $count
     * @return string
     */

    public function subString($str, $count = 255,$endChars = '...') {

        if (strlen($str)>$count){
            $substring = mb_substr($str, 0, $count);
            $lastSpace = mb_strrpos($substring,' ');
            return mb_substr($substring,0,$lastSpace).$endChars;

        }
        return $str;
    }

}
