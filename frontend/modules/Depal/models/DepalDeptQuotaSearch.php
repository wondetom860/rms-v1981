<?php

namespace frontend\modules\Depal\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Depal\models\DepalDeptQuota;

/**
 * DepalDeptQuotaSearch represents the model behind the search form of `frontend\modules\Depal\models\DepalDeptQuota`.
 */
class DepalDeptQuotaSearch extends DepalDeptQuota
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'setting_relId', 'dept_relId', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['q_percentage'], 'number'],
            [['data', 'remark'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = DepalDeptQuota::find();

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
            'setting_relId' => $this->setting_relId,
            'dept_relId' => $this->dept_relId,
            'q_percentage' => $this->q_percentage,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
