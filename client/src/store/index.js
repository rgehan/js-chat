// store/index.js

import Configuration from './conf.js'

const store = Object.create(null)
export default store;

/**
 * Fetch the given list of items.
 *
 * @param {Array<Number>} ids
 * @return {Promise}
 */

var authenticatedHeaders = new Headers({
	'Authorization': 'Basic ' + Configuration.API_BASIC_AUTH_STR(),
});

store.loadAll = () => {
	return new Promise((resolve, reject) => {
		var options = {
			method: 'GET',
			mode: 'cors',
			headers: authenticatedHeaders,
		};

		fetch('http://api.chat-js.local:8888/messages', options)
			.then(function(response) {
				response.json().then(function(messages) {
					resolve(messages);
				});
			})
			.catch(function(err) {
				reject(err);
			});
	});
}

store.send = (message, uid, success, error) => {
	return new Promise((resolve, reject) => {
		var options = {
			method: 'POST',
			mode: 'cors',
			headers: authenticatedHeaders,
			body: 'message=' + encodeURI(message),
		};

		options.headers.append('Content-Type', 'application/x-www-form-urlencoded');

		fetch('http://api.chat-js.local:8888/messages', options)
			.then(function(response) {
				resolve(response);
			})
			.catch(function(err) {
				reject(err);
			});
	});
}