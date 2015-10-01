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
        $type = \Yii::$app->request->get('type', null); // Тип статьи

        $title = null; // Тип статьи
        $query = $model::find()->where(['is_archive' => false]);
        if($type) {
            $query->andWhere(['type' => $type]); // Выборка по типу статьи
            $title = \Yii::$app->getModule("content")->types[$type];
        }

        $dataProviders = array();
        if(\Yii::$app->getModule("content")->useI18n) {
            // Разбиение по языкам
            foreach (\Yii::$app->params['languages'] as $lang => $name) {
                $queryTMP = clone $query;
                $dataProviders[$name] = new ActiveDataProvider(
                    [
                        'query' => $queryTMP->andWhere(['lang' => $lang]),
                        'sort'=> ['defaultOrder' => ['sort' => SORT_DESC]]
                    ]
                );
            }
        } else {
            $dataProviders[' '] = new ActiveDataProvider(
                [
                    'query' => $query,
                    'sort'=> ['defaultOrder' => ['sort' => SORT_DESC]]
                ]
            );
        }

        return $this->render(\Yii::$app->getModule('content')->view('index'), [
            'dataProviders' => $dataProviders,
            'title' => $title,
            'type' => $type
        ]);
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
                $this->redirect(Url::to('index?type='.\Yii::$app->request->get('type', null)));
            }
        }

        // Список типов статей
        $types = array();
        $firstType = \Yii::$app->request->get('type', null);
        if(\Yii::$app->getModule("content")->types) {
            foreach(\Yii::$app->getModule("content")->types as $i => $type) {
                if($firstType && $firstType == $i) {
                    $types = [$i => $type] + $types;
                } else {
                    $types[$i] = $type;
                }
            }
        }

        return $this->render(\Yii::$app->getModule('content')->view('update'),['model'=>$model, 'types'=>$types]);
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
                $this->redirect(Url::to('index?type='.\Yii::$app->request->get('type', null)));
            }
        }

        $types = array();
        if(\Yii::$app->getModule("content")->types) {
            foreach(\Yii::$app->getModule("content")->types as $i => $type) {
                $types[$i] = $type;
            }
        }

        return $this->render(\Yii::$app->getModule('content')->view('update'),['model'=>$model, 'types'=>$types]);
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
        $this->redirect('/admin/content/archives?type='.\Yii::$app->request->get('type', null));
    }

    /**
     * @param $id идентификатор архивируемой статьи
     * @throws \Exception
     */
    public function actionArchive($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
        if ($model) {
            $model->updateAll(['is_archive' => true], ['id' => $id]);
        }
        $this->redirect(Url::to('index?type='.\Yii::$app->request->get('type', null)));
    }

    /**
     * @param $id идентификатор восстанавливаемой статьи
     * @throws \Exception
     */
    public function actionUnarchive($id)
    {
        /** @var $model Content */
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
        if ($model) {
            $model->updateAll(['is_archive' => false], ['id' => $id]);
        }

        $this->redirect('/admin/content/archives?type='.\Yii::$app->request->get('type', null));
    }

    /**
     * Групповые действия со статьями
     * @return \yii\web\Response
     */
    public function actionGroupAction()
    {
        $url = \Yii::$app->request->post('url', null);
        $model = \Yii::$app->request->post('model', null);

        if($model) {
            $model = (new $model);

            $ItemSelected = \Yii::$app->request->post(preg_replace('/^(.*)\\\/i', '', $model->className()), null);

            if ($ItemSelected && count($ItemSelected) > 0) {
                $actions = [
                    self::ACTION_DELETE => '',
                    self::ACTION_ARCHIVE => '',
                    self::ACTION_UNARCHIVE => '',
                ];
                $action = key(array_intersect_key($actions, \Yii::$app->request->post()));

                switch ($action) {
                    case self::ACTION_DELETE:
                        $model->deleteAll(['id' => $ItemSelected]);
                        break;
                    case self::ACTION_ARCHIVE:
                        $model->updateAll(['is_archive' => true], ['id' => $ItemSelected]);
                        break;
                    case self::ACTION_UNARCHIVE:
                        $model->updateAll(['is_archive' => false], ['id' => $ItemSelected]);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this->redirect($url);
    }

    /**
     * Изменение видимости статьи
     * @return string
     */
    public function actionVisible()
    {
        $id = \Yii::$app->request->post('id', null);
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
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
        $model = \Yii::$app->getModule("content")->model("Content")->findOne($id);
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
        /** @var Content $model */
        $model = \Yii::$app->getModule("content")->model("Content");
        $type = \Yii::$app->request->get('type', null); // Тип статьи

        $title = null; // Тип статьи
        $query = $model::find()->where(['is_archive' => true]);
        if($type) {
            $query->andWhere(['type' => $type]); // Выборка по типу статьи
            $title = \Yii::$app->getModule("content")->types[$type];
        }

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
//                'sort'=> ['defaultOrder' => ['sort' => SORT_ASC]]
            ]
        );

        return $this->render(\Yii::$app->getModule('content')->view('archive'), [
            'dataProvider' => $dataProvider,
            'title' => $title,
            'type' => $type
        ]);
    }
}
