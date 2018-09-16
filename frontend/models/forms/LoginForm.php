<?php
namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use budyaga\users\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            //['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('users', 'EMAIL_OR_USERNAME'),
            'password' => Yii::t('users', 'PASSWORD'),
            'rememberMe' => Yii::t('users', 'REMEMBER_ME'),
        ];
    }
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate() &&  $this->getUser()) {
            $user = $this->getUser();
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }else
            {
                Yii::$app->getSession()->setFlash('error_save', print_r($this->errors, true));
            }
            //return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            // ozharko добавить error Message
            if ($this->validate())
                return Yii::$app->getSession()->setFlash('error', Yii::t('users', 'USER_WITH_SUCH_EMAIL_DO_NOT_EXISTS'));;
        }
    }

    public function loginbyemail($email){
        //if ($this->validate()) {
            $useremail = User::findByEmailOrUserName($email);
            return Yii::$app->user->login($useremail, $this->rememberMe ? 3600 * 24 * 30 : 0);
        //} else {
        //    return false;
        //}
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmailOrUserName($this->email);
        }

        return $this->_user;
    }
}
