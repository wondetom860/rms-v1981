<?php

namespace frontend\modules\ClassSchedule\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ClassSchedule\models\CsAvailableClassRoom;

/**
 * CsAvailableClassRoomSearch represents the model behind the search form of `frontend\modules\ClassSchedule\models\CsAvailableClassRoom`.
 */
class CsAvailableClassRoomSearch extends CsAvailableClassRoom
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'updated_at', 'created_at','white_board','black_board','room_pc','pulpit','projector','projector_board','num_of_seats'], 'integer'],
            [['room', 'description', 'data'], 'safe'],
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
        $query = CsAvailableClassRoom::find();

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
            'status' => $this->status,
            'white_board' => $this->white_board,
            'black_board' => $this->black_board,
            'projector' => $this->projector,
            'projector_board' => $this->projector_board,
            'pulpit' => $this->pulpit,
            'room_pc' => $this->room_pc,
            'num_of_seats' => $this->num_of_seats,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'room', $this->room])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
