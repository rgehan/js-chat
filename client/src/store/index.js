// store/index.js

export default {

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