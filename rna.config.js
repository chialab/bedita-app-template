const isProduction = process.env.NODE_ENV === 'production';

/**
 * @type {import('@chialab/rna-config-loader').Config}
 */
const config = {
    entrypoints: [],
    clean: true,
    sourcemap: !isProduction,
    entryNames: isProduction ? '[name]-[hash]' : '[name]',
    chunkNames: isProduction ? '[name]-[hash]' : '[name]',
    assetNames: isProduction ? '[name]-[hash]' : '[name]',
    minify: isProduction,
    bundle: isProduction,
};

export default config;
