<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\HonorListControl;

/**
 * HonorListControlSearch represents the model behind the search form about `\frontend\models\HonorListControl`.
 */
class HonorListControlSearch extends HonorListControl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hl_created_by', 'hl_created_at', 'hl_updated_at', 'hl_updated_by','hl_acadYear_relId','hl_sem_relId'], 'integer'],
            [['hl_maxSGPA', 'hl_minSGPA'], 'number'],
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
        $query = HonorListControl::find();

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
            'hl_maxSGPA' => $this->hl_maxSGPA,
            'hl_acadYear_relId' => $this->hl_acadYear_relId,
            'hl_sem_relId' => $this->hl_sem_relId,
            'hl_minSGPA' => $this->hl_minSGPA,
            'hl_created_by' => $this->hl_created_by,
            'hl_created_at' => $this->hl_created_at,
            'hl_updated_at' => $this->hl_updated_at,
            'hl_updated_by' => $this->hl_updated_by,
        ]);

        return $dataProvider;
    }
}

