<?php

namespace frontend\modules\ExitExam\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ExitExam\models\EtSetting;

/**
 * EtSettingSearch represents the model behind the search form of `frontend\modules\ExitExam\models\EtSetting`.
 */
class EtSettingSearch extends EtSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'max_trails', 'active_status', 'locked', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['setting_header_info', 'data'], 'safe'],
            [['pass_mark'], 'number'],
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
        $query = EtSetting::find();

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
            'max_trails' => $this->max_trails,
            'pass_mark' => $this->pass_mark,
            'active_status' => $this->active_status,
            'locked' => $this->locked,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'setting_header_info', $this->setting_header_info])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
