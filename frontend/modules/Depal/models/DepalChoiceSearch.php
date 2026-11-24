<?php

namespace frontend\modules\Depal\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Depal\models\DepalChoice;

/**
 * DepalChoiceSearch represents the model behind the search form of `frontend\modules\Depal\models\DepalChoice`.
 */
class DepalChoiceSearch extends DepalChoice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'std_relId', 'setting_relId', 'secured_dept_relId', 'finalizad', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['dept_selected', 'data','std_regId'], 'safe'],
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
        $query = DepalChoice::find()
            ->innerJoin('student','student.id=depal_choice.std_relId');

        // add conditions that should always apply here
        if (\Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::findOne(['std_user_relId' => \Yii::$app->user->id,'std_semester_status' => "ACTIVE"]);
            if ($std) {
                $query->andFilterWhere(['std_relId' => $std->id]);
            }else{
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message',"None-Active student for this semester");
                $query->andFilterWhere(['std_relId' => 0]);
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
            'std_relId' => $this->std_relId,
            'setting_relId' => $this->setting_relId,
            'secured_dept_relId' => $this->secured_dept_relId,
            'finalizad' => $this->finalizad,
        ]);

        $query->andFilterWhere(['like', 'dept_selected', $this->dept_selected])
            ->andFilterWhere(['like', 'student.std_regId', $this->std_regId]);

        return $dataProvider;
    }
}
