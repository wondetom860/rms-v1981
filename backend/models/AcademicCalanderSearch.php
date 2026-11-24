<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AcademicCalander;

use common\components\CommonControllerFunctions as CCF;

/**
 * AcademicCalanderSearch represents the model behind the search form about `backend\models\AcademicCalander`.
 */
class AcademicCalanderSearch extends AcademicCalander
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
            [['id', 'acad_action_relId', 'acad_acadYear_relId', 'acad_adm_relId', 'acad_prog_relId', 'acad_clslvl_relId', 'acad_sem_relId', 'acad_status', 'acad_created_by', 'acad_updated_by'], 'integer'],
            [['acad_starttime', 'acad_finishtime', 'acad_created_at', 'acad_updated_at'], 'safe'],
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
        $query = AcademicCalander::find();

        // $this->__constructor();
        $query = $query->orderBy([
            'acad_finishtime' => SORT_DESC,
            'acad_status'=>SORT_DESC
        ]);
        // $query = $query->where(['acad_acadYear_relId'=>$this->acadYearId]);

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
            'acad_action_relId' => $this->acad_action_relId,
            'acad_acadYear_relId' => $this->acad_acadYear_relId,
            'acad_adm_relId' => $this->acad_adm_relId,
            'acad_prog_relId' => $this->acad_prog_relId,
            'acad_clslvl_relId' => $this->acad_clslvl_relId,
            'acad_sem_relId' => $this->acad_sem_relId,
            'acad_starttime' => $this->acad_starttime,
            'acad_finishtime' => $this->acad_finishtime,
            'acad_status' => $this->acad_status,
            'acad_created_by' => $this->acad_created_by,
            'acad_created_at' => $this->acad_created_at,
            'acad_updated_at' => $this->acad_updated_at,
            'acad_updated_by' => $this->acad_updated_by,
        ]);

        return $dataProvider;
    }
}
