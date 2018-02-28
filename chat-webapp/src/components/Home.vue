<template>
    <div id="main-container">
        <button v-on:click="startNewChat">Start chat</button>

        <router-link to="/user">
            <button>User Settings</button>
        </router-link>
    </div>
</template>

<script>

import ChatService from '../ChatService';

export default {
    name: 'Home',
	data () {
		return {
			newChatID : -1
		}
	},
	
	created(){
		ChatService.initUser(this.$http, (token, config)=>{
			console.log(token);
			console.log(config);
		});
	},

	methods: {
		startNewChat() {

			// ChatService.newChat(this.$http)
			// .then(
			// 	(response) => {
			// 		console.log(response.body);
			// 		this.sendToChatRoom(token)
			// 	},
			// 	(response)=>{console.log('something went wrong');}
			// );

		},

		sendToChatRoom(token) {
			this.$router.push({ name: 'Chat', params: { token: token }})
		}
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
#main-container{
    margin-top: 6em;
    text-align: center;
}
</style>
