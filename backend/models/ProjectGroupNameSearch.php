<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProjectGroupName;

/**
 * ProjectGroupNameSearch represents the model behind the search form about `backend\models\ProjectGroupName`.
 */
class ProjectGroupNameSearch extends ProjectGroupName
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pg_size'], 'integer'],
            [['pg_name', 'remark'], 'safe'],
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
        $query = ProjectGroupName::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pg_size' => $this->pg_size,
        ]);

        $query->andFilterWhere(['like', 'pg_name', $this->pg_name])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
