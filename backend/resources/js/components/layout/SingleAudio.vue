<template>
    <div v-if="stock">
        <b-container class="bv-example-row">
            <b-row>
                <b-col sm="8">
                    <div class="" style="width:100%; display: flex;justify-content: center;align-items: center;">
                        <div style="magin-top:0;">
                            <b-button v-if="playing" style="margin-top:0;" @click="stop">
                                <font-awesome-icon :icon="['fa', 'stop']" />
                            </b-button>
                            <b-button v-else style="margin-top:0" @click="play">
                                <font-awesome-icon :icon="['fa', 'play']" />
                            </b-button>
                        </div>
                        <div style="width:100%; margin:0 0 0 .5em ;">
                            <wavesurfer :src="file" :options="options" id="wavesurfer" ref="surf"></wavesurfer>
                        </div>
                    </div>
                </b-col>
                <b-col sm="4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-if="stock.fileInfo">
                            <font-awesome-icon :icon="['fa', 'file-audio']" />
                            {{stock.samplingrate}}<span v-if="stock.bitDeapth">/</span>{{stock.bitDeapth}}
                            {{stock.fileType}}
                        </li>
                        <li class="list-group-item" v-if="stock.fileInfo">
                            <font-awesome-icon :icon="['far', 'clock']" /> {{stock.duration}}</li>
                        <li class="list-group-item">
                            <font-awesome-icon :icon="['fas', 'file']" /> {{stock.fileType}}</li>

                        <li class="list-group-item" v-if="stock.fileInfo">
                            <font-awesome-icon :icon="['fa', 'file-download']" />
                            {{stock.fileSize}}</li>
                        <li class="list-group-item">
                            <font-awesome-icon :icon="['fa', 'yen-sign']" /> {{stock.fee}}</li>
                    </ul>
                    <button class="btn btn-outline-secondary">
                        <font-awesome-icon :icon="['far', 'heart']" />????????????????????????</button>
                    <a class="btn btn-outline-secondary" :href="'/storage/stock_download_sample/'+stock.filename+'.mp3'"
                        role="button" download="">
                        <font-awesome-icon :icon="['fas', 'arrow-down']" />??????????????????????????????</a>
                    <button class="btn btn-warning cart_button btn-lg btn-primary btn-lg btn-block">
                        <font-awesome-icon :icon="['fas', 'cart-arrow-down']" />??????????????????</button>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import WaveSurferVue from "wavesurfer.js-vue";
    export default {
        name: 'SingleAudio',
        props: ['stock'],
        components: {
            WaveSurferVue,
        },
        data() {
            return {
                playing: false,
                options: {
                    height: 90
                },
                file: "/storage/stock_sample/" + this.stock.filename + ".mp3",
            }
        },
        mounted() {
            console.log(this.stock.info)
            console.log(this.file)
        },
        methods: {
            play: async function () {
                console.log(this.$refs.surf.waveSurfer)
                this.$refs.surf.waveSurfer.play() //???????????????
                this.playing = this.$refs.surf.waveSurfer.isPlaying()

                let result = await this.finish()
                console.log('play?????????????????????')

                //?????? 
                //?????????????????????????????????
                // this.currentTime = this.$refs.surf.waveSurfer.getCurrentTime()
                // this.duration = this.$refs.surf.waveSurfer.getDuration()


                //??????????????????????????????????????????????????????
                //??????????????????????????????????????????????????????????????????????????????????????????
                //????????????????????????????????????????????????stop????????????????????????

            },
            finish: async function (playing) {
                console.log('finish????????????????????????????????????' + playing)
                var hage = this.$refs.surf.waveSurfer.on('finish', await
                    function () {
                        console.log('owata')
                    });
                return 'finish'
            },
            stop() {
                this.$refs.surf.waveSurfer.stop()
                this.playing = this.$refs.surf.waveSurfer.isPlaying()
                return 'owata'
            },


            //?????????????????????????????????????????????
            // getAudioDuration() {
            //     var audio = new Audio(); // audio?????????
            //     audio.src = '/storage/stock_download_sample/' + this.stock.filename + '.mp3'
            //     audio.load(); // audio???????????????

            //     audio.addEventListener('loadedmetadata', function (e) {
            //         console.log(audio.duration); // ??????????????????
            //     });
            // }
        },
        watch: {
            'playing'(newVal, oldVal) {
                // ??????????????????????????????????????????????????????????????????????????????????????????
                console.log(oldVal, '->', newVal)
                if (newVal === true) {
                    console.log('???????????????')
                } else if (newVal === false) {
                    console.log('????????????????????????')
                }
            },
        },
        computed: {
            player() {
                return this.$refs.surf.waveSurfer
            }
        }
    };

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

    .wrapper {
        display: -webkit-flex;
        display: -moz-flex;
        display: -ms-flex;
        display: -o-flex;
        display: flex;
    }

</style>
