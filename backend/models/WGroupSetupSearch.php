<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WGroupSetup;
use \common\components\CommonControllerFunctions as CCF;

/**
 * WGroupSetupSearch represents the model behind the search form about `backend\models\WGroupSetup`.
 */
class WGroupSetupSearch extends WGroupSetup
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
            [['id', 'gs_group_relId', 'gs_adm_relId', 'gs_prog_relId', 'gs_acadYear_relId', 'gs_dept_relId', 'gs_clslvl_relId', 'gs_spec_relId', 'gs_sem_relId', 'gs_created_by', 'gs_updated_by', 'gs_status', 'gs_size', 'gs_internship'], 'integer'],
            [['gs_created_at', 'gs_updated_at', 'remark'], 'safe'],
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
    public function search($params,$acadYearId,$semId)
    {
        $query = WGroupSetup::find();

        // var_dump($acadYearId,$semId);
        // die();

        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('DepartmentHead')) {
            $instObj = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            if ($instObj) {
                $query->andFilterWhere(['gs_dept_relId' => $instObj->inst_dep_relId]);
            }else{
               return null; 
           }            
        }

        // add conditions that should always apply here

        if (isset($acadYearId) && !empty($acadYearId)) {
            //$query = $query->andFilterWhere(['gs_acadYear_relId'=>$acadYearId]);
        }

        if (isset($semId) && !empty($semId)) {
            //$query = $query->andFilterWhere(['gs_sem_relId'=>$semId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->orderBy([
            'gs_acadYear_relId' => SORT_DESC,
            'gs_sem_relId' => SORT_DESC,
            'gs_clslvl_relId' => SORT_ASC,
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'gs_internship' => $this->gs_internship,
            'gs_group_relId' => $this->gs_group_relId,
            'gs_adm_relId' => $this->gs_adm_relId,
            'gs_prog_relId' => $this->gs_prog_relId,
            'gs_acadYear_relId' => $this->gs_acadYear_relId,
            'gs_dept_relId' => $this->gs_dept_relId,
            'gs_clslvl_relId' => $this->gs_clslvl_relId,
            'gs_spec_relId' => $this->gs_spec_relId,
            'gs_sem_relId' => $this->gs_sem_relId,
            'gs_created_by' => $this->gs_created_by,
            'gs_updated_by' => $this->gs_updated_by,
            'gs_created_at' => $this->gs_created_at,
            'gs_updated_at' => $this->gs_updated_at,
            'gs_status' => $this->gs_status,
            'gs_size' => $this->gs_size,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
