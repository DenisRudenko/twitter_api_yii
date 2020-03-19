<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Token;
use yii\web\Controller;

/**
 * TokenController implements the CRUD actions for Token model.
 */
class TokenController extends Controller
{
	const API_SECRET_KEY = "zlTHq9ujBMOV5scH2Ycx2GpNHIUYO8wrTwrxrA2vFlXDjIrrEp";
	const API_KEY = "fdLuyZzSgZOy02LkUzZ3BwoRE";
	const TOKEN_URL = "https://api.twitter.com/oauth2/token";
	public $bearer_token;

	/**
	 * One time exchange of API_SECRET_KEY and API_KEY for Bearer OAuth 2.0 access token
	 *
	 * @returns bearerToken
	 */
	function actionNew()
	{
		$ch   = curl_init();
		$body = 'grant_type=client_credentials';

		curl_setopt( $ch, CURLOPT_URL, self::TOKEN_URL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
		curl_setopt( $ch, CURLOPT_USERPWD, self::API_KEY . ':' . self::API_SECRET_KEY );

		$headers   = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8';
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$result = curl_exec( $ch );
		if ( curl_errno( $ch ) ) {
			echo 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );

		$bearer_token = json_decode($result)->access_token;

		/**
		 * save to database new generated bearerToken
		 */
		$model = Token::findOne(1);
		$model->token = $bearer_token;
		$model->save();

		return $bearer_token;
	}





}
