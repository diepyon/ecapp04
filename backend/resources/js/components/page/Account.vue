<template>
    <div v-if="isLoggedIn">
        <div>
            <b-card no-body>
                <b-tabs pills card vertical>
                    <b-tab title="ユーザー情報" active>
                        <b-form>
                            <div class="parent" style="width:150px;">
                                <img class="userIcon" :src="blobUrl" />
                                <label style="display:initial;">
                                    <font-awesome-icon :class="activeStatus" @mouseover="beActive"
                                        @mouseleave="beInActive" class="child" :icon="['fa', 'camera']" />
                                    <input type="file" class="form-control-file " ref="file" @change="fileSelected"
                                        accept=".jpg,.jpeg,.png,.gif" style="display:none">
                                </label>
                            </div>
                            <b-form-group description="254文字まで">
                                <code>{{errorMessage.name}}</code>
                                <b-form-input v-model="user.name" @change="checkName" @blur="checkName"
                                    class="form-control" placeholder="表示名" v-on:keydown.enter="userInfoUpdate">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group description="">
                                <code>{{errorMessage.email}}</code>
                                <b-form-input v-model="user.email" @change="checkEmail" @blur="checkEmail" type="email"
                                    class="form-control" placeholder="メールアドレス" v-on:keydown.enter="userInfoUpdate">
                                </b-form-input>
                            </b-form-group>

                            <b-alert show variant="success" v-if="errorMessage.userUpdate=='updated'">更新しました。</b-alert>
                            <b-alert show variant="warning" v-if="errorMessage.userUpdate=='nothing'">変更はありませんでした。
                            </b-alert>
                            <b-alert show variant="warning" v-if="errorMessage.userUpdate=='duplicate'">
                                そのメールアドレスは既にほかのアカウントで利用されています。</b-alert>
                            <b-button @click="userInfoUpdate();" variant="primary">更新</b-button>
                        </b-form>
                    </b-tab>
                    <b-tab title="セキュリティ">
                        <template>
                            <b-form>
                                <b-form-group>
                                    <b-form-input v-model="user.email" autocomplete="username" hidden>
                                    </b-form-input>
                                </b-form-group>

                                <code v-if="errorMessage.currentPassword">{{errorMessage.currentPassword}}</code>
                                <b-form-input type="password" autocomplete="current-password"
                                    v-on:keydown.enter="passwordUpdate" v-model="user.currentPassword"
                                    @change="checkPasswords('currentPassowrd')"
                                    @blur="checkPasswords('currentPassowrd')" placeholder="現在のパスワード">
                                </b-form-input>
                                <div class="mt-2"></div>

                                <code v-if="errorMessage.newPassword">{{errorMessage.newPassword}}</code>
                                <b-form-input type="password" autocomplete="new-password"
                                    v-on:keydown.enter="passwordUpdate" @change="checkPasswords('newPassword')"
                                    @blur="checkPasswords('newPassword')" v-model="user.newPassword"
                                    placeholder="新しいパスワード">
                                </b-form-input>
                                <div class="mt-2"></div>

                                <code v-if="errorMessage.newPasswordConfirm">{{errorMessage.newPasswordConfirm}}</code>
                                <b-form-input type="password" autocomplete="new-password"
                                    v-model="user.newPasswordConfirm" v-on:keydown.enter="passwordUpdate"
                                    @change="checkPasswords('newPasswordConfirm')"
                                    @blur="checkPasswords('newPasswordConfirm')" placeholder="新しいパスワード再入力">
                                </b-form-input>
                                <div class="mt-2"></div>
                            </b-form>
                        </template>
                        <b-alert show variant="success" v-if="errorMessage.passwordUpdate=='success'">更新しました。</b-alert>
                        <b-alert show variant="warning" v-if="errorMessage.passwordUpdate=='oldPasswordError'">
                            現在のパスワードが間違っています。</b-alert>
                        <b-alert show variant="warning" v-if="errorMessage.passwordUpdate=='confirmPassworError'">
                            新しいパスワードが一致しません。</b-alert>
                        <b-button @click="passwordUpdate();" variant="primary">更新</b-button>
                    </b-tab>
                    <b-tab title="Tab 3">
                        <b-card-text>なにかしら</b-card-text>
                    </b-tab>
                </b-tabs>
            </b-card>
        </div>
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
        title: 'Account',

        data() {
            return {
                user: {
                    name: null,
                    email: null,
                    token: null,
                    currentPassword: null,
                    newPassword: null,
                    newPasswordConfirm: null,
                },
                isLoggedIn: false,
                activeStatus: 'inactive',
                fileInfo: null,
                blobUrl: 'storage/default_img/default_icon.jpg',

                errorMessage: {
                    'name': null,
                    'email': null,
                    'currentPassword': null,
                    'newPassword': null,
                    'newPasswordConfirm': null,
                    'passwordUpdate': null,
                    'userUpdate': null,
                },
            }
        },

        mounted() {
            this.getUserInfo()
            this.user.password = null
            this.user.passwordConfirm = null
        },
        methods: {
            beActive() {
                this.activeStatus = 'active'
            },
            beInActive() {
                this.activeStatus = 'inactive'
            },
            fileSelected(event) {
                this.fileInfo = event.target.files[0] //選択されたファイルの情報を変数に格納
                console.log(this.fileInfo)

                if (event.target.files[0] != undefined) {
                    this.blobUrl = URL.createObjectURL(this.fileInfo) //選択されたファイルのURLを取得 

                    this.fileName = this.fileInfo.name //いらんかも
                    this.user.file = this.fileInfo
                } else {
                    this.blobUrl = ""
                }
            },
            userInfoUpdate() {
                this.errorMessage.userUpdate = null

                let postData = new FormData()
                if (this.fileInfo) {
                    postData.append('files[0]', this.fileInfo) //files配列の先頭はthis.fileInfo
                    postData.append('extention', this.fileInfo.name.split('.').pop()) //拡張子を取得
                }

                postData.append('id', this.user.id)
                postData.append('name', this.user.name)
                postData.append('email', this.user.email)


                axios.post("/api/account/update", postData)
                    .then(response => {
                        this.errorMessage.userUpdate = response.data //更新されたかどうかの結果を格納したメッセージ

                        //イランと思う
                        if (this.errorMessage.userUpdate == 'updated') {
                            this.$bvModal.hide('modal-scoped')
                        } //変化があれば閉じる

                        this.getUserInfo() //ユーザー情報更新
                    })
                    .catch(error => {
                        console.log(error.response.data.message)
                        if (error.response.data.message.match(/Duplicate/)) {
                            //strにhogeを含む場合の処理
                            console.log('メールアドレス重複')
                            this.errorMessage.userUpdate = 'duplicate'
                        } else if (error.response.data.message.match(/Unauthenticated/)) {
                            this.$store.commit("message", 'ログインしてください。')
                            this.$router.push("/login") //ログイン画面にジャンプ
                        }
                        //console.log(error.response.data.errors.email[0])
                    })
            },
            getUserInfo() {
                axios.get("/api/loginCheck")
                    .then(response => {
                        this.isLoggedIn = true
                        let currentUser = response.data
                        console.log(currentUser)
                        this.user.name = currentUser.name
                        this.user.email = currentUser.email

                        console.log(currentUser)

                        //let icon = currentUser.icon

                        if (currentUser.icon) {
                            this.blobUrl = '/storage/user_icon/' + currentUser.icon 
                        } //最新版のユーザーアイコンを取得

                        this.user.name = this.user.name
                        this.user.email = this.user.email
                        this.user.id = response.data.id //カレントユーザーのIDを取得

                        //vuexでリアルタイムにユーザーの情報を更新（ヘッダーが変化を監視）
                        let userInfo = {
                            name: this.user.name,
                            email: this.user.name,
                            //アイコンの情報もヘッダーに表示したいなら必要かも
                        }
                        this.$store.commit("checkLogin", userInfo)
                    })
                    .catch(error => {
                        //console.log(error)
                        this.isLoggedIn = false
                        this.$store.commit("message", 'ログインしてください。')
                        this.$router.push("/login") //ログイン画面にジャンプ
                    })
            },

            //こいつら、まとめて一つのメソッドにできひん？引数にnameとかemailとかいれたらいいやん
            //なんかうまくいかんから、発火するメソッドをifで分岐したら良いと思う
            //引数にバリデーションチェックしたい項目の名前
            //if文に発動するモジュール

            checkEmail() {
                //モジュールからエラーメッセージを取得
                this.errorMessage.email = Validate.email(this.user).message

                //モジュールから真偽を取得
                var result = Validate.email(this.user).result
                return result
            },
            checkName() {
                //モジュールからエラーメッセージを取得
                this.errorMessage.name = Validate.name(this.user).message

                //モジュールから真偽を取得
                var result = Validate.name(this.user).result
                return result
            },

            //パスワードバリデーションチェック
            checkPasswords(value) {
                if (value == 'currentPassowrd') {
                    this.errorMessage.currentPassword = Validate.password(this.user.currentPassword).message
                } else if (value == 'newPassword') {
                    this.errorMessage.newPassword = Validate.password(this.user.newPassword).message
                } else if (value == 'newPasswordConfirm') {
                    this.errorMessage.newPasswordConfirm = Validate.password(this.user.newPasswordConfirm).message

                }

                if (Validate.password(this.user.currentPassword).result && Validate.password(this.user.newPassword)
                    .result && Validate.password(this.user.newPasswordConfirm).result) {
                    console.log('全部入力されている')
                    return true
                } else {
                    console.log('入力されていない項目がある')
                    return false
                }
            },
            passwordUpdate() {
                this.errorMessage.passwordUpdate = null

                let currentPassowrd = this.checkPasswords('currentPassowrd')
                let newPassword = this.checkPasswords('newPassword')
                let newPasswordConfirm = this.checkPasswords('newPasswordConfirm')

                if ((currentPassowrd && newPassword && newPasswordConfirm && (this.user.newPassword == this.user
                        .newPasswordConfirm))) {
                    console.log('全部trueかつ新しい2つのパスワードも一致')

                    axios.post("/api/account/checkOldPassword", {
                            userId: this.user.id,
                            currentPassword: this.user.currentPassword,
                            password: this.user.newPassword
                        })
                        .then(response => {
                            console.log('フロントは問題なし。apiに投げるぜ')
                            this.errorMessage.passwordUpdate = response.data

                            if (this.errorMessage.passwordUpdate == 'success') {
                                this.user.currentPassword = null
                                this.user.newPassword = null
                                this.user.newPasswordConfirm = null
                            }
                        })
                } else if (this.user.newPassword != this.user.newPasswordConfirm) {
                    console.log('新しいパスワード不一致')
                    this.errorMessage.passwordUpdate = 'confirmPassworError'

                }
            },
        },
    }

</script>
<style scoped>
    .parent {
        position: relative;
    }

    .parent .child {
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        margin: 0;
        /*余計な隙間を除く*/
        padding: 0;
        color: rgba(99, 99, 99, 0.721);
        /*余計な隙間を除く*/
        font-size: 30px;
        /*サイズ*/
    }

    .parent img {
        width: 100%;
    }

    .userIcon {
        width: 150;
        height: 150px;
        background: #ffffff;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: .7em;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .inactive {
        opacity: 0.8;
    }

    .active {}

</style>
