<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User{
    
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $profile_id;
    public $qa;
	public $hash;
	public $client;
	public $google_id;
	public $youtube;
	
    public function  __construct() {
        $this->ci =& get_instance();

        /*if(isset($_COOKIE['pieces_hash'])){
        	$this->loadByGoogleHash($_COOKIE['pieces_hash']);
        	define('URL_LOGIN', '');
        	return true;
        }
*/
		require_once LIBRARIES_PATH.'google-api-php-client/src/Google_Client.php';
		require_once LIBRARIES_PATH.'google-api-php-client/src/contrib/Google_YouTubeService.php';
		require_once LIBRARIES_PATH.'google-api-php-client/src/contrib/Google_Oauth2Service.php';

		@session_start();

		if (isset($_SESSION['cv_email'])){
			return true;
		}

		$OAUTH2_CLIENT_ID = '131772673693-vbhprjltgflhp2rdvl0r69h1j06iq0l3.apps.googleusercontent.com';
		$OAUTH2_CLIENT_SECRET = '6RsYKi9xtpQHENk9P3F8GYdN';

		$this->client = new Google_Client();
		$this->client->setClientId($OAUTH2_CLIENT_ID);
		$this->client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$redirect = filter_var('http://8amarketing.com/rrhh/curriculums/auth/setlogin');
		$this->client->setRedirectUri($redirect);
		$this->client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/picasa'));
		//$this->client->setUseObjects(true);
		$plus = new Google_Oauth2Service($this->client);
		

		if (isset($_GET['code'])) {
		  /*if (strval($_SESSION['state']) !== strval($_GET['state'])) {
		    die('The session state did not match.');
		  }*/

		  $this->client->authenticate();
		  $_SESSION['token'] = $this->client->getAccessToken();
		  header('Location: ' . $redirect);
		}

		if (isset($_SESSION['token'])) {
		    $this->client->setAccessToken($_SESSION['token']);
        
		}

		if ($this->client->getAccessToken()) {
			$_SESSION['token'] = $this->client->getAccessToken();
			
			$userinfo = $plus->userinfo->get();

			$_SESSION['cv_email'] = $userinfo['email'];
			setcookie('cv_email', $userinfo['email'], time()+3600);
			//print_r($userinfo);
			define('URL_LOGIN', '');

		} else {
			$state = mt_rand();
			$this->client->setState($state);
			$_SESSION['state'] = $state;
			$authUrl = $this->client->createAuthUrl();
			define('URL_LOGIN', $authUrl);

		}
    }

    public function loadByGoogleEmail($email){
        $user = $this->ci->db->get_where('users', array('email'=>$email))->row();
        if($user){
            $this->loadUser($user);
        }
    }

    public function loadByGoogleHash($hash){
        $user = $this->ci->db->get_where('users', array('hash'=>$hash))->row();
        if($user){
            $this->loadUser($user);
        }
    }

    public function loadUser($user){
    	$this->id = $user->id;
	    $this->firstname = $user->firstname;
	    $this->lastname = $user->lastname;
	    $this->email = $user->email;
	    $this->profile_id = $user->profile_id;
	    $this->qa = $user->qa;
	    $this->hash = $user->hash;
	    $this->allow_notifications = $user->allow_notifications;
    }

    public function isLoggIn(){
        return (!empty($this->id))? true : false;
    }

    public function isAdmin(){
        return ($this->profile_id == PROFILE_ADMINISTRADOR)? true : false;
    }

    

	public function getYoutubeVideos(){
		
		$videos = array();
		
	
		try {
		    $channelsResponse = $this->youtube->channels->listChannels('contentDetails', array(
		      'mine' => 'true',
		    ));

		    foreach ($channelsResponse['items'] as $channel) {
		      $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

		      $playlistItemsResponse = $this->youtube->playlistItems->listPlaylistItems('snippet', array(
		        'playlistId' => $uploadsListId,
		        'maxResults' => 50
		      ));

		      foreach ($playlistItemsResponse['items'] as $playlistItem) {
		      	$videos[] = array('video_id'=>$playlistItem['snippet']['resourceId']['videoId'], 'title'=>$playlistItem['snippet']['title']);
		      }
		    }
		  } catch (Google_ServiceException $e) {
		    $error = sprintf('<p>A service error occurred: <code>%s</code></p>',
		      htmlspecialchars($e->getMessage()));
		    die($error);
		  } catch (Google_Exception $e) {
		    $error = sprintf('<p>An client error occurred: <code>%s</code></p>',
		      htmlspecialchars($e->getMessage()));
		    die($error);
		  }

		  return $videos;
	}


    
}
?>