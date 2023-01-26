
const development = {
    mode: 'development',
    devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader',
                    {
                        loader: 'css-loader',
                        options: {
                            import: true,
                            sourceMap: true,
                        }
                    },
                ],
            },
            {
                test: /\.(png|jpe?g|gif)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'image/[name][ext]',
                }
            },
        ]
    },
}

module.exports = development;
