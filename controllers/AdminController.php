<?php

namespace stepancher\content\controllers;

use stepancher\content\models\Content;
use vova07\imperavi\actions\GetAction;

use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;
use vova07\fileapi\actions\UploadAction as FileAPIUpload;
use yii\filters\AccessControl;
use Faker\Provider\cs_CZ\DateTime;


class AdminController extends Controller
{
    /**
     * Поведение
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'], //все действия по работе со статьями только для админа
                    ]
                ],
            ],
        ];
    }
    /**
     * Загрузка/выгрузка изображений из текстового редактора
     * @return array
     */
    public function actions()
    {
        return [
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => \Yii::$app->getModule("content")->imageUrl,
                'path' => \Yii::$app->getModule("content")->imageDir,
                'type' => GetAction::TYPE_IMAGES,
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => \Yii::$app->getModule("content")->imageUrl,
                'path' => \Yii::$app->getModule("content")->imageDir,

            ],
            'fileapi-upload' => [
                'class' => FileAPIUpload::className(),
                'path' => \Yii::$app->getModule("content")->imageDir.'/temp'
            ]
        ];
    }

    /**Вывод всех статей в админке
     * @return string
     */
    public function actionIndex()
    {
        /** @var Content $model */
        $model = \Yii::$app->getModule("content")->model("Content");

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $model::find(),
            ]
        );
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**Создание статьи
     * @return string
     */
    public function actionCreate()
    {
        /** @var Content $model */
        $model =  \Yii::$app->getModule("content")->model("Content");
        if(\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post('Content');
//            var_dump(\Yii::$app->request->post('Content'));
//            echo "<br>";
//            var_dump($model->attributes);
//            die();
            if($model->save()){
                $this->redirect(Url::to('index'));
            }
        }
        return $this->render('update',['model'=>$model]);
    }

    /**
     * @param null $id - идентификатор статьи которую необходимо править
     * @return string
     */
    public function actionUpdate($id = null)
    {
        /** @var Content $model */
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
        if(\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post('Content');
            if($model->save()){
                $this->redirect(Url::to('index'));
            }
        }
        return $this->render('update',['model'=>$model]);
    }

    /**
     * @param $id идентификатор удаляемой статьи
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
        if ($model) {
            $model->delete();
        }
        $this->redirect('/admin/content');
    }
}
