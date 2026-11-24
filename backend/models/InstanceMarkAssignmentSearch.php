<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\InstanceMarkAssignment;
use \common\components\CommonControllerFunctions as CCF;

/**
 * InstanceMarkAssignmentSearch represents the model behind the search form about `backend\models\InstanceMarkAssignment`.
 */
class InstanceMarkAssignmentSearch extends InstanceMarkAssignment
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

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
            [['id', 'ima_catagorySetup_relId','ima_course', 'ima_created_by', 'ima_updated_by'], 'integer'],
            [['ima_possiblePoint'], 'number'],
            [['ima_instanceName', 'ima_created_at', 'ima_updated_at'], 'safe'],
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
        $this->__constructor();
        $query = InstanceMarkAssignment::find()
            ->innerJoin('cont_assess_catagory2','cont_assess_catagory2.id = instance_mark_assignment.ima_catagorySetup_relId')
            ->filterWhere([
                'cont_assess_catagory2.cat_acadYear_relId'=>$this->acadYearId,
                'cont_assess_catagory2.cat_sem_relId'=>$this->semId,
            ]);

        $this->load($params);

        // var_dump($this);die();

        $query = $query->andFilterWhere(['cat_course_relId'=>$this->ima_course]);
        // add conditions that should always apply here

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if (is_null($this->ima_course)) {
        //     $dataProvider = new ActiveDataProvider([
        //         'query' => [],
        //     ]);
        // }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ima_catagorySetup_relId' => $this->ima_catagorySetup_relId,
            'ima_possiblePoint' => $this->ima_possiblePoint,
            'ima_created_by' => $this->ima_created_by,
            'ima_created_at' => $this->ima_created_at,
            'ima_updated_by' => $this->ima_updated_by,
            'ima_updated_at' => $this->ima_updated_at,
        ]);

        $query->andFilterWhere(['like', 'ima_instanceName', $this->ima_instanceName]);

        return $dataProvider;
    }
}
