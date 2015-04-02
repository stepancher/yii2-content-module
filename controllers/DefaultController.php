<?php

namespace stepancher\content\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class DefaultController extends Controller
{
	/**
	 * @param $url статьи которую надо вывести
	 * @return string
	 * @throws HttpException
	 */
	public function actionShow($url)
	{
        if($url) {
            /** @var $contentContent Content */
            $contentModel = \Yii::$app->getModule("content")->model("Content");
            $model = $contentModel->find()->where('url=:url', ['url' => $url])
                ->andWhere('EXTRACT(epoch from date_hide) > :time or date_hide is null', ['time' => time()])
                ->andWhere('EXTRACT(epoch from date_show) < :time or date_show is null', ['time' => time()])
                ->andWhere('visible = true')
                ->one();
            if (!$model) {
                throw new HttpException('404', Yii::t('content', 'Page not found'));
            }
            return $this->render('show', ['model' => $model]);
        }else{
            return $this->render('list');
        }
	}
    public function actionList()
    {
        return $this->render('list');
    }
}
