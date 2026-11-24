<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContAssessMarkAssignment;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ContAssessMarkAssignmentSearch represents the model behind the search form about `backend\models\ContAssessMarkAssignment`.
 */
class ContAssessMarkAssignment2Search extends ContAssessMarkAssignment
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
            [['id', 'ma_catagory_relId', 'cat_coff_relId', 'ma_created_by', 'ma_updated_by'], 'integer'],
            [['ma_instance', 'ma_created_at', 'ma_updated_at'], 'safe'],
            [['ma_possiblePoint'], 'number'],
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

    public function getInstCourses(){
            if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
                $this->__constructor();
                $instDept = Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one();
                $instCourses = CourseOffering2Detail::find()
                    ->where(['OR',
                        ['coff2det_inst_relId'=>$instDept->id],
                        ['coff2det_TA_relId'=>$instDept->id]
                    ])
                    ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                    ->andWhere(['coff2_acadYear_relId'=>$this->acadYearId])
                    ->andWhere(['coff2_sem_relId'=>$this->semId])
                    ->all();
                    // $deptCourses = Course::find()->where(['course_dept_relId'=>$instDept->inst_dep_relId])->all();
                    $instCoursesArray = [];
                    if ($instCourses && count($instCourses) > 0) {
                        foreach ($instCourses as $key) {
                            $instCoursesArray[] = $key->coff2det_course_relId;
                        }
                    }
                return array_unique($instCoursesArray);
            }else{
                return NULL;
            }
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
        $query = ContAssessMarkAssignment2::find();

        $query = $query->innerJoin('cont_assess_catagory','cont_assess_catagory.id = cont_assess_mark_assignment.ma_catagory_relId')
            ->andFilterWhere(['cont_assess_catagory.cat_course_relId'=>$this->getInstCourses()])
            ->andFilterWhere(['ma_created_by'=>Yii::$app->user->identity->id]);
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
            'id' => $this->id,
            'ma_catagory_relId' => $this->ma_catagory_relId,
            'ma_possiblePoint' => $this->ma_possiblePoint,
            'ma_created_at' => $this->ma_created_at,
            'ma_created_by' => $this->ma_created_by,
            'ma_updated_by' => $this->ma_updated_by,
            'ma_updated_at' => $this->ma_updated_at,
            'cont_assess_mark_assignment.cat_coff_relId' => $this->cat_coff_relId,
        ]);

        $query->andFilterWhere(['like', 'ma_instance', $this->ma_instance]);

        return $dataProvider;
    }
}
