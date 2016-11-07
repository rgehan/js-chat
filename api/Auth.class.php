<?php

class Auth
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Fonction checkant la validité d'un mot de passe utilisateur
	 */
	public function checkUserSuppliedCredentials($user, $pass) 
	{
		//On recupere le salt correspondant à l'utilisateur
		$saltRequest = $this->pdo->prepare("SELECT salt FROM users WHERE pseudo = :pseudo");
		$saltRequest->execute(['pseudo' => $user]);
		$data = $saltRequest->fetch(PDO::FETCH_ASSOC);

		//Si on a un salt (et donc un user)
		if($data)
		{
			//On hash le password donné avec ce salt
			$salt = $data['salt'];
			$hash = hash('sha256', $salt . $pass);

			//Et on le compare a celui existant en db
			$passRequest = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE pseudo = :pseudo AND password = :hash");
			$passRequest->execute(['pseudo' => $user, 'hash' => $hash]);
		
			$count = $passRequest->fetch()[0];

			//Si on a pas d'utilisateur qui match, on ne poursuit pas...
			if($count == 1)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Sauvegarde un utilisateur
	 * @param  string $user nom d'utilisateur
	 * @param  string $pass mot de passe
	 * @return boolean
	 */
	public function saveUser($user, $pass)
	{
		$salt = uniqid(mt_rand(), true);
		$hash = hash('sha256', $salt . $pw);

		$query = $this->pdo->prepare("INSERT INTO users (uid, pseudo, password, salt) VALUES (NULL, :pseudo, :pass, :salt);");
		$ret = $query->execute(['pseudo' => $user, 'pass' => $hash, 'salt' => $salt]);
	
		return $ret;
	}

	/**
	 * Fonction générant un token de session pour l'utilisateur
	 */
	public function generateUserToken($user)
	{
		$uid = $this->getUIDFromPseudo($user);

		if($uid)
		{
			$token = $this->uniqidReal(32);

			//Supprime les tokens de l'utilisateur
			$query = $this->pdo->prepare("DELETE FROM sessions WHERE uid = :uid");
			$query->execute(['uid' => $uid]);

			//Cree un token pour l'utilisateur
			$query = $this->pdo->prepare("INSERT INTO sessions (token, uid, expiration) VALUES (:token, :uid, DATE_ADD(NOW(), INTERVAL 2 HOUR))");
			$ret = $query->execute(['token' => $token, 'uid' => $uid]);

			if($ret)
				return $token;
		}

		return false;
	}

	/**
	 * Verifie si un token est valide
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	public function checkToken($token)
	{
		//Supprime les tokens de l'utilisateur
		$query = $this->pdo->prepare("SELECT uid FROM sessions WHERE token = :token AND expiration > NOW()");
		$query->execute(['token' => $token]);

		if($query->rowCount() == 1)
			return $query->fetch(PDO::FETCH_ASSOC);
		else
			return false;
	}

	private function getUIDFromPseudo($pseudo)
	{
		$query = $this->pdo->prepare("SELECT uid FROM users WHERE pseudo = :pseudo");
		$query->execute(['pseudo' => $pseudo]);

		if($query->rowCount())
			return $query->fetch(PDO::FETCH_ASSOC)['uid'];
		else
			return false;
	}


	/**
	 * Generates really random IDs
	 * @param  integer $length [description]
	 * @return [type]          [description]
	 */
	private function uniqidReal($length = 13) 
	{
	    if (function_exists("random_bytes")) 
	    {
	        $bytes = random_bytes(ceil($length / 2));
	    } 
	    elseif (function_exists("openssl_random_pseudo_bytes")) 
	    {
	        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
	    } 
	    else 
	    {
	        throw new Exception("no cryptographically secure random function available");
	    }

	    return substr(bin2hex($bytes), 0, $length);
	}
};

