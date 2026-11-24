<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Instructor;

/**
 * InstructorSearch represents the model behind the search form about `backend\models\Instructor`.
 */
class InstructorSearch extends Instructor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inst_user_relId', 'inst_acr_relId', 'inst_acq_relId', 'inst_dep_relId', 'inst_active', 'inst_created_by', 'inst_updated_by', 'employment_type_relId'], 'integer'],
            [['inst_created_at', 'inst_updated_at', 'inst_name'], 'safe'],
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
        $query = Instructor::find()
            ->innerJoin('user', 'user.id=instructor.inst_user_relId');

        // add conditions that should always apply here

        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $query->andFilterWhere(['inst_dep_relId' => $instDept->inst_dep_relId])->andFilterWhere(['<>', 'inst_user_relId', Yii::$app->user->identity->id]);
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
            'employment_type_relId' => $this->employment_type_relId,
            'inst_dep_relId' => $this->inst_dep_relId,
            'inst_active' => $this->inst_active,
            'inst_acq_relId' => $this->inst_acq_relId,
            'inst_acr_relId' => $this->inst_acr_relId,
        ]);

        $query->andFilterWhere([
            'OR',
            ['like', 'user.first_name', $this->inst_name],
            ['like', 'user.middle_name', $this->inst_name],
            ['like', 'user.last_name', $this->inst_name]
        ]);

        return $dataProvider;
    }
}
