<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterRegistrationControlac;

/**
 * SemesterRegistrationControlacSearch represents the model behind the search form about `backend\models\SemesterRegistrationControlac`.
 */
class SemesterRegistrationControlacSearch extends SemesterRegistrationControlac
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'src_acadYear_relId', 'src_adm_relId', 'src_prog_relId', 'src_sem_relId', 'src_clslvl_relId', 'src_created_by', 'src_updated_by','src_dept_relId'], 'integer'],
            [['src_max_crdthr', 'src_min_crdthr'], 'number'],
            [['src_remark', 'src_created_at', 'src_updated_at'], 'safe'],
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
        $query = SemesterRegistrationControlac::find();

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
            'src_dept_relId' => $this->src_dept_relId,
            'src_acadYear_relId' => $this->src_acadYear_relId,
            'src_adm_relId' => $this->src_adm_relId,
            'src_prog_relId' => $this->src_prog_relId,
            'src_sem_relId' => $this->src_sem_relId,
            'src_clslvl_relId' => $this->src_clslvl_relId,
        ]);

        $query->andFilterWhere(['like', 'src_remark', $this->src_remark]);

        return $dataProvider;
    }
}
