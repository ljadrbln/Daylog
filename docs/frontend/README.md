# Frontend Tooling

Daylog frontend uses a modern TypeScript-based toolchain for consistent code quality and developer experience.

## Configuration
| Tool | Purpose | Config file |
|------|----------|--------------|
| **ESLint (Flat config)** | Enforces code style and catches errors. | `frontend/eslint.config.js` |
| **Prettier** | Formats code automatically. | `.prettierrc` |
| **EditorConfig** | Keeps indentation and line endings consistent across editors. | `.editorconfig` |
| **Vitest** | Runs unit tests in a jsdom browser-like environment. | `frontend/vitest.config.ts` |
| **Husky** | Runs quality checks before every commit. | `frontend/.husky/pre-commit` |

## Workflow

1. **Automatic setup**
   ```bash
   cd frontend
   npm install
   ```
   Husky is activated automatically via the `prepare` script.

2. **Pre-commit hook**
   Every commit runs these steps in order:
   ```bash
   npm run format   # Prettier — auto-format
   npm run lint:fix # ESLint — fix minor issues
   npm run test     # Vitest — ensure all tests pass
   ```

3. **Manual commands**
   ```bash
   npm run format      # Format all files
   npm run lint        # Check lint errors
   npm run lint:fix    # Fix lint issues
   npm run test        # Run unit tests
   npm run dev         # Start Vite dev server
   npm run build       # Production build
   ```

4. **Testing**
   Vitest uses a jsdom environment (`window`, `document` available).  
   Run tests from anywhere:
   ```bash
   cd frontend
   npm test
   ```

## Notes

- `.editorconfig` ensures uniform indentation (4 spaces) and LF line endings.  
- ESLint integrates with Prettier for conflict-free formatting.  
- All paths (`@src`, `@tests`) are resolved via `vite-tsconfig-paths`.
