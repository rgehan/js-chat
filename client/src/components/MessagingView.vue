<template lang="jade">
.message-panel
  .chat-headers
    span.chat-title Chat
    a(@click='toggleParams')
      i.fa.fa-gear.icon-gear-params

  div.chat-parameters(v-if='paramsVisible')
    input(type='text' v-model='pseudo' placeholder='Pseudo')
    input(type='text' v-model='uid')

  .chat-container
    .chat-body
      message(v-for='msg in messages' v-bind:msg='msg' v-bind:class="{'own-message': msg.uid == 1}")
    .chat-controls
      input(type='text' placeholder='Type a message...' @keyup.enter='sendMessage' v-model='messageInput')
</template>



<script>      
import MessageStore from '../store';
import Message from './partials/Message.vue';

var app = {
  name: 'MessageView',
  data: function(){
    return {
      messages: [{message: 'Test', uid:1}, {message: 'Tamere', uid:2}],
      messageInput: '',
      paramsVisible: false,
      pseudo: "Guest",
      uid: 1,
    }
  },
  components:{
    'message': Message,
  },
  methods: {
    sendMessage: function() {
      MessageStore.send(this.messageInput, this.pseudo, function(response) {
        refreshApp();
      }, function(error) {
        console.error(error);
      });

      this.messageInput = '';
    },
    toggleParams: function() {
      this.paramsVisible = !this.paramsVisible;
    }
  }
};

function refreshApp() {
  MessageStore.loadAll(function(data) {
    app.messages = data;
    console.log("Messages ok");
    console.log(data);
  }, function(err) {
    console.error(err);
  });
}

//Précharge les données
refreshApp();

export default app;

</script>




<style lang="stylus">

  body
    background #F1F4F9

  #app
    width 60%
    min-width 500px
    margin auto
    border #D1D1D1
    background white

    color #463f43

    font-family "Roboto", sans-serif
    font-weight 100
  
  li
    list-style-type none
  
  .chat-headers, .chat-parameters, .chat-container
    width 100%
    box-sizing border-box
  
  .chat-headers
    text-align center
    padding 5px 0px
  
  .chat-parameters
    padding 5px 0px
  
  .chat-title
    font-size 2em
  
  .icon-gear-params
    font-size .5em
    display inline-block
    margin-left 10px
  
  input[type=text]
    display block
    width 95%
    margin auto
    padding 4px 9px
    
    border none
    border-bottom 3px solid #EAEAEA

    font-family "Roboto", sans-serif
    font-weight 100
    font-size 1.3em

    transition all ease-in-out .1s
  
  input[type=text]:focus
    outline none
    border-bottom-color #F39BBA
  
  .chat-container
    padding 0px 7px
      
</style>
