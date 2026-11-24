<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StdStatusCalculationControlDefault;

/**
 * StdStatusCalculationControlDefaultSearch represents the model behind the search form about `backend\models\StdStatusCalculationControlDefault`.
 */
class StdStatusCalculationControlDefaultSearch extends StdStatusCalculationControlDefault
{

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['sssccdAdmRel.admission_name','sssccdProgRel.program_name','sssccdSemRel.semester_name','sssccdSssnRel.sssn_name','sssccdClslvlRel.clslvl_num','sssccdCreatedBy.username']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sssccd_adm_relId', 'sssccd_prog_relId', 'sssccd_sem_relId', 'sssccd_sssn_relId', 'sssccd_created_by', 'sssccd_updated_by', 'sssccd_clslvl_relId'], 'integer'],
            [['sssccd_max_CGPA', 'sssccd_min_CGPA', 'sssccd_max_SGPA', 'sssccd_min_SGPA'], 'number'],
            [['sssccd_remark', 'sssccd_created_at', 'sssccdAdmRel.admission_name', 'sssccd_updated_at','sssccdProgRel.program_name', 'sssccdCreatedBy.username','sssccdSemRel.semester_name','sssccdSssnRel.sssn_name','sssccdClslvlRel.clslvl_num'], 'safe'],
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
        $query = StdStatusCalculationControlDefault::find();

        // add conditions that should always apply here

        $query = $query->joinWith(['sssccdSemRel','sssccdAdmRel','sssccdProgRel','sssccdSssnRel','sssccdClslvlRel']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
            // 'sort' => [
            //     'defaultOrder' => [
            //         'sssccd_clslvl_relId' => SORT_ASC, 
            //     ]
            // ],
        ]);

        $dataProvider->sort->attributes['sssccdAdmRel.admission_name'] = [
            'asc' => ['admission_name' => SORT_ASC],
            'desc' => ['admission_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sssccdProgRel.program_name'] = [
            'asc' => ['program_name' => SORT_ASC],
            'desc' => ['program_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sssccdSemRel.semester_name'] = [
            'asc' => ['semester_name' => SORT_ASC],
            'desc' => ['semester_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sssccdSssnRel.sssn_name'] = [
            'asc' => ['sssn_name' => SORT_ASC],
            'desc' => ['sssn_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sssccdCreatedBy.username'] = [
            'asc' => ['username' => SORT_ASC],
            'desc' => ['username' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sssccdClslvlRel.clslvl_num'] = [
            'asc' => ['clslvl_num' => SORT_ASC],
            'desc' => ['clslvl_num' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'sssccd_max_CGPA' => $this->sssccd_max_CGPA,
            'sssccd_min_CGPA' => $this->sssccd_min_CGPA,
            'sssccd_created_by' => $this->sssccd_created_by,
            'sssccd_created_at' => $this->sssccd_created_at,
            'sssccd_updated_at' => $this->sssccd_updated_at,
            'sssccd_updated_by' => $this->sssccd_updated_by,
            'sssccd_max_SGPA' => $this->sssccd_max_SGPA,
            'sssccd_min_SGPA' => $this->sssccd_min_SGPA,
        ]);

        $query->andFilterWhere(['LIKE', 'admission_name', $this->getAttribute('sssccdAdmRel.admission_name')]);
        $query->andFilterWhere(['LIKE', 'program_name', $this->getAttribute('sssccdProgRel.program_name')]);
        $query->andFilterWhere(['LIKE', 'sssn_name', $this->getAttribute('sssccdSssnRel.sssn_name')]);
        $query->andFilterWhere(['semester_name'=>$this->getAttribute('sssccdSemRel.semester_name')]);
        $query->andFilterWhere(['LIKE', 'username', $this->getAttribute('sssccdCreatedBy.username')]);
        $query->andFilterWhere(['LIKE', 'clslvl_num', $this->getAttribute('sssccdClslvlRel.clslvl_num')]);

        $query->andFilterWhere(['like', 'sssccd_remark', $this->sssccd_remark]);

        return $dataProvider;
    }
}
