<template>
    <div>
        <h1>{{ title }}</h1>
        <h2 v-if="searchKeyword">「{{ searchKeyword }}」の検索結果</h2>
        <div>
            <b-input-group class="search">
                <template #prepend>
                    <b-dropdown :text="subGenreSelected.text">
                        <b-dropdown-item @click="selectSubgenre({ value: null, text: 'すべての音源' })">すべての音源
                        </b-dropdown-item>
                        <b-dropdown-item v-for="subGenreOption in subGenreOptions" :key="subGenreOption.id"
                            @click="selectSubgenre(subGenreOption)">
                            {{ subGenreOption.text }}
                        </b-dropdown-item>
                    </b-dropdown>
                </template>
                <b-form-input v-model="keyword" v-on:keydown.enter=" showArchive(); changePage(1);">
                </b-form-input>
                <template #append>
                    <b-button @click=" showArchive(); changePage(1);" type="" id="btn-search" variant="secondary">
                        <font-awesome-icon :icon="['fa', 'search']" />
                    </b-button>
                </template>
            </b-input-group>
        </div>

        <span v-for="stock in stocks" :key="stock.id">
            <div class="d-flex flex-nowrap bd-highlight">
                <b-col cols="1" class="valign-center">
                    <b-button v-if="playingNow == stock.id" :id="'button' + stock.id" @click="stop(stock.id)">
                        <font-awesome-icon :icon="['fa', 'stop']" />
                    </b-button>
                    <b-button v-else :id="'button' + stock.id" @click="play(stock.id)">
                        <font-awesome-icon :icon="['fa', 'play']" />
                    </b-button>
                </b-col>
                <b-col cols="3" class="scroll-child">
                    <av-waveform class="player" :id="'audio' + stock.id"
                        :audio-src="'/storage/stock_sample/' + stock.filename + '.mp3'" playtime-slider-color="white"
                        :canv-width="waveWidth" played-line-color="black" :playtime="false"
                        noplayed-line-color="#bababa">
                    </av-waveform>
                </b-col>
                <b-col cols="5">
                    <div class="scroll-parent">
                        <div class="scroll-child">
                            <div class="">
                                <span class="lead">
                                    {{ stock.name }}
                                </span>
                            </div>

                            <span v-for="tag in stock.tags" :key="tag.id">
                                <a v-if="tag[0]" href="#" class="badge mb-3 mb-sm-0 badge-secondary">
                                    {{ tag }}
                                </a>
                            </span>
                        </div>
                    </div>
                </b-col>
                <b-col cols="1" class="valign-center" style="background-color: white">
                    <span v-if="stock.fileInfo">{{stock.duration}}</span>
                </b-col>

                <b-col cols="2" class="valign-center" style="">
                    <span id="pc-menu">
                        <b-button pill variant="secondary" size="sm">
                            <font-awesome-icon :icon="['far', 'heart']" />
                        </b-button>
                        <b-button :href="'/storage/stock_download_sample/' + stock.filename + '.mp3'"
                            :download=" stock.filename +'.mp3'" pill variant="secondary" size="sm">
                            <font-awesome-icon :icon="['fa', 'arrow-down']" />
                        </b-button>
                    </span>
                    <span id="mobile-menu">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                :id="'dropdownMenuButton'+stock.id" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <font-awesome-icon :icon="['fas', 'ellipsis-v']" />
                            </button>
                            <div class="dropdown-menu" :aria-labelledby="'dropdownMenuButton'+stock.id">
                                <b-button class="dropdown-child-button" size="sm">
                                    <font-awesome-icon :icon="['far', 'heart']" />
                                </b-button>
                                <b-button class="dropdown-child-button"
                                    :href="'/storage/stock_download_sample/' + stock.filename + '.mp3'"
                                    :download=" stock.filename +'.mp3'" size="sm">
                                    <font-awesome-icon :icon="['fa', 'arrow-down']" />
                                </b-button>
                            </div>
                        </div>
                    </span>
                </b-col>
            </div>

            <div class="d-flex flex-nowrap bd-highlight">
                <b-col cols="12" class="">
                    <div class="mb-3 text-right">
                        <a v-if="toggleSelected ==stock.id && toggle" v-b-toggle :href="'#example-collapse'+stock.id"
                            @click.prevent @click="toggleClose(stock.id)">
                            <font-awesome-icon class="toggle-link" :icon="['fa', 'angle-up']" />
                        </a>
                        <a v-if=" toggle==false ||toggleSelected !=stock.id  " v-b-toggle
                            :href="'#example-collapse'+stock.id" @click.prevent @click="toggleOpen(stock.id)">
                            <font-awesome-icon class="toggle-link" :icon="['fa', 'angle-down']" />
                        </a>
                    </div>
                    <b-collapse :id="'example-collapse'+stock.id">
                        <div>
                            <font-awesome-icon :icon="['fa', 'file-audio']" />
                             {{stock.samplingrate}}<span v-if="stock.bitDeapth">/</span>{{stock.bitDeapth}} {{stock.fileType}}</div>
                        <div>
                            <font-awesome-icon :icon="['fa', 'file-download']" /> {{stock.fileSize}}</div>
                        <div>
                            <font-awesome-icon :icon="['fas', 'calendar']" /> {{stock.date}}</div>

                        <div class="text-right">
                            created by
                            <img v-if="stock.author_icon" class="userIcon" style="width:40px; height:40px;"
                                :src="'/storage/user_icon/'+stock.author_icon" />
                            <img v-else class="userIcon" style="width:40px; height:40px;"
                                :src="'/storage/default_img/default_icon.jpg'" />
                            {{stock.author_name}}
                        </div>
                    </b-collapse>
                </b-col>
            </div>
            <hr>
        </span>
    </div>
</template>
<script>
    import Header from "../layout/Header";
    import Footer from "../layout/Footer";
    import * as fns from 'date-fns'


    export default {
        components: {
            Header,
            Footer,
        },
        title: "Audio Archive",
        data() {
            return {
                title: "音源",
                stocks: null,
                subgenre: null,
                current_page: null,
                lists: [],
                length: null,
                parPage: null,
                totalStocksPer: null,
                pages: null,
                previous: null,
                next: null,
                keyword: null,
                searchKeyword: null,
                subGenreOptions: [],
                subGenreSelected: {
                    value: null,
                    text: "すべての音源",
                },
                beforePlaying: null,
                waveWidth: null,
                playingNow: null,
                playing: false,
                toggle: false,
                toggleSelected: null,
            }
        },
        mounted() {
            window.addEventListener("popstate", this.handlePopstate);
            let width = window.innerWidth;
            console.log(width);
            if (width < 576) {
                console.log("576より小さい");
                this.waveWidth = 70;
            } else if (width < 767) {
                console.log("767より小さい");
                this.waveWidth = 110;
            } else if (width <= 992) {
                console.log("992より小さい");
                this.waveWidth = 150;
            } else if (992 <= width) {
                console.log("992以上");
                this.waveWidth = 250;
            }

            console.log("横幅は");
            console.log(window.innerWidth);

            this.getSubgenre();
            this.current_page = Number(this.$route.query.page) || 1;
            this.keyword = this.$route.query.key;
            this.subGenreSelected.value = this.$route.query.subgenre;

            if (this.$route.query.subgenre != undefined) {
                this.subgenreSelectedByUrl();
            }
            this.showArchive();




        },
        beforeDestroy() {
            window.removeEventListener("popstate", this.handlePopstate);
        },
        computed: {},
        methods: {
            toggleClose(id) {
                this.toggle = false
                this.toggleSelected = id
                console.log('選ばれたトグルは' + this.toggleSelected)
            },
            toggleOpen(id) {
                console.log(id)
                this.toggle = true
                this.toggleSelected = id
                console.log('選ばれたトグルは' + this.toggleSelected)
            },
            play(stockId) {
                if (this.beforePlaying) {
                    //直近で再生した音源を停止
                    this.beforePlaying.currentTime = 0;
                    this.beforePlaying.pause();
                }

                let audio = document
                    .getElementById("audio" + stockId)
                    .children.item(0)
                    .children.item(0);



                this.playing = true; //オーディオを再生中フラグ
                this.playingNow = stockId; //再生中のオーディオのID

                audio.play();
                this.beforePlaying = audio;
            },
            stop(stockId) {
                let audio = document
                    .getElementById("audio" + stockId)
                    .children.item(0)
                    .children.item(0);
                audio.currentTime = 0; //なくても変わらない
                audio.pause();
                this.playing = false; //オーディオ停止中フラグ
                this.playingNow = null; //再生中のオーディオはない
            },

            selectSubgenre(subGenreOption) {
                this.subGenreSelected = subGenreOption;
            },

            handlePopstate() {
                this.current_page = Number(this.$route.query.page) || 1;
                if (this.$route.query.key != undefined) {
                    this.keyword = this.$route.query.key;
                } else {
                    this.keyword = "";
                }
                if (this.$route.query.subgenre != undefined) {
                    this.subGenreSelected.value = this.$route.query.subgenre;
                    this.subgenreSelectedByUrl();
                } else {
                    this.selectSubgenre({
                        value: null,
                        text: "すべての音源",
                    });
                }
                this.showArchive();
            },

            async showArchive() {
                this.searchKeyword = this.keyword;
                let result = null;
                result = await axios.get("/api/search", {
                    params: {
                        genre: "audio",
                        subgenre: this.subGenreSelected.value,
                        key: this.keyword,
                        page: this.current_page,
                    },
                });
                const stocks = result.data;
                this.stocks = stocks.data;

                console.log('this.stocksの中身は')
                console.log(this.stocks);


                this.parPage = stocks.meta.per_page; //1ページ当たりの表示件数
                this.totalStocksPer = stocks.meta.total; //全部でアイテムが何個あるか
                this.length = stocks.meta.last_page; //総ページ数を取得
                this.makePagenation();

                this.stocks.forEach((stock, index) => {
                    const date = fns.format(new Date(stock.created_at), 'yyyy/MM/dd')
                    this.stocks[index]['date'] = date
                });

                console.log('フォーマットした日付の追加後')
                console.log(this.stocks)

            },

            makePagenation() {
                this.pages = [];
                for (let i = 1; i < this.length + 1; i++) {
                    //ページ番号とリンク先をオブジェクトで追加
                    this.pages.push({
                        no: i,
                    });
                }
                //1個前のページ
                if (this.current_page !== 1) {
                    this.previous = this.current_page - 1;
                } else {
                    this.previous = 1;
                }
                //次のページ
                if (this.current_page !== this.length) {
                    this.next = this.current_page + 1;
                } else {
                    this.next = this.length;
                }
            },
            changePage(number) {
                this.current_page = number; //受け取ったページ番号をthis.current_pageに格納

                let $url = null;

                if (this.subGenreSelected.value && this.keyword) {
                    //console.log('サブジャンルの指定も、キーワードの指定もある')
                    $url =
                        `${window.location.origin}/audio?subgenre=${this.subGenreSelected.value}&key=${this.keyword}&page=${this.current_page}`;
                } else if (this.subGenreSelected.value && !this.keyword) {
                    //console.log('サブジャンルの指定はあるが、キーワードの指定はない')
                    $url =
                        `${window.location.origin}/audio?subgenre=${this.subGenreSelected.value}&page=${this.current_page}`;
                } else if (!this.subGenreSelected.value && !this.keyword) {
                    //console.log('サブジャンルもキーワードも指定がない')
                    $url = `${window.location.origin}/audio?page=${this.current_page}`;
                } else if (!this.subGenreSelected.value) {
                    //console.log('サブジャンルの指定がない')
                    $url = `${window.location.origin}/audio?key=${this.keyword}&page=${this.current_page}`;
                }

                window.history.pushState({
                        number,
                    },
                    `Page${number}`,
                    $url
                );

                this.showArchive();
                this.moveToTop();
            },
            moveToTop() {
                window.scrollTo({
                    top: 0,
                });
            },

            getSubgenre() {
                axios.get("/api/stocks/getSubgenre?genre=audio").then((response) => {
                    let subgenres = response.data;
                    subgenres.filter((subgenre) => {
                        this.subGenreOptions.push({
                            value: subgenre.subgenre,
                            text: subgenre.subgenreText,
                        });
                    });
                }); //サブジャンルの選択肢をデータベースから取得
            },
            subgenreSelectedByUrl() {
                axios
                    .get(
                        "/api/stocks/subgenreSelectedByUrl?subgenre=" +
                        this.$route.query.subgenre
                    )
                    .then((response) => {
                        if (response.data.subgenre != undefined) {
                            this.subGenreSelected.value = response.data.subgenre;
                            this.subGenreSelected.text = response.data.subgenreText;
                        }
                    }); //サブジャンルの選択肢をデータベースから取得
            },

        },
    };

</script>

<style scoped>
    .dropdown-toggle::after {
        display: none !important;
    }

    .search {
        padding: 0.5em;
    }

    .valign-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .canvas-parent {
        position: relative;
        height: 0;
        overflow: hidden;
        max-width: 600px;
        max-height: 340px;
        margin: 10px auto;
    }

    ::v-deep canvas {
        left: 0;
        overflow-x: auto;
    }

    ::v-deep audio {
        display: none;
    }

    .scroll-parent {
        width: 100%;
        position: relative;
    }

    .scroll-child {
        overflow-x: auto;
        white-space: nowrap;
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

    .toggle-link {
        color: #6c757d;
    }

    @media screen and (min-width:992px) {
        #pc-menu {
            display: initial;
        }

        #mobile-menu {
            display: none;
        }
    }

    @media screen and (max-width:992px) {
        #pc-menu {
            display: initial;
        }

        #mobile-menu {
            display: none;
        }
    }

    @media screen and (max-width:767px) {
        #pc-menu {
            display: none;
        }

        #mobile-menu {
            display: initial;
        }
    }

    @media screen and (max-width:576px) {
        #pc-menu {
            display: none;
        }

        #mobile-menu {
            display: initial;
        }
    }

    .dropdown-menu {
        min-width: initial;
    }

    .dropdown-child-button {
        color: #5a6268;
        background-color: initial;
        border: none;
    }

</style>
