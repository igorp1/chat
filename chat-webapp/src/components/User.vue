<template>
		<div id="main-container">
				<h2>😃 USER SETTINGS</h2>
				<div class="control-container">
					Name: <input placeholder="name" v-model="userConfig.name"><br/>
					<span>This is the name that will be visible to you and people you chat with.</span>
				</div>
				<div class="control-container">
					Color: <input placeholder="name" v-model="userConfig.color"><br/>
					<span :style="{color:iscolorValid(userConfig.color) ? userConfig.color : 'rgb(99, 99, 99)' }" >
						{{ iscolorValid(userConfig.color) ? 'COLOR PREVIEW' : '⚠️ Invalid Color' }}
					</span><br/>
					<span>This is the color you and other people will see for your messages.</span>
					
				</div>
				<div class="center"><button v-on:click="saveUserConfig">{{saveButtonText}}</button></div>
				
		</div>
</template>

<script>
import ChatService from '../ChatService';
export default {
	name: 'User',
	data () {
		return {
			userToken :"",
			userConfig: {},
			saveButtonText : "SAVE"
		}
	},
	created(){
		ChatService.initUser(this.$http, (token, config)=>{
			this.userToken = token;
			this.userConfig = config;
		});
	},

	methods:{
		saveUserConfig(){
			this.saveButtonText = "saving...";
			ChatService.updateUserConfig(this.$http, this.userToken, this.userConfig ,()=>{
				this.saveButtonText = "SAVED!";
				setTimeout(()=>{this.saveButtonText="SAVE"}, 1000);
			});
		},

		iscolorValid(color){
			return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
		}
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
