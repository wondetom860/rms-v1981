<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AssignInstructor2;

/**
 * AssignInstructor2Search represents the model behind the search form about `backend\models\AssignInstructor2`.
 */
class AssignInstructor2Search extends AssignInstructor2
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ai_coff_relId', 'ai_group_relId', 'ai_inst_relId', 'ai_TA_relId', 'ai_created_by', 'ai_updated_by'], 'integer'],
            [['ai_created_at', 'ai_updated_at'], 'safe'],
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
        $query = AssignInstructor2::find();

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
            'ai_coff_relId' => $this->ai_coff_relId,
            'ai_group_relId' => $this->ai_group_relId,
            'ai_inst_relId' => $this->ai_inst_relId,
            'ai_TA_relId' => $this->ai_TA_relId,
            'ai_created_at' => $this->ai_created_at,
            'ai_updated_at' => $this->ai_updated_at,
            'ai_created_by' => $this->ai_created_by,
            'ai_updated_by' => $this->ai_updated_by,
        ]);

        return $dataProvider;
    }
}
