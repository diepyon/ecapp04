<template>
    <div>
        <h1>{{title}}</h1>
        <div class="stocks">
            <div class="" v-for="stock in stocks" :key="stock.id">
                <div class="stock_thumbnail">
                    <router-link :to="`stocks/` + stock.id">
                        <img @error="checkImgExist(stock.id)" id="stock.id" class="thumbnail"
                            v-if="stock.genre=='image'" :src="'/storage/stock_thumbnail/'+stock.filename+'.png'">
                        <img @error="checkImgExist(stock.id)" :id="stock.id" class="thumbnail"
                            v-else-if="stock.genre=='video'" :src="'/storage/stock_thumbnail/'+stock.filename+'.png'">
                        <img :id="stock.id" class="thumbnail" v-else-if="stock.genre=='audio'"
                            :src="'/storage/stock_thumbnail/audiothumbnail.png'">
                    </router-link>
                    <div class="genre_icon">
                        <span v-show="stock.genre=='image'">
                            <font-awesome-icon :icon="['fas', 'image']" /></span>
                        <span v-show="stock.genre=='video'">
                            <font-awesome-icon :icon="['fas', 'video']" /></span>
                        <span v-show="stock.genre=='audio'">
                            <font-awesome-icon :icon="['fas', 'music']" /></span>
                    </div>
                </div>
            </div>
        </div>
        <Pagination @stocksFromChild="getStocksFromChild" :genre="''" :keyword="''" />
    </div>
</template>

<script>
    import Header from "../layout/Header";
    import Footer from "../layout/Footer";
    import Pagination from "../layout/Pagination";
    

    export default {
        components: {
            Header,
            Footer,
            Pagination,
        },
        data() {
            return {
                title: 'Archive',
                stocks: null,
            }
        },
        mounted() {
            this.genre = this.$route.query.genre
            this.keyword = this.$route.query.key
        },
        methods: {
            getStocksFromChild(value) {
                //ページネーションコンポーネントから一覧すべきレコードを取得
                this.stocks = value
            },
            checkImgExist(id) { //サムネイル画像がエラーになるときは代替え画像に置き換え
                const img = document.getElementById(id);
                img.setAttribute('src', '/storage/default_img/notfound.jpg');
            },
           
        }
    }

</script>
<style scoped>
    .b-col img {
        max-width: 100%;
    }

    .genre_icon {
        color: #adb5bd99;
        ;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.5rem;
        cursor: pointer;
        position: absolute;
        bottom: 0%;
        left: 0%;
        margin: 8px;
    }

    .stock_thumbnail {
        position: relative;
    }

    /*サムネイルの左下に出るジャンル判別アイコン*/
    .stock_thumbnail:hover img {
        filter: brightness(50%);
        -webkit-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        transition: 0.1s ease-in-out;
    }

    .stocks {
        display: flex;
        flex-wrap: wrap;
        /* 親要素を無視して画面いっぱいに表示 */
        margin-right: calc(50% - 50vw);
        margin-left: calc(50% - 50vw);
    }



    /*レスポンシブデザイン*/
    @media screen and (min-width:769px) {

        /*** この中にPCのスタイル（769px以上） ***/
        .thumbnail {
            flex-grow: 1;
            object-fit: cover;
            max-height: 200px;
            max-width: 400px;
            margin: 0.2rem;
            border-radius: 4px;
        }
    }

    @media screen and (max-width:768px) {
        /*** この中にタブレットのスタイル（768px以下） ***/
        .thumbnail {
            flex-grow: 1;
            object-fit: cover;
            max-height: 150px;
            max-width: 300px;
            margin: 0.2rem;
            border-radius: 4px;
        }
    }

    @media screen and (max-width:599px) {

        /*** この中にスマホのスタイル（599px以下） ***/
        .thumbnail {
            flex-grow: 1;
            object-fit: cover;
            max-height: 100px;
            max-width: 200px;
            margin: 0.2rem;
            border-radius: 4px;
        }
    }

</style>
