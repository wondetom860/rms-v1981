<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RmsUserLink;

/**
 * RmsUserLinkSearch represents the model behind the search form of `backend\models\RmsUserLink`.
 */
class RmsUserLinkSearch extends RmsUserLink
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'person_relId', 'created_by', 'updated_by'], 'integer'],
            [['rms_user_id', 'rms_inst_id', 'rms_std_id', 'created_at', 'updated_at', 'data'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = RmsUserLink::find();

        if (!\Yii::$app->user->can('Admin')) {
            $query->where(['rms_user_id' => \Yii::$app->user->identity->id]);
        }

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
            'person_relId' => $this->person_relId,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'rms_user_id', $this->rms_user_id])
            ->andFilterWhere(['like', 'rms_inst_id', $this->rms_inst_id])
            ->andFilterWhere(['like', 'rms_std_id', $this->rms_std_id])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
