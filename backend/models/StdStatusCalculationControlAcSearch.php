<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StdStatusCalculationControlAc;

/**
 * StdStatusCalculationControlAcSearch represents the model behind the search form about `backend\models\StdStatusCalculationControlAc`.
 */
class StdStatusCalculationControlAcSearch extends StdStatusCalculationControlAc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sssccAC_acadYear_relId', 'sssccAC_Fs', 'sssccAC_sem_relId', 'sssccAC_adm_relId', 'sssccAC_prog_relId', 'sssccAC_sssn_relId', 'sssccAC_clslvl_relId', 'sssccAC_created_by', 'sssccAC_updated_by'], 'integer'],
            [['sssccAC_max_CGPA', 'sssccAC_min_CGPA', 'sssccAC_max_SGPA', 'sssccAC_min_SGPA'], 'number'],
            [['sssccAC_remark', 'sssccAC_created_at', 'sssccAC_updated_at'], 'safe'],
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
        $query = StdStatusCalculationControlAc::find();

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
            'sssccAC_acadYear_relId' => $this->sssccAC_acadYear_relId,
            'sssccAC_Fs' => $this->sssccAC_Fs,
            'sssccAC_sem_relId' => $this->sssccAC_sem_relId,
            'sssccAC_adm_relId' => $this->sssccAC_adm_relId,
            'sssccAC_prog_relId' => $this->sssccAC_prog_relId,
            'sssccAC_sssn_relId' => $this->sssccAC_sssn_relId,
            'sssccAC_max_CGPA' => $this->sssccAC_max_CGPA,
            'sssccAC_min_CGPA' => $this->sssccAC_min_CGPA,
            'sssccAC_max_SGPA' => $this->sssccAC_max_SGPA,
            'sssccAC_min_SGPA' => $this->sssccAC_min_SGPA,
            'sssccAC_clslvl_relId' => $this->sssccAC_clslvl_relId,
            'sssccAC_created_by' => $this->sssccAC_created_by,
            'sssccAC_created_at' => $this->sssccAC_created_at,
            'sssccAC_updated_at' => $this->sssccAC_updated_at,
            'sssccAC_updated_by' => $this->sssccAC_updated_by,
        ]);

        $query->andFilterWhere(['like', 'sssccAC_remark', $this->sssccAC_remark]);

        return $dataProvider;
    }
}
