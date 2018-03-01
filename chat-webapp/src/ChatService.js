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
                (res)=>this.errorHandler(res)
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
                    (res)=>this.errorHandler(res)
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
            (res)=>this.errorHandler(res)
        ); 
    }

    static newChat(http, func){
        http.get( `${API_base}/chat/new` ).then(
            (res)=>{
                func(res.body);
            },
            (res)=>this.errorHandler(res)
        ); 
    }

    static loadChat(http, chatToken, func){
        http.get( `${API_base}/chat/${chatToken}/load` ).then(
            (res)=>{
                func(res.body);
            },
            (res)=>this.errorHandler(res)
        );
    }

    static loadOlder(http, chatToken, offset, func){
        http.get( `${API_base}/chat/${chatToken}/history/${offset}` ).then(
            (res)=>{
                func(res.body);
            },
            (res)=>this.errorHandler(res)
        );
    }

    static getDraft(token){
        let obj = JSON.parse(localStorage.getItem('CHAT_DRAFTS')) || {};
        return obj[token] || ""
    }

    static sendMessage(http,chatToken, text, userToken, func){
        let payload = { text:text, userToken:userToken };
        http.post( `${API_base}/chat/${chatToken}/send`, payload ).then(
            (res)=>{
                func(res.body['id']);
            },
            (res)=>this.errorHandler(res)
        );
    }

    static beginUpdatePoll(http, chatToken, getLastID_func, update_func){
        this.pollForUpdates(http, chatToken, getLastID_func()).then(
            (res)=>{
                update_func(res.body);
                this.beginUpdatePoll(http, chatToken, getLastID_func, update_func);
            },
            (res)=>this.errorHandler(res)
        );    
    }

    static pollForUpdates(http, chatToken, lastID){
        return http.get( `${API_base}/chat/${chatToken}/poll/${lastID}` );
    }

    static loadChatInfo(http, chatToken, func){
        http.get( `${API_base}/chat/${chatToken}/info/` ).then( 
            (res)=>{
                func(res.body);
            },
            (res)=>this.errorHandler(res)
        );
    }

    static chatInfoUpdate(http, chatToken, payload , func){
        http.post( `${API_base}/chat/${chatToken}/info/update`, payload ).then(
            (res)=>{
                func(res.body);
            },
            (res)=>this.errorHandler(res)
        );    
    }

    static errorHandler(response){
        console.error("🚨😱 Catastrophic failure.");
    }

    /*====== HELPERS ========*/
    static copyTextToClipboard(text) {
        var textArea = document.createElement("textarea");
    
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = 0;
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Copying text command was ' + msg);
        } catch (err) {
            console.log('Oops, unable to copy');
        }
        document.body.removeChild(textArea);
    }

}