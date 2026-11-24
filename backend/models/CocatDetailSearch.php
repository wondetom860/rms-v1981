<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CocatDetail;

/**
 * CocatDetailSearch represents the model behind the search form about `backend\models\CocatDetail`.
 */
class CocatDetailSearch extends CocatDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cocatDet_cocat_relId', 'cocatDet_course_relId', 'cocatDet_created_by', 'cocatDet_updated_by'], 'integer'],
            [['cocatDet_created_at', 'cocat|Det_updated_at'], 'safe'],
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
        $query = CocatDetail::find();

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
            'cocatDet_cocat_relId' => $this->cocatDet_cocat_relId,
            'cocatDet_course_relId' => $this->cocatDet_course_relId,
            'cocatDet_created_by' => $this->cocatDet_created_by,
            'cocatDet_created_at' => $this->cocatDet_created_at,
            'cocatDet_updated_by' => $this->cocatDet_updated_by,
            'cocat|Det_updated_at' => $this->cocat|Det_updated_at,
        ]);

        return $dataProvider;
    }
}
