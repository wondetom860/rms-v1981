<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GradeScale;

/**
 * GradeScaleSearch represents the model behind the search form about `backend\models\GradeScale`.
 */
class GradeScaleSearch extends GradeScale
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gs_created_by', 'gs_updated_by','gs_forProgram','gs_scale_status'], 'integer'],
            [['gs_min', 'gs_max', 'gs_gradePoint','gs_pass_status','gs_clearable'], 'number'],
            [['gs_ltr_grade', 'gs_remark', 'gs_created_at', 'gs_updated_at'], 'safe'],
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
        $query = GradeScale::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'gs_min' => $this->gs_min,
            'gs_max' => $this->gs_max,
            'gs_forProgram' => $this->gs_forProgram,
            'gs_pass_status' => $this->gs_pass_status,
            'gs_clearable' => $this->gs_clearable,
            'gs_gradePoint' => $this->gs_gradePoint,
            'gs_scale_status' => $this->gs_scale_status,
        ]);

        $query->andFilterWhere(['like', 'gs_ltr_grade', $this->gs_ltr_grade])
            ->andFilterWhere(['like', 'gs_remark', $this->gs_remark]);

        return $dataProvider;
    }
}
