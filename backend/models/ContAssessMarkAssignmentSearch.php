<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContAssessMarkAssignment;

/**
 * ContAssessMarkAssignmentSearch represents the model behind the search form about `backend\models\ContAssessMarkAssignment`.
 */
class ContAssessMarkAssignmentSearch extends ContAssessMarkAssignment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ma_catagory_relId', 'ma_created_by', 'ma_updated_by'], 'integer'],
            [['ma_instance', 'ma_created_at', 'ma_updated_at'], 'safe'],
            [['ma_possiblePoint'], 'number'],
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
        $query = ContAssessMarkAssignment::find();

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
            'ma_catagory_relId' => $this->ma_catagory_relId,
            'ma_possiblePoint' => $this->ma_possiblePoint,
            'ma_created_at' => $this->ma_created_at,
            'ma_created_by' => $this->ma_created_by,
            'ma_updated_by' => $this->ma_updated_by,
            'ma_updated_at' => $this->ma_updated_at,
        ]);

        $query->andFilterWhere(['like', 'ma_instance', $this->ma_instance]);

        return $dataProvider;
    }
}
