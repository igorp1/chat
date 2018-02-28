<template>
    <div>
        <header ><router-link to="/"><h1 id="title">chat_</h1></router-link></header>
        <div class="pure-g">
            <div class="pure-u-md-1-5 pure-u-lg-1-5"></div>
            <div class="pure-u-1 pure-u-md-3-5 pure-u-lg-3-5" id="router-outlet-container">
                <router-view/>
            </div>
            <div class="pure-u-md-1-5 pure-u-lg-1-5"></div>
        </div>
    </div>
</template>

<script>
export default {
  name: 'app'
}
</script>

<style>
@import url('https://fonts.googleapis.com/css?family=Titillium+Web');
@import url('https://unpkg.com/purecss@1.0.0/build/pure-min.css');
@import url('https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css');

#router-outlet-container, #title{
    font-family: 'Titillium Web', sans-serif !important;
}

#title{
    margin:0;
    text-align: center;
    color: #AA73DB;
}

header{
    background-color: rgba(255, 255, 255, 0.95);
    border-bottom: #AA73DB solid 2px;
    position: fixed;
    width: 100vw;
    left: 0;
    top: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    padding-top: .3em;
    padding-bottom: .3em; 
}

header a{
    text-decoration: none ;
}

/* ============= GLOBAL STYLES ============= */

body {
    font-family: 'Titillium Web', sans-serif; 
    padding: 1em;
}


input{
    width: 300px;
    border: none;
    outline: none;
    border-radius: .2em;
    padding: .2em .75em;
    border-bottom: #AA73DB solid 2px;
}



.center{ text-align: center; }
.transition{transition: all 0.3s cubic-bezier(.25,.8,.25,1);}

.shadow-low { box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); }
.shadow-med { box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); }
.shadow-high{ box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23); }



button{
    border: solid white 2px;
    background-color: rgb(241, 241, 241);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    color: #AA73DB;
    padding: .3em;
    padding-left: 1em;
    padding-right: 1em;
    outline: none;
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    margin: 1em;
}

button:hover{
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
    background-color: rgb(233, 233, 233);
}

button:active{
    box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
    background-color: #AA73DB;
    color: rgb(241, 241, 241);
}

</style>
