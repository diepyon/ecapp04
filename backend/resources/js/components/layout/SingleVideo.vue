<template>
    <div v-if="stock">
        <b-container class="bv-example-row">
            <b-row>
                <b-col sm="8">
                    <video class="thumbnail" loop autoplay muted :src="'/storage/stock_sample/'+stock.filename+'.mp4'"
                        controls></video>
                </b-col>

                <b-col sm="4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <font-awesome-icon :icon="['fas', 'expand-alt']" />
                            {{stock.mediaInfo.width}}x{{stock.mediaInfo.height}}px</li>
                        <li class="list-group-item">
                            <font-awesome-icon :icon="['far', 'clock']" />{{stock.mediaInfo.time}}</li>
                        <li class="list-group-item"><font-awesome-icon :icon="['fas', 'file']" />{{stock.fileType}}</li>
                        <li class="list-group-item"> {{stock.size}}</li>
                        <li class="list-group-item"> {{stock.mediaInfo.aspect}}</li>
                        <li class="list-group-item"> ￥{{stock.fee}}</li>
                    </ul>

                    <button class="btn btn-outline-secondary">
                        <font-awesome-icon :icon="['far', 'heart']" />お気に入りに保存</button>
                    <a class="btn btn-outline-secondary" :href="'/storage/stock_download_sample/'+stock.filename+'.mp4'"
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
        name: 'SingleVideo',
        props: ['stock'],

        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {

        },
    };

</script>

<style scoped>

</style>
