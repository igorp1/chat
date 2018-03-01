<template>
    <div id="main-container">
		<div class="chat-title shadow-med" v-on:click="openSettings">
			{{chatInfo.name}}
		</div>
		<div style="text-align:center;" v-if="this.loadPreviousbuttonText !== null"><button v-on:click="loadOlder" style="margin-top: 0;">{{loadPreviousbuttonText}}</button></div>
		<div style="margin-top:7.5em" v-else></div>
		<div class="messages-container">
			<!-- CHAT HISTORY! -->
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
			<div v-for="(line, index) in messagesSending" :key="`sending_${index}`">
				<div v-if="isLineFromUser(line)" class="pure-g">
					<div class="pure-u-1-5"></div>
					<div class="pure-u-4-5">
						<div class="message shadow-low" :style="{ borderRightColor:line.from.color }">{{ line.text }}</div>
						<span class="message-detail pull-right" style="color:grey" >Sending...</span>
					</div>
				</div>
			</div>
		</div>

		<div class="write-message">
			<div class="pure-g">
				<div class="pure-u-md-1-5 pure-u-lg-1-5"></div>
				<div class="pure-u-1 pure-u-md-3-5 pure-u-lg-3-5">
					<div class="textarea-container">
						<input rows="1" placeholder="message" v-on:keyup.enter="keyCheck" v-model="message" />
					</div>
				</div>
				<div class="pure-u-md-1-5 pure-u-lg-1-5"></div>
			</div>
		</div>
    </div>
</template>

<script>
import ChatService from '../ChatService';

export default {
	name: 'User',
	data () {
		return {
			userConfig : {},
			userToken : "",
			chatToken : "",
			chatHistory : [],
			messagesSending : [],
			chatInfo : {},
			message: "",
			historyOffset:0,
			loadPreviousbuttonText:"load previous",
		}
	},
	created(){
		ChatService.initUser(this.$http, (token, config)=>{
			this.userConfig = config;
			this.userToken = token;
		});
		this.chatToken = this.$route.params.token;
		this.loadChat(this.chatToken);
		this.message = ChatService.getDraft(this.chatToken);
		
	},
	methods : {

		loadChat(token){

			ChatService.loadChat(this.$http, token, 
				(chatObj)=>{
					this.chatInfo = {
						name : chatObj['name'],
						protected : chatObj['protected']
					};
					this.chatHistory = chatObj['messages'] || [];
					this.setupAutoFetch();
				}
			);

			this.scrollToBottom();
			

		},

		isLineFromUser(line){
			return line.from.token === this.userToken;
		},

		scrollToBottom(){
			setTimeout(function(){window.scroll(0, document.documentElement.offsetHeight)}, 50);
		},

		keyCheck(event){
			event.preventDefault();
			if(event.key === "Enter"){
				if(this.message.length == 0){return;}
				this.prepareToSendMessage();
			}
			this.autoSaveDraftLocally(this.message);
		},

		addNewToChatHistory(msgObjectArr){

			msgObjectArr.map( (msgObject) => {
				this.messagesSending.removeIf( (el)=> el.text == msgObject.text  );
				this.chatHistory.push(msgObject);
			});

			this.scrollToBottom();
		},

		addNewToSending(msgObj){
			this.messagesSending.push(msgObj);
		},

		addOldToChatHistory(msgObject){
			this.chatHistory.push(msgObject);
		},

		prepareToSendMessage(){

			this.sendMessage(this.message);
			let msgObject = this.makeNewMessageObject(this.message);
			this.addNewToSending(msgObject);

			this.message = "";
		},

		sendMessage(msg){
			if(msg == ""){return;}
			ChatService.sendMessage(this.$http, this.chatToken, msg, this.userToken, (newID)=>{
				// not much to see here.
				console.log("SENT newID");
			});
		},

		makeNewMessageObject(msg){
			let userObj = this.userConfig;
			userObj["token"] = this.userToken;
			return { text:msg, from:userObj };
		},

		openSettings(){
			this.$router.push({ name: 'ChatSettings', params: { token: this.chatToken }})
		},

		autoSaveDraftLocally(message){
			let drafts = JSON.parse(localStorage.getItem("CHAT_DRAFTS")) || {};
			drafts[this.chatToken] = message;
			localStorage.setItem("CHAT_DRAFTS", JSON.stringify(drafts));
		},

		loadOlder(){
			this.historyOffset++;
			this.loadPreviousbuttonText="loading..."
			ChatService.loadOlder(this.$http, this.chatToken, this.historyOffset, (hist)=>{
				if( hist.length === 0 ){
					this.loadPreviousbuttonText=null;
					return;
				}
				this.chatHistory = hist.concat(this.chatHistory);
				this.loadPreviousbuttonText = "DONE!";
				setTimeout(()=>{this.loadPreviousbuttonText="load previous"}, 1500);
			});
		},

		setupAutoFetch(){
			ChatService.beginUpdatePoll(this.$http, this.chatToken, 
				()=>{
					return this.chatHistory[this.chatHistory.length-1]["id"] || -1;
				},
				(newMessages)=>{ 
					this.addNewToChatHistory(newMessages);
				}
			);
		}
	}
}
</script>

<style scoped>
#main-container{
	margin-top: 4em;
}

.messages-container{
	margin-bottom: 3em;
}

.message{
    padding: .5em;
    margin-top: .5em;
    border-radius: .3em;
    border-left: solid 4px white;
    border-right: solid 4px white;
	font-family: 'Titillium Web', sans-serif; 
	max-width: 100%
}

.pull-right{
	float: right;
}

span.message-detail{
	font-family: 'Titillium Web', sans-serif; 
	font-size: .8em;
}

.write-message {
	position: fixed;
	bottom: 1em;
	left:0;
	width: 100%;
}

.write-message input{
	position: relative;
	margin-left: .5em;
	margin-right: .5em;
	padding: .5em 1em;
    border-radius: .4em;
	width: calc(100% - 1em);
	border: 1px solid rgb(223, 223, 223);
}

@media (max-width: 767px) { 
	.write-message div.textarea-container{
		width: 95vw;
	}
}

@media (max-width: 670px) { 
	.write-message div.textarea-container{
		width: 94vw;
	}
}

@media (max-width: 540px) { 
	.write-message div.textarea-container{
		width: 93vw;
	}
}

@media (max-width: 455px) { 
	.write-message div.textarea-container{
		width: 91vw;
	}
}

@media (max-width: 381px) { 
	.write-message div.textarea-container{
		width: 89vw;
	}
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
