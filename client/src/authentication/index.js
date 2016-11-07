class Auth {
	constructor() {
		this.isAuthenticated = false;
		this.user = '';
		this.uid = '';
	}

	authString() {
		return btoa(this.user + ':' + this.pass); //Base64 Encode
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
								that.user = user;
								that.uid = data.uid;
								that.isAuthenticated = true;

								resolve(true);
							}

							resolve(false);
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