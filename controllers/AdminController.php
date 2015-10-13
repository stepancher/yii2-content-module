<?php

namespace stepancher\content\controllers;

use stepancher\content\models\Content;
use vova07\imperavi\actions\GetAction;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use vova07\fileapi\actions\UploadAction as FileAPIUpload;
use yii\filters\AccessControl;

class AdminController extends Controller
{
    const
        ACTION_DELETE = 'delete',
        ACTION_ARCHIVE = 'archive',
        ACTION_UNARCHIVE = 'unarchive';

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
                        'roles' => ['r_content'],
                        'actions' => ['index', 'archives', 'images-get']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['w_content'],
                        'actions' => ['create', 'update', 'delete', 'archive', 'unarchive', 'group-action', 'sort', 'visible',
                        'fileapi-upload', 'image-upload']
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
                'url' => \Yii::$app->getModule($this->module->id)->imageUrl,
                'path' => \Yii::$app->getModule($this->module->id)->imageDir,
                'type' => GetAction::TYPE_IMAGES,
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => \Yii::$app->getModule($this->module->id)->imageUrl,
                'path' => \Yii::$app->getModule($this->module->id)->imageDir,

            ],
            'fileapi-upload' => [
                'class' => FileAPIUpload::className(),
                'path' => \Yii::$app->getModule($this->module->id)->imageDir.'/temp'
            ]
        ];
    }

    /**Вывод всех статей в админке
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = Yii::$app->getModule($this->module->id)->model('ContentSearch', ['id' => $this->module->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_archive' => false]);

        return $this->render(Yii::$app->getModule('user')->view('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**Создание статьи
     * @return string
     */
    public function actionCreate()
    {
        /** @var Content $model */
        $model =  \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id]);
        if(\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post(preg_replace('/(.*)\\\/i', '', $model->className()));
            if($model->save()){
                $this->redirect(Url::to('index'));
            }
        }

        return $this->render(\Yii::$app->getModule($this->module->id)->view('update'), ['model' => $model, 'moduleId' => $this->module->id]);
    }

    /**
     * @param null $id - идентификатор статьи которую необходимо править
     * @return string
     */
    public function actionUpdate($id = null)
    {
        /** @var Content $model */
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if(\Yii::$app->request->isPost) {
            $model->attributes = \Yii::$app->request->post(preg_replace('/(.*)\\\/i', '', $model->className()));
            if($model->save()){
                $this->redirect(Url::to('index'));
            }
        }

        return $this->render(\Yii::$app->getModule($this->module->id)->view('update'), ['model' => $model, 'moduleId' => $this->module->id]);
    }

    /**
     * @param $id идентификатор удаляемой статьи
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if ($model) {
            $model->delete();
        }
        $this->redirect('/admin/' . $this->module->id . '/archives');
    }

    /**
     * @param $id идентификатор архивируемой статьи
     * @throws \Exception
     */
    public function actionArchive($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if ($model) {
            $model->updateAll(['is_archive' => true], ['id' => $id]);
        }
        $this->redirect(Url::to('index'));
    }

    /**
     * @param $id идентификатор восстанавливаемой статьи
     * @throws \Exception
     */
    public function actionUnarchive($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if ($model) {
            $model->updateAll(['is_archive' => false], ['id' => $id]);
        }

        $this->redirect('/admin/' . $this->module->id . '/archives');
    }

    /**
     * Групповые действия со статьями
     * @return \yii\web\Response
     */
    public function actionGroupAction()
    {
        $url = Yii::$app->request->post('url', null);
        $model = Yii::$app->request->post('model', null);
        $action = Yii::$app->request->post('action', null);
        $keys = explode(',', Yii::$app->request->post('keys', null));

        if($model) {
            $model = (new $model);

            if ($keys && count($keys) > 0) {
                switch ($action) {
                    case self::ACTION_DELETE:
                        $model->deleteAll(['id' => $keys]);
                        break;
                    case self::ACTION_ARCHIVE:
                        $model->updateAll(['is_archive' => true], ['id' => $keys]);
                        break;
                    case self::ACTION_UNARCHIVE:
                        $model->updateAll(['is_archive' => false], ['id' => $keys]);
                        break;
                    default: break;
                }
            }

            return $this->redirect($url);
        }

        return false;
    }

    /**
     * Изменение видимости статьи
     * @return string
     */
    public function actionVisible()
    {
        $id = \Yii::$app->request->post('id', null);
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if($model) {
            $model->visible = \Yii::$app->request->post('visible');
            if($model->save()) {
                return json_encode(['type' => 'success', 'message' => 'OK']);
            }
        }
        return json_encode(['type' => 'danger', 'message' => 'Error']);
    }

    public function actionSort()
    {
        $id = \Yii::$app->request->post('id', null);
        $model = \Yii::$app->getModule($this->module->id)->model("Content", ['id' => $this->module->id])->findOne($id);
        if($model) {
            $model->sort = \Yii::$app->request->post('sort', null);
            if($model->save()) {
                return json_encode(['type' => 'success', 'message' => 'OK']);
            }
        }
        return json_encode(['type' => 'danger', 'message' => 'Error']);
    }

    public function actionArchives()
    {
        $searchModel = Yii::$app->getModule($this->module->id)->model('ContentSearch', ['id' => $this->module->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_archive' => true]);

        return $this->render(Yii::$app->getModule('user')->view('archive'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
