<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AcademicQualification;

/**
 * AcademicQualificationSearch represents the model behind the search form about `backend\models\AcademicQualification`.
 */
class AcademicQualificationSearch extends AcademicQualification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'acq_created_by', 'acq_updated_by'], 'integer'],
            [['acq_name', 'acq_desc', 'acq_created_at', 'acq_updated_at'], 'safe'],
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
        $query = AcademicQualification::find();

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
            'acq_created_by' => $this->acq_created_by,
            'acq_created_at' => $this->acq_created_at,
            'acq_updated_by' => $this->acq_updated_by,
            'acq_updated_at' => $this->acq_updated_at,
        ]);

        $query->andFilterWhere(['like', 'acq_name', $this->acq_name])
            ->andFilterWhere(['like', 'acq_desc', $this->acq_desc]);

        return $dataProvider;
    }
}
