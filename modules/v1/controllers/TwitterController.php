<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Twitter;
use app\modules\v1\models\Token;
use yii\web\Controller;

/**
 * TwitterController implements the CRUD actions for Twitter model.
 */
class TwitterController extends Controller {


	/**
	 * @param $user_name
	 *
	 */
	public function actionAdd( $user_name ) {

		$token        = Token::findOne( 1 )->token;
		$twitter_user = Twitter::findOne( [
			'user_name' => $user_name,
		] );

		if ( isset( $twitter_user ) ) {
			echo $twitter_user->user_name . 'is already in our Feed list';
			exit;
		} else {
			$curl = curl_init();

			curl_setopt_array( $curl, array(
				CURLOPT_URL            => "https://api.twitter.com/labs/2/users/by?usernames=" . $user_name,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => "",
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => "GET",
				CURLOPT_HTTPHEADER     => array(
					"Authorization: Bearer " . $token
				),
			) );

			$response = curl_exec( $curl );
			curl_close( $curl );


			$response_array = json_decode( $response, true );
			if ( isset( $response_array['errors'] ) ) {
				echo $response_array['errors'][0]['detail'];
				exit;
			}
			$model            = new Twitter();
			$model->id        = $response_array['data'][0]['id'];
			$model->user_name = $user_name;
			$model->save();
			echo $user_name . ' Saved to Twitter Feed List..';
			exit;
		}
	}

	/**
	 * @param $user_name
	 *
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionRemove( $user_name ) {


		$model = Twitter::findOne( [
			'user_name' => $user_name
		] );
		if ( isset( $model ) ) {
			$model->delete();
			echo $user_name . 'user was deleted' . PHP_EOL;
			exit;
		}

		echo $user_name . ' was not found in Feed list..';

		return false;
	}


	/**
	 * @return string
	 */
	public function actionFeed() {
		$feed = [];
		$token         = Token::findOne( 1 )->token;
		$twitter_users = Twitter::find()->all();
		$user_name     = '';

		foreach ( $twitter_users as $single_user ) {
			$user_name = $single_user->user_name;



			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_URL,
				'https://api.twitter.com/labs/2/tweets/search?query=from:' . $user_name . '&tweet.fields=entities' );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );

			$headers   = array();
			$headers[] = 'Authorization: Bearer ' . $token;
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

			$result = curl_exec( $ch );
			if ( curl_errno( $ch ) ) {
				echo 'Error:' . curl_error( $ch );
			}
			curl_close( $ch );


			$result = json_decode( $result, true );

			if(!isset($result['data'])){
				echo $user_name.' has protected account..';
				continue;
			}

			foreach ( $result['data'] as $feed_twit ) {

				$hashtags = [];
				if ( isset( $feed_twit['entities']['hashtags'] ) ) {
					foreach ( $feed_twit['entities']['hashtags'] as $hashtag ) {
						$hashtags[] = $hashtag['tag'];

					}
				}

				$feed['feed'][] = [
					'user'     => $user_name,
					'text'     => $feed_twit['text'],
					'hashtags' => $hashtags
				];
			}



		}
		return json_encode($feed);
	}
}
