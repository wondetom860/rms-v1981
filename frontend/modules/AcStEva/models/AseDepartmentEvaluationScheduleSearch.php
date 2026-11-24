<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule;

/**
 * AseDepartmentEvaluationScheduleSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule`.
 */
class AseDepartmentEvaluationScheduleSearch extends AseDepartmentEvaluationSchedule
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'header_relId', 'dept_relId', 'criteria_bundle_relId', 'start_date', 'end_date', 'finalized', 'status', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'safe'],
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
        $query = AseDepartmentEvaluationSchedule::find();

        // add conditions that should always apply here
        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            $query->andFilterWhere(['dept_relId' => $inst->inst_dep_relId]);
        }
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
            'header_relId' => $this->header_relId,
            'dept_relId' => $this->dept_relId,
            'criteria_bundle_relId' => $this->criteria_bundle_relId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'finalized' => $this->finalized,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
