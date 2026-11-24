<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterRegistration2;

/**
 * SemesterRegistration2Search represents the model behind the search form about `frontend\models\SemesterRegistration2`.
 */
class SemesterRegistration2Search extends SemesterRegistration2
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        // 'semreg_std_relId',
        return [
            [['id',  'semreg_acadYear_relId', 'std_dept_relId', 'semreg_last_ssn_relId', 'std_prog_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_approved', 'semreg_approved_by', 'semreg_created_by', 'semreg_updated_by'], 'integer'],
            [['semreg_SGPA', 'semreg_CGPA'], 'number'],
            [['first_name','stdId'], 'string'],
            [['semreg_approved_at', 'semreg_created_at', 'semreg_updated_at'], 'safe'],
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
    public function search($params,$print=NULL)
    {
        $query = SemesterRegistration2::find()
            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId');

        // add conditions that should always apply here

        if (\Yii::$app->user->can('Student')) {
            // var_dump(1);die();
            $query->filterWhere(['student.std_user_relId' => \Yii::$app->user->identity->id]);
        }else if (\Yii::$app->user->can('DepartmentHead')) {
            $dept = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id])->inst_dep_relId;
            $query->filterWhere([
                    'student.std_dept_relId' => $dept,
                ]);
        }else if(\Yii::$app->user->can('Instructor')){
            // get advising gsets:
            $inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $advisorModel = new SemesterAdvisor();
            $gsetIds = $advisorModel->getAdvisingGsetIds($inst->id);

            $query->filterWhere(['student.std_group_realId' => $gsetIds]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($print) {
            $params = $_SESSION['printParams'];
            $query
                ->orderBy([
                'semreg_clslvl_relId' => SORT_DESC,
                'semreg_semester_relId' => SORT_DESC,
                'semreg_SGPA' => SORT_DESC,
                'semreg_CGPA' => SORT_DESC,
                'user.first_name' => SORT_ASC,
                'user.middle_name' => SORT_ASC,
            ]);
        }else{
            $_SESSION['printParams']  = $params;
            $query
                ->orderBy([
                'semreg_acadYear_relId' => SORT_DESC,
                'semreg_semester_relId' => SORT_DESC,
            ]);
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'semreg_std_relId' => $this->semreg_std_relId,
            'semreg_last_ssn_relId' => $this->semreg_last_ssn_relId,
            'student.std_prog_relid' => $this->std_prog_relId,
            'student.std_dept_relId' => $this->std_dept_relId,
            'semreg_acadYear_relId' => $this->semreg_acadYear_relId,
            'semreg_clslvl_relId' => $this->semreg_clslvl_relId,
            'semreg_semester_relId' => $this->semreg_semester_relId,
            'semreg_SGPA' => $this->semreg_SGPA,
            'semreg_CGPA' => $this->semreg_CGPA,
          
        ]);

        $query->andFilterWhere(['LIKE','student.std_regId' , $this->stdId]);

        $query->andFilterWhere(['OR',
                ['LIKE','user.first_name',$this->first_name],
                ['LIKE','user.middle_name' ,$this->first_name],
                ['LIKE','user.last_name' ,$this->first_name]
            ]);

        return $print ? $query->all():$dataProvider;
    }
}
