//import Page1 from './components/Page1'
import Page2 from './components/Page2'
export const routes = [
        {
            path: '/',
            name: 'welcome',
            component: require('./components/Page1.vue').default,
            props: { title: "This is the SPA home" }
        },
        {
            path: '/edit',
            name: 'page',
            component: Page2,
            props: {
                title: "This is the SPA Second Page",
                author : {
                    name : "Fisayo Afolayan",
                    role : "Software Engineer",
                    code : "Always keep it clean"
                }
            }
        },
    ];
