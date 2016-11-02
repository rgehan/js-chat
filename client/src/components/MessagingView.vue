<template lang="jade">
.message-panel
  .chat-headers
    span.chat-title Chat

  .chat-container
    .chat-body(id="chat-body")
      message(v-for='msg in messages' v-bind:msg='msg')
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
      MessageStore.loadAll()
      .then(messages => {
        this.messages = messages;
      })
      .then(() => {
        this.scrollDown();
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
    scrollDown: () => {
      console.log("Scrolling down");
      var elem = document.getElementById("chat-body");
      elem.scrollTop = elem.scrollHeight;
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
  
  .chat-headers, .chat-container
    width 100%
    padding 5px 0px
    box-sizing border-box
  
  .chat-headers
    text-align center
    border-bottom 1px solid #D1D1D1
  
  .chat-title
    font-size 2em
  
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
