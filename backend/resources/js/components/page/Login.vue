<template>
    <div v-if="isLoggedIn=='no'">
        <h1>{{title}}</h1>
        <b-form>
            <b-form-group label="メールアドレス" description="">
                <code>{{errorMessage.email}}</code>
                <b-form-input v-on:keydown.enter="onSubmit" @change="checkEmail" @blur="checkEmail" v-model="form.email" type="email"
                    placeholder="メールアドレス" required>
                </b-form-input>
            </b-form-group>
            <b-form-group label="パスワード" description="">
                <code>{{errorMessage.password}}</code>
                <b-form-input v-on:keydown.enter="onSubmit" @change="checkPassword" @blur="checkPassword" v-model="form.password" type="password"
                    placeholder="パスワード" required>
                </b-form-input>
            </b-form-group>
            <b-alert show variant="danger" v-if="message">{{message}}</b-alert>
            <b-button variant="primary" @click="onSubmit">ログイン</b-button>
        </b-form>
    </div>
</template>
<script>
    import Header from '../layout/Header'
    import Footer from '../layout/Footer'

    //バリデーションのモジュールを外部ファイルから読み込み
    import * as Validate from '../../modules/validation.js'
    export default {
        components: {
            Header,
            Footer,
        },
        title: 'Login',

        data() {
            return {
                title: 'Login',
                message: null,

                form: {
                    email: '',
                    password: '',
                },
                errorMessage: {
                    'email': null,
                    'password': null,
                },
                isLoggedIn: null,
            }
        },
        mounted() {
            //console.log('もともとアクセスしたかったページは' + this.$store.state.jumpTo)

            this.message = this.$store.state.message

            axios.get("api/loginCheck")
                .then(response => {
                    this.isLoggedIn = 'yes' //trueだと判定までの読み込み中に一瞬ログインフォームが表示されてしまう
                    console.log('ログイン済み')

                    // if ((localStorage.getItem('jumpTo'))) {
                    //     this.$router.push(localStorage.getItem('jumpTo'))
                    //     localStorage.clear()
                    // }else{
                    //     this.$router.push('/account')
                    // }

                    this.$router.push('/account')
                })
                .catch(error => {
                    console.log('未ログイン')
                    this.isLoggedIn = 'no'
                })
        },

        methods: {
            checkEmail() {
                //モジュールからエラーメッセージを取得
                this.errorMessage.email = Validate.email(this.form).message

                //モジュールから真偽を取得
                var result = Validate.email(this.form).result
                return result
            },
            checkPassword() {
                //モジュールからエラーメッセージを取得
                this.errorMessage.password = Validate.password(this.form.password).message

                //モジュールから真偽を取得
                return Validate.password(this.form.password).result

            },
            onSubmit() {
                this.message = null
                var emailResult = this.checkEmail()
                var passwordResult = this.checkPassword()

                if (emailResult && passwordResult) { //check項目が全てtrueなら  
                    axios.post('/api/login', this.form)
                        .then(response => {
                            let userInfo = {
                                id: response.data.user.id,
                                name: response.data.user.name,
                                email: response.data.user.email,
                                token: response.data.token,
                            }
                            this.$store.commit("checkLogin", userInfo)
                            this.$store.commit("resetState") //vuexに保存されているメッセージをリセット

                            //ヘッダーのユーザーネームを読み込むため強制リロード
                            //let jumpTo = localStorage.getItem('jumpTo')
                            //console.log(jumpTo)
                            //localStorage.clear()

                            //console.log('もともとアクセスしたかったページは' + jumpTo)

                            //pushに変えてみた。headerのbefore mountedで監視してるから、セッション切れ後もワンチャンいける。
                            //this.$router.push(jumpTo) //もともとアクセスしたかったページ

                            if ((localStorage.getItem('jumpTo'))) {
                                this.$router.push(localStorage.getItem('jumpTo'))
                                localStorage.clear()
                            } else {
                                this.$router.push('/account')
                            }
                            localStorage.clear()
                        })
                        .catch((error => {
                            console.log(error)
                            this.message = 'ユーザー名またはパスワードが違います。'
                            //もしくはこの情報で新規登録しますか？みたいな。
                            //既に登録されていないメールアドレスが入力された場合。
                        }))
                } else {
                    this.message = '入力内容に不備があります。'
                }
            }
        },

    }

</script>
