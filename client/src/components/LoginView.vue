<template lang="jade">
    .ui.centered.grid
        .ui.eight.wide.column
            h2.ui.header Login view
            .ui.form
                .field
                    label Username:
                    input(v-model="user" placeholder="Username")
                .field
                    label Password:
                    input(v-model="pass" placeholder="Password")
                button.ui.button(@click="doLogin") Login
</template>

<style lang="stylus">
</style>

<script>
    import {auth} from '../authentication';

    var app = {
        name: 'LoginView',
        data: function(){
            return {
                user: 'user',
                pass: 'password'
            }
        },
        components:{
        },
        methods: {
            doLogin(){
                auth.login(this.user, this.pass)
                    .then((response) => {
                        if(response)
                            this.$router.push('/');
                    })
                    .catch((err) => {
                        console.error(err);
                    });
                
            },
        }
    };

    export default app;
</script>