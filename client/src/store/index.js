// store/index.js

import { auth } from '../authentication';

const store = Object.create(null);
export default store;

/**
 * Fetch the given list of items.
 *
 * @param {Array<Number>} ids
 * @return {Promise}
 */

store.getAuthHeaders = () => {
	return new Headers({
		'x-auth-token': auth.authTokenString(),
	});
};

store.loadConversation = () => {
	return new Promise((resolve, reject) => {
		let options = {
			method: 'GET',
			mode: 'cors',
			headers: store.getAuthHeaders(),
		};

		fetch('http://api.chat-js.local:8888/conversations', options)
			.then(response => response.json())
			.then(data => resolve(data))
			.catch(err => reject(err));
	});
};

store.loadAll = (convId) => {
	return new Promise((resolve, reject) => {
		let options = {
			method: 'GET',
			mode: 'cors',
			headers: store.getAuthHeaders(),
		};

		fetch(`http://api.chat-js.local:8888/messages?convId=${convId}`, options)
			.then(response => response.json())
			.then(data => resolve(data))
			.catch(err => reject(err));
	});
};

store.send = (message, uid, success, error) => {
	return new Promise((resolve, reject) => {
		let options = {
			method: 'POST',
			mode: 'cors',
			headers: store.getAuthHeaders(),
			body: `message=${encodeURI(message)}`,
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
};