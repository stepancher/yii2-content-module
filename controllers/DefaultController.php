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
        $type = Yii::$app->request->get('type', 'content');
        if ($url) {
            /** @var $contentContent Content */
            $contentModel = \Yii::$app->getModule($type)->model("Content", ['id' => $type]);
            $model = $contentModel->find()->where('url=:url', ['url' => $url])
                ->andWhere('EXTRACT(epoch from date_hide) > :time or date_hide is null', ['time' => time()])
                ->andWhere('EXTRACT(epoch from date_show) < :time or date_show is null', ['time' => time()])
                ->andWhere(['visible' => true, 'is_archive' => false])
                ->one();
            if (!$model) {
                throw new HttpException('404', Yii::t('content', 'Page not found'));
            }
            return $this->render(\Yii::$app->getModule($type)->view('show'), ['model' => $model]);
        } else {
            return $this->render(\Yii::$app->getModule($type)->view('list'));
        }
	}

    public function actionList()
    {
        return $this->render(\Yii::$app->getModule($this->module->id)->view('list'), ['moduleId' => $this->module->id]);
    }
}
