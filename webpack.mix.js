const mix = require("laravel-mix");
const path = require("path");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .webpackConfig({
    stats: {
      children: true,
    },
    resolve: {
      extensions: [".ts", ".vue"],
      alias: {
        "@": path.resolve(__dirname, "./resources/js"),
      },
    },
    module: {
      rules: [
        {
          test: /\.ts$/,
          loader: "ts-loader",
          exclude: /node_modules/,
          options: { appendTsSuffixTo: [/\.vue$/] },
        },
      ],
    },
  })
  .vue({ version: 3 })
  .ts("resources/js/app.ts", "public/js")
  .sass("resources/sass/app.scss", "public/css");
