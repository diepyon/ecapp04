<template>
    <div v-if="isLoggedIn=='no'">
        <h1>{{title}}</h1>
        <b-form>
            <b-form-group label="メールアドレス" description="">
                <code>{{errorMessage.email}}</code>
                <b-form-input v-model="form.email" v-on:keydown.enter="register" @change="checkEmail" @blur="checkEmail"
                    type="email" class="form-control">
                </b-form-input>
            </b-form-group>

            <b-form-group label="パスワード" description="">
                <label for="">password</label>
                <code v-if="errorMessage.password">{{errorMessage.password}}</code>
                <b-form-input v-model="form.password" v-on:keydown.enter="register" @change="checkPassword"
                    @blur="checkPassword" type="password" class="form-control">
                </b-form-input>
            </b-form-group>
            <b-alert show variant="danger" v-if="errorMessage.submit">{{errorMessage.submit}}</b-alert>
            <button type="button" class="btn btn-primary" @click="register">登録</button>
        </b-form>
    </div>
</template>
<style scoped>
    .enter {
        border: 10px dotted powderblue;
    }
    .preview {
        margin: .5em;
    }
    /*ファイルプレビューエリアの余白*/
    .preview img,
    video {
        width: 100%;
        max-width: 500px;
    }
</style>
<script>
    import Header from '../layout/Header'
    import Footer from '../layout/Footer'

    //バリデーションのモジュールを外部ファイルから読み込
    import * as Validate from '../../modules/validation.js'


    export default {
        components: {
            Header,
            Footer,
        },
        title: 'Register',
        data() {
            return {
                title: 'Register',
                //あらかじめ変数を定義してあげないとフロントが混乱する
                form: {
                    email: '',
                    password: '',
                },

                errorMessage: {
                    'email': null,
                    'password': null,
                    'submit': null
                },
                isLoggedIn: null,
            }
        },
        mounted() {
            axios.get("api/loginCheck")
                .then(response => {
                    this.isLoggedIn = 'yes' //trueだと判定までの読み込み中にフォームが表示されてしまう
                    console.log('ログイン済み')
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
            login() {
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
                        this.$router.push('/account')
                        localStorage.clear()
                    })
            },

            register() {
                this.errorMessage.submit = null
                //投稿とボタンが押されたときに発動するメソッド
                //投稿直前にも入力に不備がないかチェック
                var emailResult = this.checkEmail()

                var passwordResult = this.checkPassword()
                console.log(passwordResult)

                if (emailResult && passwordResult) { //check項目が全てtrueなら      
                    //バリデーション関数のreturnがどちらもtrueなら下記実行
                    axios.post('/api/register', this.form) //apiのルートを指定。第2引数には渡したい変数を入れる
                        .then(response => {
                            //ログインしましたって出したい

                            //ログイン処理
                            this.login()
                        })
                        .catch(error => {
                            if (error.response.data.errors.email[0] == 'The email has already been taken.') {
                                console.log(error.response.data.errors.email[0])
                                this.errorMessage.submit = 'このメールアドレスはすでに登録されています。'
                            } else {
                                alert('あかんかったわ、コンソール見て');
                            }
                        })
                } else {
                    this.errorMessage.submit = '入力内容に不備があります。'
                }
            }
        },
    }

</script>
