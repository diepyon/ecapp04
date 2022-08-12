<template>
    <div>
        <Header />
        <div class="container">
            <router-view />
        </div>
        <Footer />
    </div>
</template>

<script>
    import Header from '../layout/Header'
    import Footer from '../layout/Footer'
    import SingleImage from '../layout/SingleImage'

    export default {
        data() {
            return {
            }
        },

        components: {
            Header,
            Footer,
            SingleImage
        },
        mounted() {
            //ログインページに強制リダイレクトされてけど気が変わって
            //別のメニューをクリックしたらログインフォームのリダイレクト先をデフォルト値に戻したい
            //うまくいかない
            //this.$store.commit("resetState") //vuexに保存されているメッセージをリセット
        },
        methods: {

        }
    }

</script>
<style scoped>
    header {
        margin-bottom: .5em;
    }

    footer {
        margin-top: .5em;
    }
    /* cssを親に集約するならここに書く */
    /* ::v-deep .aaa {
        color:red;
    } */

</style>
