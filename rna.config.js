const isProduction = process.env.NODE_ENV === 'production';

/**
 * @type {import('@chialab/rna-config-loader').Config}
 */
const config = {
    entrypoints: [],
    clean: true,
    format: 'esm',
    sourcemap: !isProduction,
    entryNames: isProduction ? '[name]-[hash]' : '[name]',
    chunkNames: isProduction ? '[name]-[hash]' : '[name]',
    assetNames: isProduction ? '[name]-[hash]' : '[name]',
    jsxFactory: 'h',
    jsxFragment: 'Fragment',
    jsxModule: '@chialab/dna',
    minify: isProduction,
    bundle: true,
};

export default config;
