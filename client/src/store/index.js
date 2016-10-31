// store/index.js

const store = Object.create(null)
export default store;

/**
 * Fetch the given list of items.
 *
 * @param {Array<Number>} ids
 * @return {Promise}
 */

store.loadAll = () => {
	return new Promise((resolve, reject) => {
		var options = {
			method: 'GET',
			mode: 'cors',
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
			headers: new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' }),
			body: 'message=' + encodeURI(message) + '&uid=' + encodeURI(uid),
		};

		fetch('http://api.chat-js.local:8888/messages', options)
			.then(function(response) {
				resolve(response);
			})
			.catch(function(err) {
				reject(err);
			});
	});
}