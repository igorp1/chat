<template>
    <div id="main-container">
		<div class="chat-title shadow-med" v-on:click="openSettings">
			🍑 BUTT talks
		</div>
		<div style="text-align:center"><button>load previous...</button></div>
		<div class="messages-container">
			<div v-for="(line, index) in chatHistory" :key="index">
				<div v-if="isLineFromUser(line)" class="pure-g">
					<div class="pure-u-1-5"></div>
					<div class="pure-u-4-5">
						<div class="message shadow-low" :style="{ borderRightColor:line.from.color }">{{ line.text }}</div>
						<span class="message-detail pull-right" :style="{ color:line.from.color }" >{{line.from.name}}</span>
					</div>
				</div>
			
				<div v-else class="pure-g">
					<div class="pure-u-4-5">
						<div class="message shadow-low" :style="{ borderLeftColor:line.from.color }">{{ line.text }}</div>
						<span class="message-detail" :style="{ color:line.from.color }">{{line.from.name}}</span>
					</div>
					<div class="pure-u-1-5"></div>
				</div>
			</div>
		</div>
		<div>
			<textarea rows="1" class="write-message" placeholder="message" v-on:keyup="keyCheck" v-model="message"></textarea>
		</div>
    </div>
</template>

<script>
import ChatService from '../ChatService';

export default {
	name: 'User',
	data () {
		return {
			userInfo : {},
			chatToken : "",
			chatHistory : [],
			chatInfo : {},
			message: ""
		}
	},
	created(){
		this.chatToken = this.$route.params.token;
		this.loadChat(this.chatToken);
	},
	methods : {

		loadChat(token){

			this.userInfo = {name: "🛰 idp", token: "abcxyz", color: "#4B9BFF" };
			this.chatInfo = {name:'idp chat'};
			this.chatHistory = [];
			for(let i=0; i < 100; i++){
				this.chatHistory.push({
					text:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla a est vitae odio ultrices dictum. Donec ut vehicula neque, vitae volutpat ex. Etiam porttitor sollicitudin imperdiet. ", 
					from:{name: i%2 ? "🛰 idp" : "test bot", token: i%2 ? "abcxyz" : "123456", color: i%2 ? "#4B9BFF" : "#FF5C51"}
				});
			}

			// ChatService.loadChat(this.$http, token)
			// .then(
			// 	(response) => {
			// 		console.log(response.body);
			// 	},
			// 	(response)=>{console.log('something went wrong');}
			// );

			this.scrollToBottom();
			

		},

		isLineFromUser(line){
			return line.from.token === "abcxyz";
		},

		scrollToBottom(){
			setTimeout(function(){window.scroll(0, document.documentElement.offsetHeight)}, 50);
		},

		keyCheck(event){
			this.autoSaveDraftLocally(this.message);
			if(event.key === "Enter"){
				let msgObject = this.makeNewMessageObject(this.message);
				this.sendMessage(msgObject);
				this.addNewToChatHistory(msgObject);
				this.message = "";
			}
		},

		addNewToChatHistory(msgObject){
			this.chatHistory.push(msgObject);
			this.scrollToBottom();
		},

		addOldToChatHistory(msgObject){
			this.chatHistory.push(msgObject);
		},

		sendMessage(msg){
			//ChatService.sendMessage(this.http, this.message);
			console.log("[SENT] " + msg);
		},

		makeNewMessageObject(msg){
			return { text:msg, from:this.userInfo };
		},

		openSettings(){
			this.$router.push({ name: 'ChatSettings', params: { token: this.chatToken }})
		},

		autoSaveDraftLocally(message){
			let drafts = JSON.parse(localStorage.getItem("CHAT_DRAFTS")) || {};
			drafts[this.chatToken] = message;
			localStorage.setItem("CHAT_DRAFTS", JSON.stringify(drafts));
		}
	}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
#main-container{
	margin-top: 4em;
}

.messages-container{
	margin-bottom: 1em;
}

.message{
    padding: .5em;
    margin-top: .5em;
    border-radius: .4em;
	border: solid 4px white;
	font-family: 'Titillium Web', sans-serif; 
}

.pull-right{
	float: right;
}

.message-detail{
	font-family: 'Titillium Web', sans-serif; 
}

.write-message{
	border: 1px solid rgb(223, 223, 223);
    padding: .5em 1em;
    border-radius: .4em;
	width: calc(100% - 2em - 2px);resize: vertical;
}

.chat-title{
	cursor: pointer;
	top: 3.8em;
    left: .5em;
	font-size: 1.2em;
	position: fixed;
    border: white;
	padding: .5em 1em;
    background-color: whitesmoke;
}



</style>
