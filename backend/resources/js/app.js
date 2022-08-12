import Vue from "vue";
import router from "./router";
import App from "./components/page/App"

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

require('./bootstrap');

import WaveSurferVue from "wavesurfer.js-vue";
Vue.use(WaveSurferVue);

import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas, far, fab)
Vue.component('font-awesome-icon', FontAwesomeIcon)


import titleMixin from './modules/title.js'    
Vue.mixin(titleMixin)


window.Vue = require('vue').default;

Vue.component('example-component', require('./components/ExampleComponent.vue').default);


import AudioVisual from 'vue-audio-visual'
Vue.use(AudioVisual)


import store from "./store";

const app = new Vue({
    el: '#app',
    render: h => h(App),
    store,
    router,
    components: {
        App
    },    
});


