<template>
    <div>
        <h1>Home</h1>
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
            axios.get('/api/hoge')
        },
        methods: {

        },
        computed: {

        }

    }

</script>
