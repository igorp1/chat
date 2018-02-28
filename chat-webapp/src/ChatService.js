let ENV = 'dev';
let APIs = {
    dev : "http://localhost/~idp/chat-backend/api",
    prod: ''
}; 
const API_base = APIs[ENV];

export default class ChatService{

    static initUser(http, func, saveToLocalStorage=true){

        if(localStorage.getItem('CHAT_USER_TOKEN') === null){
			this.newUser(http).then(
                (response)=>{

                    let config = response.body["config"];
                    let token = response.body["token"];

                    if(saveToLocalStorage){
                        this.saveUserTokenLocally(token);
                        this.saveUserConfigLocally( config );
                    }
                    func(token, config);
                },
                ()=>{console.log('Something went wrong');}
            )
        }
        else{
            let userToken = localStorage.getItem('CHAT_USER_TOKEN');
            if(localStorage.getItem('CHAT_USER_CONFIG') === null){
                ChatService.fetchUser(http, userToken).then(
                    (response)=>{
                        if(saveToLocalStorage){
                            this.saveUserConfigLocally(response.body);
                        }
                        func(userToken, response.body);
                    },
                    ()=>{console.log('Something went wrong');}
                )
            }
            else{
                func(userToken, JSON.parse(localStorage.getItem('CHAT_USER_CONFIG')) );
            }
        }

    }

    static saveUserTokenLocally(token){
        localStorage.setItem('CHAT_USER_TOKEN', token);
    }

    static saveUserConfigLocally(config){
        localStorage.setItem('CHAT_USER_CONFIG', JSON.stringify(config));
    }

    static newUser(http){
        return http.get( `${API_base}/user/new` ); 
    }

    static fetchUser(http, userToken){
        return http.get( `${API_base}/user/${userToken}/fetch` ); 
    }

    static updateUserConfig(http, userToken, userConfig, func){
        http.post( `${API_base}/user/${userToken}/update`, userConfig ).then(
            (response)=>{
                this.saveUserConfigLocally(userConfig);
                func();
            },
            ()=>{console.log('Something went wrong');}
        ); 
    }

    static newChat(http){
        return http.get( `${API_base}/chat/new` ); 
    }

    static loadChat(http, chatToken){
        return http.get( `${API_base}/chat/${chatToken}/load` ); 
    }

}