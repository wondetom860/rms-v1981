<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DepartmentHead;

/**
 * DepartmentHeadSearch represents the model behind the search form about `backend\models\DepartmentHead`.
 */
class DepartmentHeadSearch extends DepartmentHead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dh_inst_relId', 'dh_dept_relId', 'dh_status', 'created_by', 'updated_by'], 'integer'],
            [['dh_assig_date', 'dh_terminated_date', 'created_at', 'updated_at'], 'safe'],
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
        $query = DepartmentHead::find()
            ->innerJoin('department','department.id = department_head.dh_dept_relId')
            ->orderBy(['dh_status'=>SORT_DESC,'dept_name' => SORT_ASC,'dh_assig_date' => SORT_DESC]);

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
            'dh_inst_relId' => $this->dh_inst_relId,
            'dh_dept_relId' => $this->dh_dept_relId,
            'dh_assig_date' => $this->dh_assig_date,
            'dh_terminated_date' => $this->dh_terminated_date,
            'dh_status' => $this->dh_status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
