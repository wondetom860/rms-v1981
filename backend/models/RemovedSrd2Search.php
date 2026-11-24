<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RemovedSrd2;

/**
 * RemovedSrd2Search represents the model behind the search form of `backend\models\RemovedSrd2`.
 */
class RemovedSrd2Search extends RemovedSrd2
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'semreg_relId', 'std_relId', 'grade_relId', 'coff2det_relId', 'hod_approved', 'hod_approved_at', 'hod_approved_by', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['srd2_serialized', 'cont_ass_marks', 'data'], 'safe'],
            [['total_mark'], 'number'],
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
        $query = RemovedSrd2::find();

        // add conditions that should always apply here
        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = Instructor::find()->where(['inst_user_relId' => \Yii::$app->user->identity->id])->one();
            $query->innerJoin('course_offering2_detail', 'course_offering2_detail.id = removed_srd2.coff2det_relId')
                ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
                ->andWhere(['course.course_dept_relId' => $inst->inst_dep_relId]);
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
            'coff2det_relId' => $this->coff2det_relId,
            'semreg_relId' => $this->semreg_relId,
            'std_relId' => $this->std_relId,
            'total_mark' => $this->total_mark,
            'grade_relId' => $this->grade_relId,
            'hod_approved' => $this->hod_approved,
            'hod_approved_at' => $this->hod_approved_at,
            'hod_approved_by' => $this->hod_approved_by,
            'reg_approved' => $this->reg_approved,
            'reg_approved_at' => $this->reg_approved_at,
            'reg_approved_by' => $this->reg_approved_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        // $query->andFilterWhere(['like', 'srd2_serialized', $this->srd2_serialized])
        //     ->andFilterWhere(['like', 'cont_ass_marks', $this->cont_ass_marks])
        //     ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
