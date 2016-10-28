/*
 * Interface avec l'API
 */
var MessageAPI = {
	loadAll: function(success, error) {
		var options = {
			method: 'GET',
			mode: 'cors',
		};

		fetch('http://api.chat-js.local:8888/messages', options)
			.then(function(response) {
				response.json().then(function(messages) {
					success(messages);
				});
			})
			.catch(function(err) {
				error(err);
			});
	},
	send: function(message, pseudo, success, error) {
		var options = {
			method: 'POST',
			mode: 'cors',
			headers: new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' }),
			body: 'message=' + encodeURI(message) + '&uid=' + encodeURI(pseudo),
		};

		fetch('http://api.chat-js.local:8888/messages', options)
			.then(function(response) {
				return success(response);
			})
			.catch(function(err) {
				return error(err);
			});
	},
};

/*
 * Creation de l'application
 */
var app = new Vue({
	el: '#app',
	data: {
		messages: [],
		messageInput: '',
		paramsVisible: false,
		pseudo: "Guest",
	},
	watch: {

	},
	computed: {

	},
	methods: {
		sendMessage: function() {
			MessageAPI.send(this.messageInput, this.pseudo, function(response) {
				refreshApp();
			}, function(error) {
				console.error(error);
			});

			this.messageInput = '';
		},
		toggleParams: function() {
			this.paramsVisible = !this.paramsVisible;
		},
		messageClass: function(message) {
			return message.uid == this.pseudo ? 'yourMessages' : 'theirMessages';
		}
	}
});

function refreshApp() {
	MessageAPI.loadAll(function(data) {
		app.messages = data;
	}, function(err) {
		console.error(err);
	});
}

//Précharge les données
refreshApp();