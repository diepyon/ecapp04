<template>
    <div>
        <!-- <div v-if="stock&& stock.stasut=='publish'"></div> -->
        <div v-if="stock">
            <SingleImage v-if="stock && stock.genre == 'image'" v-bind:stock="stock" />
            <SingleVideo v-else-if="stock && stock.genre == 'video'" v-bind:stock="stock" />
            <SingleAudio v-else-if="stock && stock.genre == 'audio'" v-bind:stock="stock" />

            <span class="">
                <!-- <p>名前：{{stock.name}}</p> -->
                <p>詳細：{{stock.detail}}</p>
            </span>
            <!-- <div>created by {{authorName}}</div> -->
        </div>
        <div v-else>
            <h1>statusがpublich以外なら隠せばいい</h1>
        </div>


        <span v-if="stock">
            <h1>{{stock.name}}</h1>
            <b-row>
                <b-col sm="4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            {{stock.subGenre}}
                        </li>
                        <li class="list-group-item">
                            <font-awesome-icon :icon="['fas', 'calendar']" /> {{date}}
                        </li>
                    </ul>
                </b-col>
                <b-col sm="4">
                    <span v-for="tag in stock.tags" :key="tag.id">
                        <a v-if="tag[0]" href="#" class="badge   badge-secondary">
                            {{ tag }}
                        </a>
                    </span>
                </b-col>
                <b-col sm="4">
                    <div class="" >
                        created by
                        <img v-if="stock.author_icon" class="userIcon" style="width:40px; height:40px;" :src="'/storage/user_icon/'+stock.author_icon" />
                        <img v-else class="userIcon" style="width:40px; height:40px;" :src="'/storage/default_img/default_icon.jpg'" />
                        {{stock.author_name}}
                    </div>

                </b-col>
            </b-row>
        </span>

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
                stock: null, //子コンポーネントに渡すための変数

                date: null,
                author_id: null,
                authorName: null,

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
                            //this.authorName = response.data.name //投稿者名
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
        margin-bottom: .7em;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }    
</style>
