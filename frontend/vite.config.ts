import {defineConfig} from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

import path from 'node:path';
import {fileURLToPath} from 'node:url';
import {dirname} from 'node:path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    root: path.resolve(__dirname),
    base: '/assets/',
    plugins: [tsconfigPaths()],
    build: {
        outDir: path.resolve(__dirname, '../public/assets'),

        emptyOutDir: false,
        rollupOptions: {
            input: {
                'entries-list-vanilla': path.resolve(
                    __dirname,
                    'src/Presentation/entries/vanilla/list.vanilla.entry.ts'
                ),

                'dl-components': path.resolve(__dirname, 'ui/scss/dl-components.scss')
            },
            output: {
                assetFileNames: (asset) => {
                    if (asset.name?.endsWith('.css')) {
                        return 'css/[name][extname]';
                    }

                    if (asset.name?.endsWith('.woff2')) {
                        return 'fonts/[name][extname]';
                    }

                    return 'assets/[name][extname]';
                },
                entryFileNames: 'js/[name].js'
            }
        }
    }
});
