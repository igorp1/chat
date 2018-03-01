<template>
		<div id="main-container">
				<h2 >⚙️ CHAT SETTINGS</h2>
				<div class="control-container">
					Name: <input placeholder="name" v-model="chatSettings.name" :disabled="inputDisabled"><br/>
					<span>This is the name that will be visible to you and people in the chat.</span>
				</div>
				<div class="control-container">
					Link:  <a class="transition chat-link" v-on:click="hola">{{chatLink}}</a><br>
					<span style="color:#AA73DB" v-if="chatLinkClipWarn">{{chatLinkClipWarn}}<br /></span>
					<span>Share the link above to start chatting with a friend.</span>
				</div>
				<div class="center"><button v-on:click="saveChatSettings">{{updateButtonLabel}}</button></div>


		</div>
</template>

<script>
import ChatService from '../ChatService';

export default {

	name: 'ChatSettings',
	data () {
		return {
			chatSettings : {name:"loading..."},
			inputDisabled : true,
			chatLink : "",
			chatLinkClipWarn : "",
			updateButtonLabel : "SAVE"
		}
	},
	created(){
		this.chatLink = this.getChatLink();

		ChatService.loadChatInfo(this.$http, this.$route.params.token, (chatSettings)=>{
			this.chatSettings = chatSettings;
			this.inputDisabled = false;
		});

	},
	methods : {
		saveChatSettings(){
			this.updateButtonLabel = "saving...";
			let payload = this.chatSettings;
			ChatService.chatInfoUpdate(this.$http, this.$route.params.token, payload , ()=>{
				this.updateButtonLabel = "SAVED";
				setTimeout(()=>{this.updateButtonLabel = "SAVE";}, 1500);
			});

		},

		getChatLink(){
			let link = location.href.split('/');
			link.pop();
			return link.join('/');
		},

		hola(){
			ChatService.copyTextToClipboard(this.chatLink);
			this.chatLinkClipWarn = "COPIED TO CLIBOARD!";
			setTimeout(()=>{this.chatLinkClipWarn = "";}, 1500);
		}

		



	}
}
</script>

<style scoped>

#main-container{
	margin-top: 6em;
}

.control-container{
	margin: 1.5em 0.5em;
}

input{
	margin-bottom: .5em;
}

span{
	color:rgb(99, 99, 99);
	font-size:.8em;
}

.chat-link{
	margin-left: .5em;
	cursor: pointer;
	font-family: monospace !important;
	font-size: 1.3em;
}

.chat-link:hover{
	color: rgb(180, 136, 219)
}

.chat-link:active{
	color: #AA73DB;
}

</style>
