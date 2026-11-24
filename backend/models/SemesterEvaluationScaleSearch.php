<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluationScale;

/**
 * SemesterEvaluationScaleSearch represents the model behind the search form about `backend\models\SemesterEvaluationScale`.
 */
class SemesterEvaluationScaleSearch extends SemesterEvaluationScale
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ses_name', 'remark'], 'safe'],
            [['ses_maxPoint', 'ses_minPoint','ses_pass_status',], 'number'],
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
        $query = SemesterEvaluationScale::find();

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
            'ses_pass_status' => $this->ses_pass_status,
            'ses_maxPoint' => $this->ses_maxPoint,
            'ses_minPoint' => $this->ses_minPoint,
        ]);

        $query->andFilterWhere(['like', 'ses_name', $this->ses_name])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
