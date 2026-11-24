<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\User;

/**
 * UserSearch represents the model behind the search form about `backend\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'updated_by', 'created_by'], 'integer'],
            [['username', 'first_name', 'middle_name', 'last_name', 'gender', 'auth_key', 'user_type', 'password_hash', 'password_reset_token', 'email', 'nationality', 'dob', 'photo_num', 'created_at', 'updated_at','phone_mobile','role','OTP'], 'safe'],
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
        $query = User::find()->leftJoin('auth_assignment','user.id=auth_assignment.user_id');

        if (\Yii::$app->user->can('UniversityRegistrar')) {
            $query->andFilterWhere(['OR',
                ['status' => self::STATUS_UNAPPROVED],
                ['activated' => self::INACTIVE_ACCOUNT],
                ['activated_by' => \Yii::$app->user->id]
            ])->andFilterWhere(['item_name' => 'Student']);
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
            'status' => $this->status,
            'dob' => $this->dob,
            'OTP' => $this->OTP,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'user_type', $this->user_type])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'nationality', $this->nationality])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile]);

        return $dataProvider;
    }
}
