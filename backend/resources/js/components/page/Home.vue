<template>
    <div>
        <h1>Home</h1>
        <av-waveform id="audio" audio-src="http://192.168.47.22/storage/stock_sample/1a32ca02.mp3" :playtime="false"
           playtime-line-color="blue" noplayed-line-color="red" playtime-slider-color="green" radius="4"></av-waveform>
        <button @click="play">再生</button>
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
                title: 'Home',
            }
        },
        mounted() {

        },
        methods: {
            play() {
                let audio = document.getElementById("audio").children.item(0).children.item(0)
                audio.play();
            }
        },
        computed: {

        }

    }

</script>
