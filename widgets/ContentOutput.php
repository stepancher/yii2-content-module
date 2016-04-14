<?php
namespace stepancher\content\widgets;

use stepancher\content\models\Content;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * Class ContentOutput
 * отображает созданные ContentModule статьи двумя способами,
 * если $onlyLinks задан true то отображаются только ссылки на статьи
 * если false топревью статей
 * @package stepancher\content\widgets
 */
class ContentOutput extends Widget
{
    public $onlyLinks = false;
    public $moduleId;
    public $isArchive = false; //выводить архивные статьи

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        /** @var Content $model */
        $model = \Yii::$app->getModule($this->moduleId)->model("Content", ['id' => $this->moduleId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $model::find()->where('EXTRACT(epoch from date_hide) > :time or date_hide is null', ['time' => time()])
                ->andWhere('EXTRACT(epoch from date_show) < :time or date_show is null', ['time' => time()])
                ->andWhere(['visible' => true, 'is_archive' => $this->isArchive])
                ->orderBy('sort'),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        if ($this->onlyLinks) {
            return $this->render('links', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('preview', ['dataProvider' => $dataProvider, 'moduleId' => $this->moduleId]);
        }
    }
}
