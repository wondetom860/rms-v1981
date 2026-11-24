<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EducationInformation;

/**
 * EducationInformationSeasrch represents the model behind the search form about `backend\models\EducationInformation`.
 */
class EducationInformationSeasrch extends EducationInformation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ei_enteranceMarkPoint', 'ei_created_by', 'ei_updated_by'], 'integer'],
            [['ei_G10_doc_scan', 'ei_G12_doc_scan', 'ei_created_at', 'ei_updated_at'], 'safe'],
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
        $query = EducationInformation::find();

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
            'ei_enteranceMarkPoint' => $this->ei_enteranceMarkPoint,
            'ei_created_at' => $this->ei_created_at,
            'ei_updated_at' => $this->ei_updated_at,
            'ei_created_by' => $this->ei_created_by,
            'ei_updated_by' => $this->ei_updated_by,
        ]);

        $query->andFilterWhere(['like', 'ei_G10_doc_scan', $this->ei_G10_doc_scan])
            ->andFilterWhere(['like', 'ei_G12_doc_scan', $this->ei_G12_doc_scan]);

        return $dataProvider;
    }
}
