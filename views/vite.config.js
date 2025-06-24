import { defineConfig } from "vite";
import path from "path";
import { svelte } from "@sveltejs/vite-plugin-svelte";

// https://vitejs.dev/config/
export default defineConfig({
  build: {
    minify: true,
    emptyOutDir: true,
    chunkSizeWarningLimit: 1600,
    manifest: true,
    rollupOptions: {
      // overwrite default .html entry
      input: "/src/main.js",
      // output: {
      //     entryFileNames: `assets/[name].js`,
      //     chunkFileNames: `assets/[name].js`,
      //     assetFileNames: `assets/[name].[ext]`
      // }
    },
    outDir: "../dist",
  },
  plugins: [
    svelte({
      onwarn: (warning, handler) => {
        if (warning.message.includes("'invoize' is not defined")) return;
        handler(warning);
      },
    }),
  ],
  resolve: {
    alias: {
      $lib: path.resolve("./src/lib"),
      $utils: path.resolve("./src/utils"),
    },
  },
});
