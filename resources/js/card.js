Nova.booting((app, store) => {
    app.component(
        'most-visited-pages',
        require('./components/MostVisitedPages').default
    );
    app.component(
        'referrer-list',
        require('./components/ReferrerList').default
    );
});
