export default {
	API_USER: 'api',
	API_PASS: 'password',
	API_BASIC_AUTH_STR: function() {
		return btoa(this.API_USER + ':' + this.API_PASS); //Base64 Encode
	}
}