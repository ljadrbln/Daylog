import {defineConfig} from 'vitest/config';
import tsconfigPaths from 'vite-tsconfig-paths';

export default defineConfig({
    plugins: [tsconfigPaths()],
    test: {
        environment: 'jsdom',
        passWithNoTests: true,
        globals: true,
        include: ['tests/**/*.test.ts'],
        reporters: ['default'],
        testTimeout: 5000
    }
});
