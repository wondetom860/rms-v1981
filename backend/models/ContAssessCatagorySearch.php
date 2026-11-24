<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContAssessCatagory;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ContAssessCatagorySearch represents the model behind the search form about `backend\models\ContAssessCatagory`.
 */
class ContAssessCatagorySearch extends ContAssessCatagory
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
            [['id', 'cat_name_relId', 'cat_created_by', 'cat_course_relId', 'cat_updated_by'], 'integer'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
            [['cat_maxPoint'], 'number'],
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
        $query = ContAssessCatagory::find();

        // Get those courses only of the depts' ones. eg. 
        if (Yii::$app->user->can('DepartmentHead')) {
            $dept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
        $query = $query->innerJoin('course','course.id = cont_assess_catagory.cat_course_relId AND course.course_dept_relId = '.$dept)->orderBy('course.id ASC, cont_assess_catagory.cat_maxPoint ASC');


        }else if ( Yii::$app->user->can('Instructor')) {
            // load only the instructor's course, he/she is assigned in this semester.
             $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

            // initiate current year and semester.
            $this->__constructor();
            // get course assigned for the instructor.
            $coffs = CourseOffering2Detail::find()
                ->where(['coff2det_inst_relId'=> $inst->id])
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andWhere(['coff2_acadYear_relId'=>$this->acadYearId,'coff2_sem_relId'=> $this->semId,'coff2det_course_relId'=>$this->cat_course_relId])
                ->all();
            $cofArray = [];
            $courseIds = [];
            if ($coffs && count($coffs) > 0) {
                foreach ($coffs as $key) {
                    // $cofArray['id'] = $key->id;
                    $courseIds = $key->coff2det_course_relId;
                    // $cofArray['course_title'] = Course::findOne($key->coff2det_course_relId)->course_title;
                }
            }
            // return $cofArray;
            $query = $query->andFilterWhere(['cat_course_relId'=>$courseIds]);
        }else{
            $query = NULL;
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cat_maxPoint' => $this->cat_maxPoint,
            'cat_created_by' => $this->cat_created_by,
            'cat_course_relId' => $this->cat_course_relId,
            'cat_created_at' => $this->cat_created_at,
            'cat_updated_at' => $this->cat_updated_at,
            'cat_updated_by' => $this->cat_updated_by,
            'cat_name_relId' => $this->cat_name_relId,
        ]);

        return $dataProvider;
    }
}
