<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WGroupSetup;
use backend\models\CourseOffering2;
use \common\components\CommonControllerFunctions as CCF;
/**
 * CourseOffering2Search represents the model behind the search form about `backend\models\CourseOffering2`.
 */
class CourseOffering2Search extends CourseOffering2
{
    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coff2_sem_relId', 'coff2_group_relId','coff2_acadYear_relId'], 'integer'],
            [['admission','department','clslvl','program'],'safe'],
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
        $query = CourseOffering2::find()->innerJoin('ac_active_programs','ac_active_programs.id=course_offering2.coff2_acProg_relId');

        // offering list should be for the groups asigned for the inst's / HoD's dept.

        // get HOD dept.
        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
            if ($instDept) {
                $instDept = $instDept->inst_dep_relId;
            }else{
              $instDept =  NULL;
            }

            // $this->__constructor();
            // find groups assigned for this dept.

            // 'gs_acadYear_relId'=>$this->acadYearId,
            // 'gs_sem_relId'=>$this->semId,
            $gset = WGroupSetup::find()
                ->where([
                    'gs_dept_relId'=>$instDept
                ])
                ->all();
            if ($gset && count($gset) > 0) {

                $gsetIds = [];
                foreach ($gset as $keyGroup) {
                    $gsetIds[] = $keyGroup->id;
                }
                // ->andFilterWhere([
                //     'coff2_acadYear_relId'=>$this->acadYearId])
                //     ->andFilterWhere(['coff2_sem_relId'=>$this->semId])
                $query=$query->andFilterWhere(['coff2_group_relId'=>$gsetIds]);
            }else{
                // HOD but nt asssigned any group for his dept::
                $query = $query->andWhere('id=0');
            }
        }
        $query = $query->orderBy([
            'coff2_acadYear_relId' => SORT_DESC,
            'coff2_sem_relId' => SORT_DESC,
        ]);

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
            'ac_active_programs.dept_relId' => $this->department,
            'ac_active_programs.class_level_relId' => $this->clslvl,
            'coff2_acadYear_relId' => $this->coff2_acadYear_relId,
            'coff2_sem_relId' => $this->coff2_sem_relId,
        ]);

        return $dataProvider;
    }
}
