<template>

    <div v-if="isLoggedIn">
        <h1>{{title}}</h1>
        <code>{{errorMessage.file}}</code>
        <div class="drop_area" v-show="previewArea==false" @dragenter="dragEnter" @dragleave="dragLeave"
            @dragover.prevent @drop.prevent="dropFile" :class="{enter: isEnter}">
            <div>販売する作品をドラッグ＆ドロップ</div>
            <div>png,jpg,mp4,mov,wav,mp3</div>
            <label>
                <span class="btn btn-primary">
                    選択
                    <input type="file" class="form-control-file " ref="file" @change="fileSelected"
                        accept=".jpg,.jpeg,.png,.gif,.mp3,.wav,.m4a,.mp4,.mov" style="display:none">
                </span>
            </label>
        </div>

        <div class="form-group">
            <div v-show="previewArea" class="preview">
                <div class="delete-mark" @click="deleteFile">×</div>
                <img v-if="genre=='image'" :src="blobUrl">
                <video v-on:loadedmetadata="videoInfo" controls v-if="genre=='video'" id="video" loop autoplay muted
                    :src="blobUrl"></video>
                <span id="audioPreview" v-if="genre=='audio'">
                    <audio v-on:loadedmetadata="audioInfo" id="audio" controls :src="blobUrl"></audio>

                    <div class="flex">
                        <span class="button">
                            <b-button v-if="playing" @click="stopAudio()">
                                <font-awesome-icon :icon="['fa', 'stop']" />
                            </b-button>
                            <b-button v-else @click="playAudio()">
                                <font-awesome-icon :icon="['fa', 'play']" />
                            </b-button>
                        </span>
                        <div class="waveform">
                            <av-waveform class="player" id="waveform" v-if="genre=='audio'" :audio-src="blobUrl"
                                playtime-slider-color="white" played-line-color="black" :playtime="false"
                                :canv-width="waveWidth" noplayed-line-color="#bababa">
                            </av-waveform>
                        </div>
                    </div>
                </span>
            </div>
        </div>


        <div id="form">
            <div class="form-group">
                <label for="">作品名</label>
                <code>{{errorMessage.name}}</code>
                <input v-model="name" @change="checkName" @blur="checkName" type="txt" class="form-control">
            </div>





            <div class=form-group>
                <label for="">販売価格</label>
                <select v-model="feeSelected">
                    <option v-for="feeOption in feeOptions" v-bind:value="feeOption.value" :key="feeOption.value">
                        {{ feeOption.text }}
                    </option>
                </select>
            </div>

            <template v-if="genre">
                <div>
                    <code>{{errorMessage.subGenre}}</code>
                    <b-form-select v-model="subGenreSelected" :options="subGenreOption" class="mb-3"
                        @change="checkSubGenre" @blur="checkSubGenre">
                        <!-- This slot appears above the options from 'options' prop -->
                        <template #first>v-bind:options="[genre ? 'active']"
                            <b-form-select-option :value="null" disabled>ジャンルを選択してください。
                            </b-form-select-option>
                        </template>
                    </b-form-select>

                    <div class="mt-3">subGenreSelected: <strong>{{ subGenreSelected }}</strong></div>
                </div>
            </template>


            <div>
                <input type="text" v-model="tag" v-on:keydown.enter="addTag" placeholder="タグ" :disabled="disabled" />
                <b-button @click="addTag" :disabled="disabled">追加</b-button>
            </div>

            <span v-for="tag in tags" :key="tag.id" href="#" class="badge mb-3 mb-sm-0 badge-secondary"
                style="margin-right:.5em;">
                <b-button @click="deletTag(tag)" variant="secondary" pill style="font-size:75%;padding:initial;">
                    <font-awesome-icon :icon="['fa', 'times']" />
                </b-button>
                {{tag}}
            </span>

            <!-- 後で消す↑ -->
            <div class="form-group">
                <label for="">商品説明</label>
                <code>{{errorMessage.detail}}</code>
                <textarea v-model="detail" @change="checkDetail" @blur="checkDetail" class="form-control" id=""
                    rows="5"></textarea>
            </div>


            <div class="form-submit">
                <b-button v-if="uploading" variant="primary" disabled>
                    <b-spinner small type="grow"></b-spinner>
                    投稿中
                </b-button>

                <button v-else type="button" class="btn btn-primary" @click="stockCreate">投稿</button>
            </div>




        </div>
    </div>
</template>

<script>
    import Header from '../layout/Header'
    import Footer from '../layout/Footer'

    export default {
        components: {
            Header,
            Footer,
        },
        data() {
            return {
                title: 'Stock Create',
                //あらかじめ変数を定義してあげないとフロントが混乱する
                name: '',
                detail: '',
                isEnter: false, //ドラッグアンドドロップフォームの変数初期値
                fileInfo: null, //inputfileの情報を格納する変数
                videDuration: null,
                fileType: null,
                fileName: '',
                deleteButton: false,

                //ジャンル選択の配列
                genre: '',
                genreString: '',
                //金額選択の配列

                subGenreSelected: null,
                subGenreOption: [],

                feeSelected: 1500,
                feeOptions: [{
                        text: '￥1,500',
                        value: 1500
                    },
                    {
                        text: '￥5,000',
                        value: 5000
                    },
                    {
                        text: '￥10,000',
                        value: 10000
                    },
                    {
                        text: '￥20000',
                        value: 20000
                    },
                ],
                //配列にしたい
                maxNameLength: 10,
                maxDetailLength: 120,
                errorMessage: {
                    'name': null,
                    'detail': null,
                    'file': null,
                    'subGenre': null,
                },
                blobUrl: null,
                previewArea: false,
                isLoggedIn: false,
                currentUserid: null,
                waveWidth: null,
                playing: false,
                uploading: false,

                tag: null,
                tags: [],

                disabled: false,

            }
        },
        mounted() {
            axios.get("/api/loginCheck")
                .then(response => {
                    this.isLoggedIn = true
                    this.currentUserid = response.data.id
                })
                .catch(error => {
                    console.log(error)
                    this.isLoggedIn = false
                    this.$store.commit("message", 'ログインしてください。')
                    // this.$store.commit("jumpTo", this.$route.path)

                    //ローカルストレージにこのurlを記憶
                    localStorage.setItem('jumpTo', this.$route.path);

                    //vuexバージョン（こっちをLoginlvueで使うとうまくいかない）
                    this.$store.commit('jumpTo', this.$route.path)

                    this.$router.push("/login") //ログイン画面にジャンプ
                })
            let width = window.innerWidth
            console.log(width)
            if (width < 576) {
                console.log('576より小さい')
                this.waveWidth = 280
            } else if (width < 767) {
                console.log('767より小さい')
                this.waveWidth = 330
            } else if (width <= 992) {
                console.log('992より小さい')
                this.waveWidth = 450
            } else if (992 <= width) {
                console.log('992以上')
                this.waveWidth = 750
            }
        },

        methods: {
            addTag() {
                if (this.tag && (this.tags.indexOf(this.tag)) == -1) {
                    this.tags.push(this.tag)
                    console.log('入力値は空じゃないし、重複はないから追加する')
                } else {
                    console.log('重複あり、もしくは空だから追加しない')
                }
                console.log(this.tags)
                this.tag = null

                console.log(this.tags.length)
                if (this.tags.length >= 5) {
                    console.log('上限')
                    this.disabled = true
                }
            },
            deletTag(tag) {
                console.log('削除対象は' + tag)

                var index = this.tags.indexOf(tag);
                console.log('index番号は')
                console.log(index)

                console.log('削除前')
                console.log(this.tags)

                this.tags.splice(index, 1)
                console.log('削除後')
                console.log(this.tags)

                if (this.tags.length < 5) {
                    this.disabled = false
                }
            },

            getSubgenre() {
                axios.get("/api/stocks/getSubgenre?genre=" + this.genre)
                    .then(response => {
                        this.subGenreOption = []

                        let subgenres = response.data
                        subgenres.filter(subgenre => {
                            this.subGenreOption.push({
                                value: subgenre.subgenre,
                                text: subgenre.subgenreText
                            })
                        });
                    }) //サブジャンルの選択肢をデータベースから取得
            },
            videoInfo() {
                console.log(video)
                this.checkFile(video.duration) //プレビュー用のvideoタグから長さを取得
            },
            audioInfo() {
                console.log(audio)
                //audio.play() //読み込みと同時に再生も可能
                this.checkFile(audio.duration)
            },
            playAudio() {
                document.getElementById('waveform').children.item(0).children.item(0).play()
                this.playing = true
            },
            stopAudio() {
                document.getElementById('waveform').children.item(0).children.item(0).pause()
                document.getElementById('waveform').children.item(0).children.item(0).currentTime = 0;
                this.playing = false
            },

            deleteFile() {
                this.fileInfo = null;
                this.previewArea = false
                this.deleteButton = false
                this.fileName = null
                this.$refs.file.value = null; //input fileクリア
                this.genre = null
                this.genreString = null
                this.subGenreOption = []
                this.subGenreSelected = null

                this.playing = false
                //サブジャンルも消す(消せてるはず)
            },

            //バリテーション
            checkName() {
                var n = ''
                var n = this.name.length //nameの文字数を取得
                if (n > this.maxNameLength) { //maxNameLengthはdata()内で定義
                    this.errorMessage.name = String(this.maxNameLength) + "文字以内で入力してください。"
                } else if (n == 0) {
                    this.errorMessage.name = "何か入力してください。"
                } else {
                    this.errorMessage.name = ""
                }
                //document.getElementById('nameCheck').innerHTML = nameMessage
                if (this.errorMessage.name == "") {
                    var result = true
                } else {
                    var result = false
                } //nameの入力に問題がなければtrueを返す
                return (result)
            },
            checkSubGenre() {
                console.log(this.subGenreSelected)
                if (this.subGenreSelected == null || this.subGenreSelected == undefined) {
                    this.errorMessage.subGenre = "選択してください"
                    return false
                } else {
                    this.errorMessage.subGenre = ""
                    return true
                }
            },
            checkDetail() {
                var n = ''
                var n = this.detail.length //detailの文字数を取得
                if (n > this.maxDetailLength) {
                    this.errorMessage.detail = String(this.maxDetailLength) + "文字以内で入力してください。"
                } else if (n == 0) {
                    this.errorMessage.detail = "何か入力してください。"
                } else {
                    this.errorMessage.detail = ""
                }
                //document.getElementById('detailCheck').innerHTML = errorMessage.detail
                if (this.errorMessage.detail == "") {
                    var result = true
                } else {
                    var result = false
                } //detailの入力に問題がなければtrueを返す
                return (result)
            },
            checkFile(duration) {
                this.previewArea = false //previewエリアのタグを非表示

                if (this.fileInfo == null || this.fileInfo == undefined) {
                    this.errorMessage.file = "選択してください"
                    this.deleteFile()
                } else if (this.fileInfo.size > 1073741824) {
                    //1GBなら1073741824
                    this.errorMessage.file = "ファイルサイズ上限の1GBを超えています。"
                    this.deleteFile()
                } else if (this.fileInfo.size <= 0) {
                    this.errodMessage.file = "ファイル不正です。サイズが0KBです。"
                    this.deleteFile()
                } else if (this.genre == 'video' && duration > 60) { //〇秒以上の動画なら
                    this.errorMessage.file = "投稿できる動画は60秒までです。"
                    this.deleteFile()
                } else if (this.genre == 'audio' && duration > 300) { //〇秒以上の動画なら
                    this.errorMessage.file = "投稿できる音源は5分までです。"
                    this.deleteFile()
                } else {
                    this.errorMessage.file = ""
                    this.previewArea = true //previewエリアのタグを表示
                }
                //document.getElementById('fileCheck').innerHTML = errorMessage.file
                if (this.errorMessage.file == "") {
                    var result = true
                } else {
                    var result = false
                    this.deleteFile() //ファイル情報をクリア
                } //fileの入力に問題がなければtrueを返す
                return (result)
            },

            dragEnter() {
                this.isEnter = true;
            },
            dragLeave() {
                this.isEnter = false;
            },
            dragOver() {
                console.log('DragOver')
            },
            dropFile(event) {
                this.fileInfo = [...event.dataTransfer.files][0] //選択されたファイルの情報を変数に格納

                this.fileType = this.fileInfo.type;

                if (this.fileType.match('video') || this.fileType.match('audio') || this.fileType.match('image')) {
                    this.fileName = this.fileInfo.name
                } else {
                    alert('そのファイル形式は選択できません。')
                    this.fileInfo = null
                }

                this.isEnter = false;

                if (this.fileInfo) {
                    this.errorMessage.file = null //ファイル未選択のバリデーションエラーが出てたら消す
                }

                if (this.fileInfo != undefined) {
                    this.blobUrl = URL.createObjectURL(this.fileInfo) //選択されたファイルのURLを取得  
                } else {
                    this.blobUrl = ""
                }
                this.genreSelect()
            },

            fileSelected(event) {
                this.fileInfo = event.target.files[0] //選択されたファイルの情報を変数に格納

                if (this.fileInfo) {
                    this.errorMessage.file = null //ファイル未選択のバリデーションエラーが出てたら消す
                }

                if (event.target.files[0] != undefined) {
                    this.blobUrl = URL.createObjectURL(this.fileInfo) //選択されたファイルのURLを取得 
                    this.fileName = this.fileInfo.name
                } else {
                    this.blobUrl = ""
                }
                this.genreSelect()
                this.getSubgenre()
            },

            genreSelect() {
                console.log('ジャンル生成メソッド')

                let result = this.checkFile() //ファイルに問題がないかチェック

                if (result && this.fileInfo && this.fileInfo.type.match(
                        'image')) { //問題がないファイルが存在（選ばれていて）なおかつ画像なら
                    this.genre = 'image'
                    this.genreString = "画像"


                } else if (result && this.fileInfo && this.fileInfo.type.match(
                        'quicktime')) { //問題ないファイル存在が（選ばれていて）なおかつ動画なら
                    this.genre = 'video'
                    this.genreString = "映像"

                    //macのmovファイル？プレビューできないかもしれないイことを説明
                } else if (result && this.fileInfo && this.fileInfo.type.match(
                        'video')) { //問題ないファイル存在が（選ばれていて）なおかつ動画なら
                    this.genre = 'video'
                    this.genreString = "映像"

                } else if (result && this.fileInfo && this.fileInfo.type.match(
                        'audio')) { //問題ないファイル存在が（選ばれていて）なおかつ音源なら
                    this.genre = 'audio'
                    this.genreString = "音源"

                } else {
                    this.blobUrl = null
                    this.previewArea = false
                }
            },
            stockCreate() { //投稿とボタンが押されたときに発動するメソッド
                //投稿直前にも入力に不備がないかチェック
                let nameResult = this.checkName()
                let detailResult = this.checkDetail()
                let fileResult = this.checkFile()
                let subGenreReulst = this.checkSubGenre()

                if (nameResult && detailResult && fileResult && subGenreReulst) { //check項目が全てtrueなら

                    this.uploading = true

                    let postData = new FormData()
                    postData.append('files[0]', this.fileInfo) //files配列の先頭はthis.fileInfo
                    postData.append('form[extention]', this.fileInfo.name.split('.').pop()) //拡張子を取得
                    postData.append('form[name]', this.name)
                    postData.append('form[genre]', this.genre)
                    postData.append('form[subGenre]', this.subGenreSelected)
                    postData.append('form[fee]', this.feeSelected)
                    postData.append('form[detail]', this.detail)
                    postData.append('userId', this.currentUserid)
                    postData.append('form[tags]', this.tags)

                    //バリデーション関数のreturnがどちらもtrueなら下記実行
                    axios.post('/api/stocks/create', postData) //api.phpのルートを指定。第2引数には渡したい変数を入れる（今回は配列postData=入力された内容）
                        .then(response => {
                            this.makeToast('投稿できました')
                            //投稿に成功したらv-modelを使って書くフォームをクリア
                            this.name = ""
                            this.fileName = ""
                            this.fileInfo = null
                            this.$refs.file.value = null; //input fileクリア
                            this.genre = ""
                            this.feeSelected = 1500
                            this.detail = ""
                            this.genreString = ""
                            this.previewArea = false
                            this.uploading = false
                            this.tags = []
                        })
                        .catch(function (error) {
                            this.makeToast('投稿できませんでした。')
                            console.log(error);

                            this.uploading = false
                        })
                } else {
                    this.makeToast('入力に不備があります。')
                    this.$refs.file.value = null; //input fileクリア
                }
            },
            //投稿後のメッセージに変えたい。
            makeToast(message) {
                this.$bvToast.toast(message, {
                    title: '通知',
                    toaster: 'b-toaster-bottom-right',
                    autoHideDelay: 5000,
                    appendToast: false
                })
            },

        },
    }

</script>
<style scoped>
    .drop_area {
        color: gray;
        font-weight: bold;
        font-size: 1.2em;
        /*display: flex;*/
        justify-content: center;
        align-items: center;
        width: 500px;
        /*height: 300px;*/
        border: 5px solid gray;
        border-radius: 15px;
        max-width: 100%;
        padding: 5em 0.5em;
        text-align: center;
    }

    .enter {
        border: 10px dotted powderblue;
    }

    .delete-mark {
        top: -14px;
        right: -10px;
        font-size: 30px;
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

    #genreSelectForm {
        display: none;
    }

    .flex {
        display: flex;

        align-items: center;
    }

    .waveform {
        width: 100%;
        position: relative;
        overflow: hidden;
        margin-right: calc(((100vw - 100%) / 2) * -1);
    }

    .player {
        overflow-x: auto;
    }

    .button {
        margin-right: .5em;
    }

    ::v-deep audio {
        display: none;
    }

    ::v-deep canvas {
        /* left: 0;
        overflow-x: auto; */
    }

</style>
