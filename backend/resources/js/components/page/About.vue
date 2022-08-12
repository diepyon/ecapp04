<template>
    <div>
        <h1>{{title}}</h1>
        <p>いかした素材を販売するぜ</p>
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
        title: 'About',

        data(){
            return{
                title: 'About',
                }
        },
        mounted() {
        },
        methods: {
        },
    }
</script>
