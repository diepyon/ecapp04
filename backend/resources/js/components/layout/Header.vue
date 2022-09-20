<template>
    <header>
        <div>
            <b-navbar toggleable="lg" type="dark" variant="dark">
                <b-navbar-brand to="/">NavBar</b-navbar-brand>
                <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
                <b-collapse id="nav-collapse" is-nav>
                    <b-navbar-nav>
                        <b-nav-item :class="{ active: $route.path === '/' }" to="/">Home</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/about' }" to="/about">About</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/stocks/create' }" to="/stocks/create">Post
                        </b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/stocks' }" to="/stocks">Archive</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/image' }" to="/image">画像</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/audio' }" to="/audio">音源</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/video' }" to="/video">映像</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/login' }" to="/login">Login</b-nav-item>
                        <b-nav-item :class="{ active: $route.path === '/register' }" to="/register">Register
                        </b-nav-item>
                    </b-navbar-nav>
                    <!-- Right aligned nav items -->
                    <b-navbar-nav class="ml-auto">
                        <b-nav-item-dropdown v-if="isLoggedIn" right>
                            <!-- Using 'button-content' slot -->
                            <template #button-content>
                                <em>{{ userName}}</em>
                            </template>
                            <b-dropdown-item to="/account">Account</b-dropdown-item>
                            <b-dropdown-item @click="logout">Logout</b-dropdown-item>
                        </b-nav-item-dropdown>
                        <button v-else>ログイン</button>
                    </b-navbar-nav>
                </b-collapse>
            </b-navbar>
        </div>
    </header>
</template>
<script>
    export default {
        data() {
            return {
                userName: null,
                isLoggedIn: false,
                resetFlug:null,
            }
        },
        mounted() {
            //ユーザー名取れないなどログインしていないときだけ走らせれば省エネでは？
            //ていうかbeforemountedに書いてるかいらん？console.logで確認中
            axios.get("/api/loginCheck")
                .then(response => {
                    this.isLoggedIn = true
                    let currentUser = response.data
                    this.user.name = currentUser.name
                    //this.userName = localStorage.getItem('userName')
                    //email: localStorage.getItem("userEmail"),
                    console.log('mountedのログインチェックに成功')
                })
                .catch(error => {
                    this.isLoggedIn = false
                    console.log('mountedに失敗')
                })

            //プロフィール更新時に認識させるから必要
            this.$store.watch(
                (state, getters) => getters.getUserName,
                (newValue, oldValue) => {
                    console.log('user changed! %s => %s', oldValue, newValue)
                    this.userName = newValue

                    //セッション切れログアウトからのログイン時には通知が出ない
                    //oldvalueとnewvalueの動きを確認してリファクタリングが必要

                    let referrer = this.$router.referrer //セッション切れの時のこの部分が知りたい
                    console.log(referrer)

                    if (this.userName && referrer.name == 'login') {
                        //ユーザー名が取得できた&ログインページから飛んできたか、元々のユーザー名がnullなら
                        this.makeToast('ログインしました。')
                    } else if (this.userName == null) {
                        //ユーザー名がnullになったら
                        this.makeToast('ログアウトしました。')
                    }

                    //vuexのユーザー名が変わったことを検知した上でサンクタムのログインチェック処理
                    axios.get("/api/loginCheck")
                        .then(response => {
                            this.isLoggedIn = true
                            let userInfo = {
                                name: this.$store.getters.getUserName,
                                //email: localStorage.getItem("userEmail"),
                            }
                            this.$store.commit("updateUser", userInfo);
                            console.log('gettersのログインチェックに成功')
                        })
                        .catch(error => {
                            this.isLoggedIn = false
                            console.log('gettersのログインチェックに失敗')
                        })
                }
            )
            let userInfo = {
                name: localStorage.getItem("userName"),
                //email: localStorage.getItem("userEmail"),
            }
            this.$store.commit("updateUser", userInfo);
            //console.log(localStorage.getItem("userName")) //localstorageの値をとる
        },

        beforeUpdate() {
            //セッション切れの時にもちゃんと動作するか確認
            if (this.isLoggedIn == false) {
                axios.get("/api/loginCheck")
                    .then(response => {
                        this.isLoggedIn = true
                        let userInfo = {
                            name: this.$store.getters.getUserName,
                            //email: localStorage.getItem("userEmail"),
                        }
                        this.$store.commit("updateUser", userInfo)
                        console.log('beforeupdateのチェックに成功')
                    })
                    .catch(error => {
                        this.isLoggedIn = false
                        console.log('beforeupdateのチェックに失敗')
                    })
            }
        },

        computed: {

        },
        methods: {
            makeToast(message) {
                this.$bvToast.toast(message, {
                    title: '通知',
                    toaster: 'b-toaster-bottom-right',
                    autoHideDelay: 5000,
                    appendToast: false
                })
            },
            logout() {
                axios.post("/api/logout")
                    .then(response => {
                        localStorage.clear()
                        this.$store.commit("logout") //vuexの内容をリセット

                        //pushに変えてみた。headerのbefore mountedで監視してるから、セッション切れ後もワンチャンいける。
                        this.$router.push("/login") //ログイン画面にジャンプ
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
        },
    };

</script>
