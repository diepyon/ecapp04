import Vue from 'vue'
import Router from 'vue-router'
import Home from './components/page/Home'
import About from './components/page/About.vue'
import StockCreate from './components/page/StockCreate.vue'
import StockArchive from './components/page/StockArchive.vue'
import Images from './components/page/Images.vue'
import Videos from './components/page/Videos.vue'
import Audios from './components/page/Audios.vue'
import StockSingle from './components/page/StockSingle.vue'
import Login from './components/page/Login.vue'
import NotFound from './components/page/NotFound.vue'
import Register from './components/page/Register.vue'
import Account from './components/page/Account.vue'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    routes: [{
            path: '*',
            name: NotFound,
            component: NotFound
        },
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/about',
            name: 'about',
            component: About,
        },
        {
            path: '/stocks/create',
            name: 'stockcreate',
            component: StockCreate,
            
        },
        {
            path: '/stocks',
            name: 'stockarchive',
            component: StockArchive
        },
        {
            path: '/image',
            name: 'images',
            component: Images
        },
        {
            path: '/video',
            name: 'videos',
            component: Videos
        },
        {
            path: '/audio',
            name: 'audios',
            component: Audios
        },        
        {
            path: '/stocks/:id(\\d+)',
            name: 'stocksingle',
            component: StockSingle,
            props: route => ({
                id: Number(route.params.id)
            })
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '/account',
            name: 'account',
            component: Account,
        },
    ]
})
router.beforeEach((to, from, next) => {
  router['referrer'] = from;
  next();
})

export default router;
