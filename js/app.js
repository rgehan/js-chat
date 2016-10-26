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
	}
};

/*
 * Creation de l'application
 */
var app = new Vue({
	el: '#app',
	data: {
		messages: [],
	},
	watch: {

	},
	computed: {

	},
	methods: {

	}
});

//Pré-charge les messages
MessageAPI.loadAll(function(data) {
	app.messages = data;
}, function(err) {
	console.error(err);
});