<?php

namespace stepancher\content\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContentSearch represents the model behind the search form about `stepancher\content\models\Content`.
 */
class ContentSearch extends Content
{
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
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['header', 'title', 'image_file', 'short_text', 'text', 'url', 'description', 'keywords', 'create_time', 'update_time', 'date_show', 'date_hide', 'type', 'lang'], 'safe'],
            [['visible', 'is_archive', 'on_main'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $module = \Yii::$app->getModule($this->moduleId)->model("Content", ['id' => $this->moduleId]);
        $query = $module::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'visible' => $this->visible,
            'sort' => $this->sort,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'date_show' => $this->date_show,
            'date_hide' => $this->date_hide,
            'is_archive' => $this->is_archive,
            'on_main' => $this->on_main,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image_file', $this->image_file])
            ->andFilterWhere(['like', 'short_text', $this->short_text])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'lang', $this->lang]);

        return $dataProvider;
    }
}
