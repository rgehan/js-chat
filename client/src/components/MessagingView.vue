<template lang="jade">
.ui.grid.message-panel
    .ui.four.wide.column
        .my-sidebar
            .my-sidebar-headers
                span.sidebar-title Conversations
            .my-sidebar-container
                conversation(v-for='conv in conversations'
                             v-bind:conv='conv'
                             v-bind:class="{'current-conv': isThisConvCurrent(conv)}"
                             v-on:selectConvRequested="selectConvRequested")
    .ui.twelve.wide.column
        .chat-content
            .chat-headers(@click='update')
                span.chat-title Chat

            .chat-container
                .chat-body(id="chat-body")
                    message(v-for='msg in messages' v-bind:msg='msg' v-bind:class="{'own-message': isThisMessageMine(msg)}")
                .chat-controls
                    input(type='text' placeholder='Type a message...' @keyup.enter='sendMessage' v-model='messageInput')
</template>



<script>      
    import MessageStore from '../store';
    import Message from './partials/Message.vue';
    import Conversation from './partials/Conversation.vue';
    import {auth} from '../authentication';

    var app = {
        name: 'MessageView',
        data: function(){
            return {
                messages: [],
                conversations: [],
                convId: 0,
                messageInput: '',
            }
        },
        created(){
            this.update()
        },
        components:{
            'message': Message,
            'conversation': Conversation,
        },
        beforeRouteEnter(to, from, next){
            if(!auth.isAuthenticated)
                next('/login');
            
            next();
        },
        methods: {
            update(){
                MessageStore.loadAll(this.convId)
                .then(messages => {
                    this.messages = messages;
                })
                .then(() => {
                    this.scrollDown();
                });

                MessageStore.loadConversation()
                .then(conversations => {
                    this.conversations = conversations;
                });
            },
            sendMessage: function() {
                MessageStore.send(this.messageInput, this.uid, this.convId)
                .then(response => {
                    this.update();
                })
                .catch(error => {
                    console.error(error);
                });

                this.messageInput = '';
            },
            selectConvRequested: function(id) {
                this.convId = id;
                this.update();
            },
            scrollDown: () => {
                var elem = document.getElementById("chat-body");
                elem.scrollTop = elem.scrollHeight;
            },
            isThisMessageMine: msg => {
                return msg.pseudo === auth.user;
            },
            isThisConvCurrent: conv => {
                return false; //TODO
            }
        }
    };

    export default app;
</script>


<style lang="stylus">
    
    //*
    //Resets list style
    li
        list-style-type none
        
    //Stylize input
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


    .chat-headers, .chat-container, .my-sidebar-headers
            width 100%
            padding 8px 0px
            box-sizing border-box


    .message-panel
        border #D1D1D1
        color #463f43
        font-family "Roboto", sans-serif
        font-weight 100
        
        .sidebar
            border-right solid 1px #D1D1D1
            position fixed
            height 100%
            overflow hidden
    
        .chat-headers
            text-align center
            border-bottom 1px solid #D1D1D1
            overflow hidden
                        
            .chat-title
                font-size 1.7em
        
        .chat-container
            padding 0px 7px
        
            .chat-body
                height 700px
                overflow scroll
                overflow-x hidden
                

    .my-sidebar-headers
        text-align center
        border-bottom 1px solid #D1D1D1
        
        .sidebar-title
            font-size 1.7em
    
</style>
