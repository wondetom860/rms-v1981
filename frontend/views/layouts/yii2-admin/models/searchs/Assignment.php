<?php

namespace mdm\admin\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AssignmentSearch represents the model behind the search form about Assignment.
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Assignment extends Model
{
    public $id;
    public $username;
    public $fullname;
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'username', 'fullname','role'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-admin', 'ID'),
            'username' => Yii::t('rbac-admin', 'Username'),
            'fullname' => Yii::t('rbac-admin', 'Full Name'),
            'name' => Yii::t('rbac-admin', 'Name'),
        ];
    }

    /**
     * Create data provider for Assignment model.
     * @param  array                        $params
     * @param  \yii\db\ActiveRecord         $class
     * @param  string                       $usernameField
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $class, $usernameField)
    {
        $query = $class::find()->innerJoin('auth_assignment','user.id=auth_assignment.user_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['auth_assignment.item_name' => $this->role]);
        $query->andFilterWhere(['like', $usernameField, $this->username]);
        $query->andFilterWhere(['OR',['like', 'first_name', $this->fullname],['like', 'middle_name', $this->fullname]]);

        return $dataProvider;
    }
}
