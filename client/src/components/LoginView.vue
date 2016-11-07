<template>
  <div>
 	<h1>Login view</h1>
 	<input v-model="user"><br/>
 	<input v-model="pass"><br/>
 	<button @click="doLogin">Login</button>
  </div>
</template>

<script>
import {auth} from '../authentication';

var app = {
  name: 'LoginView',
  data: function(){
    return {
      user: '',
      pass: ''
    }
  },
  components:{
  },
  methods: {
  	doLogin(){
  		auth.login(this.user, this.pass);
  		//console.log(auth);
  		this.$router.push('/');
  	},
  }
};

export default app;

</script>