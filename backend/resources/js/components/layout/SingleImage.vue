<template>
    <div v-if="stock">
        <b-container class="bv-example-row">
            <b-row>
                <b-col sm="8">
                    <img class="thumbnail" :src="'/storage/stock_sample/'+stock.filename+'.png'">
                </b-col>

                <b-col sm="4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-if="stock.fileInfo"> {{stock.fileInfo.width}}x{{stock.fileInfo.height}}px</li>
                        <li class="list-group-item">{{stock.fileType}}</li>
                        <li class="list-group-item"> {{stock.size}}</li>
                        <li class="list-group-item"  v-if="stock.fileInfo"> {{stock.fileInfo.aspect}}</li>
                        <li class="list-group-item"> ￥{{stock.fee}}</li>
                    </ul>

                    <button class="btn btn-outline-secondary">
                        <font-awesome-icon :icon="['far', 'heart']" />お気に入りに保存</button>
                    <a class="btn btn-outline-secondary" :href="'/storage/stock_download_sample/'+stock.filename+'.png'"
                        role="button" download="">
                        <font-awesome-icon :icon="['fas', 'arrow-down']" />サンプルダウンロード</a>
                        
                    <button class="btn btn-warning cart_button btn-lg btn-primary btn-lg btn-block">
                        <font-awesome-icon :icon="['fas', 'cart-arrow-down']" />カートに追加</button>
                </b-col>
            </b-row>
        </b-container>

    </div>
</template>

<script>
    export default {
        name: 'SingleImage',
        props: ['stock'],

        data() {
            return {

            }
        },
        mounted() {
            console.log('子')
            console.log(this.stock)
        },
        methods: {

        },
    };

</script>

<style scoped>

</style>
