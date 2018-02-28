<template>
		<div id="main-container">
				<h2 >⚙️ CHAT SETTINGS</h2>
				<div class="control-container">
					Name: <input placeholder="name"><br/>
					<span>This is the name that will be visible to you and people in the chat.</span>
				</div>
				<div class="control-container">
					Privacy Key: <input placeholder="name"><br/>
					<span>Set a privacy key and share it securely with the people you want to chat</span>
				</div>
				<div class="center"><button v-on:click="saveChatSettings">SAVE</button></div>

				<h2>📌 REMIND ME</h2>
				<div class="control-container">
					Email: <input placeholder="name"><br/>
					<span>Give us your email and we'll send you a chat link so you can find it later.</span>
				</div>
				<div class="center"><button v-on:click="saveChatSettings">SEND</button></div>

		</div>
</template>

<script>
export default {
	name: 'ChatSettings',
	data () {
		return {
			chatSettings :""
		}
	},
	created(){
		this.userToken = "hello";
	},
	methods : {
		saveChatSettings(){}
	}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
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

</style>
