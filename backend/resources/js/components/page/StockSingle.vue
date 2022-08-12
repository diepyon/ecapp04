<template>
    <div>
         <!-- <div v-if="stock&& stock.stasut=='publish'"></div> -->
        <div v-if="stock">
            <h1>ID.{{ id }}詳細個別ページです</h1>

            <SingleImage v-if="stock && stock.genre == 'image'" v-bind:stock="stock" />
            <SingleVideo v-else-if="stock && stock.genre == 'video'" v-bind:stock="stock" />
            <SingleAudio v-else-if="stock && stock.genre == 'audio'" v-bind:stock="stock" />

            <span class="">
                <p>名前：{{stock.name}}</p>
                <p>金額：{{stock.fee}}</p>
                <p>ジャンル：{{stock.genre}}</p>
                <p>詳細：{{stock.detail}}</p>
                <p>投稿日:{{date}}</p>
            </span>
            <div>created by {{authorName}}</div>
        </div>
        <div v-else>
            <h1>statusがpublich以外なら隠せばいい</h1>
        </div>
    </div>
</template>

<script>
    import Header from "../layout/Header";
    import Footer from "../layout/Footer";
    import SingleImage from '../layout/SingleImage'
    import SingleVideo from '../layout/SingleVideo'
    import SingleAudio from '../layout/SingleAudio'
    import * as fns from 'date-fns'


    export default {
        props: {
            id: Number,
        },
        components: {
            Header,
            Footer,
            SingleImage,
            SingleVideo,
            SingleAudio
        },
        //title: 'タイトルどうしよう'
        data() {
            return {
                stock: null,
                date: null,
                author_id: null,
                authorName: null,
                hoge: null,
            }
        },
        methods: {

        },
        mounted() {
            axios.get('/api/stocks/' + this.id)
                .then(response => {
                    this.stock = response.data.data
                    this.date = fns.format(new Date(this.stock.created_at), 'yyyy/MM/dd')

                    console.log('親')
                    console.log(response.data.data)

   
                    //入れ子にしてもいいのか、めんどくさいけどasyncawait使うべき？
                    //→メソッド化したらええやん
                    axios.get('/api/hoge/' + this.stock.author_id)
                        .then(response => {
                            this.authorName = response.data.name //投稿者名
                        })
                    //console.log(this.stock.author_id) //投稿者IDはUsercontroller経由しなくても取れる
                })
        },
    }

</script>
<style scoped>
    ::v-deep .btn {
        margin-top: .5em
    }

    ::v-deep li.list-group-item>svg {
        margin-right: 0.2em;
    }

    ::v-deep .thumbnail {
        width: 100%;
    }

</style>
