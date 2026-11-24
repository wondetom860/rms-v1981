<?php

namespace frontend\modules\ExitExam\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ExitExam\models\EtElligiblityTest;

/**
 * EtElligiblityTestSearch represents the model behind the search form of `frontend\modules\ExitExam\models\EtElligiblityTest`.
 */
class EtElligiblityTestSearch extends EtElligiblityTest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'semreg_relId', 'srd2_relId', 'reason', 'pass_status', 'trails', 'remaining_trails', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data', 'regId', 'full_name'], 'safe'],
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
        $query = EtElligiblityTest::find();
        $query->joinWith('semregRel')
            ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id=student.std_user_relId');
        // add conditions that should always apply here
        if (\Yii::$app->user->can('Student')) {
            $query
                ->andWhere(['user.id' => \Yii::$app->user->identity->id]);
        }
        $query->orderBy(['created_at' => SORT_DESC]);
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
            'semreg_relId' => $this->semreg_relId,
            'srd2_relId' => $this->srd2_relId,
            'reason' => $this->reason,
            'pass_status' => $this->pass_status,
            'trails' => $this->trails,
            'remaining_trails' => $this->remaining_trails,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);
        $query->andFilterWhere(['like', 'student.std_regId', $this->regId]);
        $query->andFilterWhere([
            'OR',
            ['like', 'first_name', $this->full_name],
            ['like', 'middle_name', $this->full_name],
            ['like', 'last_name', $this->full_name],
        ]);

        return $dataProvider;
    }
}
