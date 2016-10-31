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
 * On créé des composants
 */

const Login = {
	template: ' \
	<div> \
		<input type="text" id="login"> \
		<input type="password" id="password"> \
	</div>'
}

const Messaging = {
	template: ' \
	<div class="message-panel"> \
			<div class="chat-headers"> \
				<span class="chat-title">Chat</span> <a @click="toggleParams"><i class="fa fa-gear icon-gear-params"></i></a> \
			</div> \
			<div v-if="paramsVisible" class="chat-parameters"> \
				<input type="text" v-model="pseudo" placeholder="Pseudo"> \
				<input type="text" v-model="uid"> \
			</div> \
			<div class="chat-container"> \
				<div class="chat-body"> \
					<div v-for="msg in messages" v-bind:class="messageClass(msg)"> \
						<span class="message">{{msg.message}}</span><br/> \
						<span class="author">{{msg.uid}}</span> \
					</div> \
				</div> \
				<div class="chat-controls"> \
					<input type="text" placeholder="Type a message..." @keyup.enter="sendMessage" v-model="messageInput"> \
				</div> \
			</div> \
		</div>'
}

/*
 * On crée le router
 */

const routes = [
	{ path: '/login', component: Login  },
	{ path: '/', component: Messaging },
]

const router = new VueRouter({
	routes: routes,
});

/*
 * Creation de l'application
 */

const app = new Vue({
	router: router,
}).$mount('#app');

/*var app = new Vue({
	el: '#app',

	data: {
		messages: [],
		messageInput: '',
		paramsVisible: false,
		pseudo: "Guest",
		uid: 1,
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
});*/

function refreshApp() {
	MessageAPI.loadAll(function(data) {
		app.messages = data;
	}, function(err) {
		console.error(err);
	});
}

//Précharge les données
refreshApp();