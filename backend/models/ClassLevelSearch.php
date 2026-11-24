<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ClassLevel;

/**
 * ClassLevelSearch represents the model behind the search form about `backend\models\ClassLevel`.
 */
class ClassLevelSearch extends ClassLevel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'clslvl_admission_relId', 'clslvl_program_relId', 'clslvl_status', 'clslvl_created_by', 'clslvl_updated_by'], 'integer'],
            [['clslvl_name', 'clslvl_created_at', 'clslvl_updated_at'], 'safe'],
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
        $query = ClassLevel::find();

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
            'clslvl_admission_relId' => $this->clslvl_admission_relId,
            'clslvl_program_relId' => $this->clslvl_program_relId,
            'clslvl_status' => $this->clslvl_status,
            'clslvl_created_by' => $this->clslvl_created_by,
            'clslvl_created_at' => $this->clslvl_created_at,
            'clslvl_updated_by' => $this->clslvl_updated_by,
            'clslvl_updated_at' => $this->clslvl_updated_at,
        ]);

        $query->andFilterWhere(['like', 'clslvl_name', $this->clslvl_name]);

        return $dataProvider;
    }
}
