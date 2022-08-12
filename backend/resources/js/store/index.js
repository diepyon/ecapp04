import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        message: null,
        name: null,
        email: null,
        token: null,
        jumpTo: '/',//ログイン失敗した人は本来どこに行きたかったのかを記憶
        hoge:true,
    },
    getters: {
        getUserName(state) { return state.name }
    },
    mutations: {
        message(state, message) {
            state.message = message
        },
        jumpTo(state,jumpTo){
            //わざわざリダイレクト前のパスを把握する必要がない説
            state.jumpTo = jumpTo
        },
        resetState(state) {
            state.message = null
            state.jumpTo = '/'
        },
        updateUser(state, user) {
            state.name = user.name
            state.email = user.email
        },
        
        // hoge(state,boolean) {
        //     state.hoge = boolean
        // },
   

        checkLogin(state, userInfo) {
            //vuexに格納
            state.id = userInfo.id
            state.name = userInfo.name
            state.email = userInfo.email
            //state.token = userInfo.token

            //更新時にも残すようにローカルストレージに格納
            localStorage.setItem("id", state.id)
            //localStorage.setItem("token", state.token)
            localStorage.setItem("userName", state.name)
            localStorage.setItem("userEmail", state.email)
        },
        logout(state) { //vuexとローカルストレージの内容を削除
            state.name = null
            state.email = null
            state.token = null
            localStorage.clear() //追記
        },
    }
}, );
export default store;
