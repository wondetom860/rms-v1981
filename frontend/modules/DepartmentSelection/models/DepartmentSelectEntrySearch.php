<?php

namespace frontend\modules\DepartmentSelection\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntry;

/**
 * DepartmentSelectEntrySearch represents the model behind the search form of `frontend\modules\DepartmentSelection\models\DepartmentSelectEntry`.
 */
class DepartmentSelectEntrySearch extends DepartmentSelectEntry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'student_relId', 'setting_relId', 'approved_dept_relId', 'approved_by', 'created_by', 'updated_by'], 'integer'],
            [['dept_choice', 'approved_at', 'created_at', 'updated_at', 'data'], 'safe'],
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
        $query = DepartmentSelectEntry::find();

        // add conditions that should always apply here

        if (\Yii::$app->user->can('Student')) {
            $student = \backend\models\Student::findOne(['std_user_relId' =>\Yii::$app->user->identity->id]);

            if ($student && count($student) > 0) {
                $query->andFilterWhere(['student_relId' => $student->id]);
            }else{
                $query->andFilterWhere(['student_relId' => NULL]);
            }
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
            'id' => $this->id,
            'student_relId' => $this->student_relId,
            'setting_relId' => $this->setting_relId,
            'approved_dept_relId' => $this->approved_dept_relId,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'dept_choice', $this->dept_choice])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
