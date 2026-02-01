const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Répertoire de sortie
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    
    // Point d'entrée principal
    .addEntry('app', './assets/app.js')
    
    // Activer le splitChunks pour optimiser
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    
    // Nettoyage du répertoire de sortie
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    
    // Activer React (JSX)
    .enableReactPreset()
    
    // Activer PostCSS pour TailwindCSS
    .enablePostCssLoader()
    
    // Configuration Babel
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    
    // Alias pour les imports
    .addAliases({
        '@': __dirname + '/assets/react',
        '@components': __dirname + '/assets/react/components',
        '@pages': __dirname + '/assets/react/pages',
        '@hooks': __dirname + '/assets/react/hooks',
        '@api': __dirname + '/assets/react/api',
        '@contexts': __dirname + '/assets/react/contexts',
        '@utils': __dirname + '/assets/react/utils',
    })
    
    // Configuration du dev server
    .configureDevServerOptions(options => {
        options.allowedHosts = 'all';
        options.https = false;
        options.hot = true;
    })
;

module.exports = Encore.getWebpackConfig();
