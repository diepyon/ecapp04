<template>
    <div>
        <div v-if="stock && stock.status == 'inspecting'">
            <h1>審査中です。</h1>
        </div>

        <div v-if="stock && stock.status == 'delete'">
            <h1>この投稿は削除されました。</h1>
        </div>

        <div v-if="(stock && stock.status == 'publish')||(currentUser && currentUser.role=='administrator')">
            <div v-if="stock">
                <SingleImage v-if="stock && stock.genre == 'image'" v-bind:stock="stock" />
                <SingleVideo v-else-if="stock && stock.genre == 'video'" v-bind:stock="stock" />
                <SingleAudio v-else-if="stock && stock.genre == 'audio'" v-bind:stock="stock" />
            </div>
            <span v-if="stock ">
                <h1>{{ stock.name }}</h1>
                <span class="">
                    <p>{{ stock.detail }}</p>
                </span>
                <b-row>
                    <b-col sm="4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                {{ stock.subGenre }}
                            </li>
                            <li class="list-group-item">
                                <font-awesome-icon :icon="['fas', 'calendar']" /> {{ date }}
                            </li>
                        </ul>
                    </b-col>
                    <b-col sm="4">
                        <span v-for="tag in stock.tags" :key="tag.id">
                            <a v-if="tag[0]" href="#" class="badge badge-secondary">
                                {{ tag }}
                            </a>
                        </span>
                    </b-col>
                    <b-col sm="4">
                        <div class="">
                            created by
                            <img v-if="stock.author_icon" class="userIcon" style="width: 40px; height: 40px"
                                :src="'/storage/user_icon/' + stock.author_icon" />
                            <img v-else class="userIcon" style="width: 40px; height: 40px"
                                :src="'/storage/default_img/default_icon.jpg'" />
                            {{ stock.author_name }}
                        </div>
                    </b-col>
                </b-row>
            </span>
        </div>

        <div v-if="(currentUser && currentUser.role=='administrator') && (stock && stock.status == 'inspecting')">
            <b-card bg-variant="dark" text-variant="white" title="審査">
                <b-card-text>
                    承認しますか？
                </b-card-text>

                <b-button href="#" variant="primary" @click="approval">承認</b-button>

                <!-- Button trigger modal -->
                <b-button href="#" variant="danger" data-toggle="modal" data-target="#exampleModalCenter">却下</b-button>
            </b-card>

            <!-- Modal -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">却下</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <b-form-group label="理由" v-slot="{ ariaDescribedby }">
                                <code>{{ errorMessage.selectedLength }}</code>
                                <b-form-checkbox-group v-model="selected" :options="options"
                                    :aria-describedby="ariaDescribedby" name="flavour-2a" stacked>
                                </b-form-checkbox-group>
                                <b-form-input v-if="othercheckExist()" v-model="rejected_reason_comment"
                                    placeholder="その他の場合入力"></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="modal-footer">
                            <b-button href="#" variant="danger" @click="reject">確定</b-button>
                            <b-button variant="secondary" data-dismiss="modal">キャンセル</b-button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import Header from "../layout/Header";
    import Footer from "../layout/Footer";
    import SingleImage from "../layout/SingleImage";
    import SingleVideo from "../layout/SingleVideo";
    import SingleAudio from "../layout/SingleAudio";
    import * as fns from "date-fns";

    export default {
        props: {
            id: Number,
        },
        components: {
            Header,
            Footer,
            SingleImage,
            SingleVideo,
            SingleAudio,
        },
        //title: 'タイトルどうしよう'
        data() {
            return {
                stock: null,
                stockPromise: null, //中間変数
                date: null,
                author_id: null,
                authorName: null,
                currentUser: null,


                selected: [], // rejected_reasonにしたほうがバックエンドで受け取り時の書き方がすっきりするかも
                options: [],
                rejected_reason_comment: null,

                errorMessage: {
                    selectedLength: null,
                    otherReason: null,
                },


            };
        },
        methods: {
            logincheck() {
                axios.get("/api/loginCheck")
                    .then(response => {
                        this.isLoggedIn = true
                        let currentUser = response.data
                        this.currentUser = currentUser


                        //console.log('stocksingleのログインチェックに成功')
                        //console.log(this.currentUser)
                    })
                    .catch(error => {
                        this.isLoggedIn = false
                        //console.log('stocksingleのログインチェックによると未ログイン状態')
                    })

                //不要
                // axios.get("/api/aaa")
                //     .then(response => {
                //         console.log('aaa')
                //         console.log(response)
                //     }).catch(error => {
                //         console.log('akan')
                //     })
            },

            approval() {
                console.log(this.stock.id)
                axios.post("/api/stocks/approval", {
                    id: this.stock.id
                }).then(response => {
                    console.log(response.data)
                    if (response.data === 1) {
                        //レコード再読み込み
                        // const stock = axios.get("/api/stocks/" + this.id);
                        // this.stock = stock.data.data
                    }
                })
            },
            selectCheck() {
                if (this.selected.length === 0) {
                    this.errorMessage.selectedLength = "選択してください";
                    return false;
                } else {
                    this.errorMessage.selectedLength = "";
                    return true;
                }
            },
            otherComment() {
                //otherが選択されているかつコメント欄なしならfalse

                //otherが選択されているかつコメント欄ありならtrue
                if (this.othercheckExist() && this.rejected_reason_comment) {
                    console.log("otherが選択されているかつコメント欄ありだからOK")
                    return true
                } else if (!this.othercheckExist()) {
                    console.log('otherが選択されていないからOK(コメントの有無は問わない)')
                    return true
                } else if (this.othercheckExist() && !this.rejected_reason_comment) {
                    console.log('otherが選択されているのにコメントがないからあかん')
                    return false
                }
            },
            getKeyByValue(obj, value) { //選択済みの項目に特定の値があるかチェック
                return Object.keys(obj).find(key => obj[key] === value);
            },
            othercheckExist() {
                if (this.getKeyByValue(this.selected, "other") !== undefined) {
                    return true
                } 
                    return false

            },
            reject() {

                
                let selectCheck = this.selectCheck();
                let otherComment = this.otherComment();

                //console.log(this.selected)
                let rejected_reason_comment =this.rejected_reason_comment

                if (!this.othercheckExist()) {
                    rejected_reason_comment = null //その他のチェックなければコメントは投稿しない
                }

                let postData = {
                    rejected_reason:this.selected,
                    rejected_reason_comment:rejected_reason_comment,
                }

                console.log('投げたいデータは')
                console.log(postData)


                //console.log(selectCheck)


                //1件も選ばれていなければバリデーションでとめたい
                //idも投げたい
                axios.post("/api/stocks/reject", this.selected).then(response => {
                    //console.log(response.data)
                })

            },
            getRejectedReasons() {
                axios.get("/api/stocks/getRejectedReasons", {
                    params: {
                        genre: this.stock.genre
                    }
                }).then(response => {
                    this.options = []
                    let options = response.data;

                    options.filter((options) => {
                        this.options.push({
                            value: options.reason,
                            text: options.reasonText,
                        });
                    });
                })
            }
        },
        created() {
            this.stockPromise = axios.get("/api/stocks/" + this.id); //中間変数
        },
        async mounted() {
            this.logincheck()

            const stock = await this.stockPromise; //さらに中間変数
            this.stock = stock.data.data;
            this.stockPromise = null; //createdで定義した方の中間テーブルは用済み

            this.date = fns.format(
                new Date(this.stock.created_at),
                "yyyy/MM/dd"
            );

            this.getRejectedReasons()
        },
    };

</script>
<style scoped>
    ::v-deep .btn {
        margin-top: 0.5em;
    }

    ::v-deep li.list-group-item>svg {
        margin-right: 0.2em;
    }

    ::v-deep .thumbnail {
        width: 100%;
        max-width: 400px;
    }

    .parent {
        position: relative;
    }

    .userIcon {
        width: 150;
        height: 150px;
        background: #ffffff;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.7em;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

</style>
