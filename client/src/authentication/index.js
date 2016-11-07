class Auth {
	constructor() {
		this.isAuthenticated = false;
		this.user = '';
		this.pass = '';
	}

	authString() {
		return btoa(this.user + ':' + this.pass); //Base64 Encode
	}

	login(user, pass) {
		this.user = user;
		this.pass = pass;
		this.isAuthenticated = true;
	}

	logout() {
		this.user = '';
		this.pass = '';
		this.isAuthenticated = false;
	}
}

export let auth = new Auth();