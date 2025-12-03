import {defineConfig} from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';
import path from 'path';

export default defineConfig({
    plugins: [tsconfigPaths()],

    build: {
        outDir: path.resolve(__dirname, '../public/assets'),
        emptyOutDir: true,

        rollupOptions: {
            input: {
                'entries-list-vanilla': path.resolve(
                    __dirname,
                    'src/Presentation/entries/vanilla/list.vanilla.entry.ts'
                )
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: 'assets/[name][extname]'
            }
        }
    }
});
