import {defineConfig} from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

import path from 'node:path';
import {fileURLToPath} from 'node:url';
import {dirname} from 'node:path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

/**
 * Resolve project-relative paths for Rollup/Vite inputs.
 *
 * Purpose:
 * Provide a short, explicit helper to keep rollup input configuration readable
 * while preserving a single source of truth for __dirname-based resolution.
 *
 * @param relativePath string Project-relative path from repository root.
 * @return string Absolute filesystem path.
 */
function r(relativePath: string): string {
    const resolvedPath = path.resolve(__dirname, relativePath);

    return resolvedPath;
}

export default defineConfig({
    root: path.resolve(__dirname),
    base: '/assets/',
    plugins: [tsconfigPaths()],
    build: {
        outDir: path.resolve(__dirname, '../public/assets'),

        emptyOutDir: false,
        rollupOptions: {
            input: {
                'entries-list-vanilla': r(
                    'src/Presentation/entries/vanilla/list/list.vanilla.entry.ts'
                ),

                'entry-view-vanilla': r(
                    'src/Presentation/entries/vanilla/view/view.vanilla.entry.ts'
                ),

                'entry-edit-vanilla': r(
                    'src/Presentation/entries/vanilla/edit/edit.vanilla.entry.ts'
                ),

                'entry-add-vanilla': r('src/Presentation/entries/vanilla/add/add.vanilla.entry.ts'),
                'dl-components': r('ui/scss/dl-components.scss')
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
