class Auth {
	constructor() {
		this.isAuthenticated = false;
		this.user = '';
		this.uid = '';
		this.token = '';
	}

	authTokenString() {
		return 'Token ' + this.token;
	}

	getAuthHeaders() {
		return new Headers({
			'x-auth-token': this.authTokenString(),
		});
	}

	login(user, pass) {
		return new Promise((resolve, reject) => {
			let options = {
				method: 'POST',
				mode: 'cors',
				body: `user=${encodeURI(user)}&pw=${encodeURI(pass)}`,
				headers: new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' }),
			};

			let that = this;

			//Appelle l'API de login
			fetch('http://api.chat-js.local:8888/login', options)
				.then(function(response) {
					//On parse la reponse
					response.json()
						.then(function(data) {
							//Si le login a fonctionné
							if (data.status === 'login_ok') {
								that.token = data.token;
								that.isAuthenticated = true;

								let options2 = {
									method: 'GET',
									mode: 'cors',
									headers: that.getAuthHeaders(),
								};

								//On recupere des infos sur l'user
								fetch('http://api.chat-js.local:8888/userinfo', options2)
									.then(response => response.json())
									.then(data => {
										that.uid = data.uid;
										that.user = data.pseudo;

										resolve(true);
									})
									.catch(err => {
										console.error(err);
										reject(err);
									});
							}
						})
						.catch(function(err) {
							reject(err);
						});
				})
				.catch(function(err) {
					reject(err);
				});
		});
	}

	logout() {
		this.user = '';
		this.uid = '';
		this.isAuthenticated = false;
	}
}

export let auth = new Auth();