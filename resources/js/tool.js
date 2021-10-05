Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-google-analytics',
            path: '/nova-google-analytics',
            component: require('./components/Tool'),
        },
        {
            name: 'nova-google-analytics-page',
            path: '/nova-google-analytics/page',
            component: require('./components/Page'),
            props: route => ({ url: route.query.url })
        },
    ])
})
