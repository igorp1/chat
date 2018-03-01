<template>
    <div id="main-container">

		<div class="about">
			<h3>👋🏾 Welcome</h3>
			<p>This is a simple yet robust public chat of the free.</p>

			<h3>📑 How it works</h3>
			<ol>
				<li>All you need to do to start chatting is click "Start chat".</li>
				<li>Once you are in a chat page click on the chat name on the top left to edit it.</li>
				<li>You can go directly to a chat page and the history will be retrieved.</li>
				<li>If you go directly to a chat page to claim it if it doesn't exist.</li>
					<ul>
						<li>Like this: /chat/<b>&lt;whatever-you-wanna-name-it&gt;</b></li>
					</ul>
				<li>No need to login but you can edit your "User Settings" if you want.</li>
			</ol>
		</div>

		<div class="actions">
			<button v-on:click="startNewChat">Start chat</button>

			<router-link to="/user">
				<button>User Settings</button>
			</router-link>
		</div>

		<div class="info">
			<a target="_blank" href="https://igorp1.github.io">by idp_</a>
			<a target="_blank" href="https://github.com/igorp1/chat"><img height="20px" src="https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg" /></a>
			<a target="_blank" href="https://www.bunq.com/"><img height="20px" src="https://www.bunq.com/img/design/logo-2017-black.svg" /></a>
		</div>

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
		ChatService.initUser(this.$http, (token, config)=>{ /* all set */ });
	},

	methods: {

		startNewChat() {
			ChatService.newChat(this.$http, (token)=>{
				this.sendToChatRoom(token);
			});
		},

		sendToChatRoom(token) {
			this.$router.push({ name: 'Chat', params: { token: token }})
		}
		
    }
}
</script>

<style scoped>
#main-container{
    margin-top: 6em;
    text-align: center;
}
.about{
	padding: 0 2em;
	text-align: left;
}

.actions{
	margin-top:2em;
}

a{
	text-decoration: none;
	color: black;
}

img{
	margin-left: .8em;
	vertical-align: middle;
}

div.info{
	margin-top: 4em;
}

</style>
