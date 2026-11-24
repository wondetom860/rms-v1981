<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContAssessCatagory2;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ContAssessCatagory2Search represents the model behind the search form about `backend\models\ContAssessCatagory2`.
 */
class ContAssessCatagory2Search extends ContAssessCatagory2
{

    public $courseId;
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
            [['id', 'cat_coff_relId','cat_course_relId', 'cat_catagory_relId', 'cat_created_by', 'cat_updated_by'], 'integer'],
            [['cat_maxPoint'], 'number'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
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
        // initiate current year and semester.
        $this->__constructor();

        $query = ContAssessCatagory2::find();
        $query->innerJoin('course','course.id = cont_assess_catagory2.cat_course_relId');
        $query->andFilterWhere(['cat_acadYear_relId'=>$this->acadYearId,'cat_sem_relId'=>$this->semId]);

        // Get those courses only of the depts' ones. eg. 
        if (Yii::$app->user->can('DepartmentHead')) {

            $dept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            $query->andFilterWhere(['course.course_dept_relId'=>$dept]);

        }else if ( Yii::$app->user->can('Instructor')) {
            // load only the instructor's course, he/she is assigned in this semester.
             $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

            // get course assigned for the instructor.
            $coffs = CourseOffering2Detail::find()
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andWhere([
                    'coff2_acadYear_relId'=>$this->acadYearId,
                    'coff2_sem_relId'=> $this->semId,
                    'coff2det_inst_relId'=> $inst->id
                ])
                ->all();
            $cofArray = [];
            $courseIds = [];
            if ($coffs && count($coffs) > 0) {
                foreach ($coffs as $key) {
                    // $cofArray['id'] = $key->id;
                    $courseIds[] = $key->coff2det_course_relId;
                    // $cofArray['course_title'] = Course::findOne($key->coff2det_course_relId)->course_title;
                }
            }
            // return $cofArray;
            $query->andFilterWhere(['cat_course_relId'=>$courseIds]);
            // var_dump($query->all());die();
        }else{
            $query->where('0=1');
        }
        // add conditions that should always apply here

        $query->orderBy('course.course_title ASC, cat_maxPoint ASC');
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
            'cat_coff_relId' => $this->cat_coff_relId,
            'cat_catagory_relId' => $this->cat_catagory_relId,
            'cat_maxPoint' => $this->cat_maxPoint,
            'cat_created_at' => $this->cat_created_at,
            'cat_course_relId' => intval($this->cat_course_relId),
            'cat_created_by' => $this->cat_created_by,
            'cat_updated_by' => $this->cat_updated_by,
            'cat_updated_at' => $this->cat_updated_at,
        ]);

        return $dataProvider;
    }
}
