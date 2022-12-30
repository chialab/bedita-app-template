import process from 'node:process';

const isProduction = process.env.NODE_ENV === 'production';

/**
 * @type {import('@chialab/rna-config-loader').Config}
 */
const config = {
    entrypoints: [
        {
            input: [
                './resources/scripts/index.ts',
                './resources/styles/index.css',
            ],
            publicPath: '/build/',
            output: 'webroot/build/',
            manifestPath: 'webroot/build/manifest.json',
            entrypointsPath: 'webroot/build/entrypoints.json',
        },
    ],
    clean: true,
    sourcemap: !isProduction,
    entryNames: isProduction ? '[name]-[hash]' : '[name]',
    chunkNames: isProduction ? '[name]-[hash]' : '[name]',
    assetNames: isProduction ? '[name]-[hash]' : '[name]',
    minify: isProduction,
    bundle: isProduction,
};

export default config;
