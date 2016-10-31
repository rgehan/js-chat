<template lang="jade">
.message-panel
  .chat-headers
    span.chat-title Chat
    a(@click='toggleParams')
      i.fa.fa-gear.icon-gear-params
    //a(@click='update') Refresh

  div.chat-parameters(v-if='paramsVisible')
    input(type='text' v-model='pseudo' placeholder='Pseudo')
    input(type='text' v-model='uid')

  .chat-container
    .chat-body(id="chat-body")
      message(v-for='msg in messages' v-bind:msg='msg' v-bind:class="{'own-message': msg.uid == uid}")
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
      messages: [],
      messageInput: '',
      paramsVisible: false,
      pseudo: "Guest",
      uid: 1,
    }
  },
  created(){
    this.update()
  },
  components:{
    'message': Message,
  },
  methods: {
    update(){
      MessageStore.loadAll().then(messages => {
        this.messages = messages;

        var elem = document.getElementById("chat-body");
        elem.scrollTop = elem.scrollHeight;
      });
    },
    sendMessage: function() {
      MessageStore.send(this.messageInput, this.uid)
      .then(response => {
        this.update();
      })
      .catch(error => {
        console.error(error);
      });

      this.messageInput = '';
    },
    toggleParams: function() {
      this.paramsVisible = !this.paramsVisible;
    }
  }
};

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
    border-bottom 1px solid #D1D1D1
  
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
    padding: 8px 6px
    
    border none
    border-top 1px solid #D1D1D1
    
    font-family "Roboto", sans-serif
    font-weight 100
    font-size 1em

    transition all ease-in-out .1s
  
    &:focus
      outline none
  
  .chat-container
    padding 0px 7px
    
    .chat-body
      overflow scroll
      overflow-x hidden
      height 600px
      
</style>
