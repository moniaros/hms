<?php

class CheckinForm extends CFormModel
{
	public $id;
	public $username;
	public $gender;
	public $mobile;
	public $checkinDate;
	public $checkoutDate;


	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('id, username, mobile, checkinDate, checkoutDate', 'required'),
			array('id', 'match', 'pattern'=>'/^(\d{18,18}|\d{15,15}|\d{17,17}x)$/'),
			array('mobile', 'match', 'pattern'=>'/^1[3|5][0-9]\d{4,8}$/'),
			array('checkinDate', 'match', 'pattern'=>'/^\d{4}\d{2}\d{2}$/'),
			array('checkoutDate', 'match', 'pattern'=>'/^\d{4}\d{2}\d{2}$/'),
			array('gender', 'boolean'),
		//	array('username', 'match', 'pattern'=>'/w/'),
			// rememberMe needs to be a boolean
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
