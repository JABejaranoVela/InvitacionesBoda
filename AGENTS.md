# Repository Guidelines

## Project Structure & Module Organization
- `src/pages/` contains route-based `.astro` pages (e.g. `src/pages/index.astro`, `src/pages/templates/index.astro`).
- `src/components/` holds reusable UI pieces (hero, cards, navbar, footer).
- `src/layouts/` defines shared layouts (e.g. `src/layouts/Layout.astro`).
- `src/data/` stores local content like `src/data/templates.ts`.
- `src/styles/` contains global Tailwind + custom CSS (`src/styles/global.css`).
- `public/` is for static assets served as-is.

## Build, Test, and Development Commands
- `npm install` installs dependencies.
- `npm run dev` starts the Astro dev server (default `localhost:4321`).
- `npm run build` builds the production site into `dist/`.
- `npm run preview` serves the production build locally.
- `npm run astro -- ...` runs Astro CLI tools (e.g. `npm run astro -- check`).

## Coding Style & Naming Conventions
- Use 2-space indentation in `.astro`, `.ts`, and CSS files.
- Component files use PascalCase (e.g. `TemplateCard.astro`).
- Route folders are lowercase and match URLs (e.g. `src/pages/templates/`).
- Tailwind utility classes are preferred; custom CSS lives in `src/styles/global.css`.
- Keep strings and content in Spanish unless otherwise requested.

## Testing Guidelines
- No testing framework is configured yet. If adding tests, document the chosen tool and add a script in `package.json` (e.g. `npm test`).
- Prefer colocating UI tests near components or using a `tests/` directory.

## Commit & Pull Request Guidelines
- Git history uses Conventional Commits (e.g. `feat: ...`, `chore: ...`). Keep new commits aligned with that format.
- PRs should include a short summary, screenshots for UI changes, and any relevant context or decisions.
- Link related issues when applicable.

## Configuration & Assets
- Tailwind is configured via the Astro Vite integration; global styles are in `src/styles/global.css`.
- Remote images are currently sourced from Unsplash; replace with local assets in `public/` when final content is ready.

## Docs / Skills (consultar según tarea)
- Estilo UI y consistencia visual: @doc/frontend-style.md
- Guías de estética/diseño (look & feel): @doc/frontend-aesthetics-guidelines.md
- Registro de skills y decisiones: @doc/skills-used.md

Regla:
- Antes de cambios visuales (UI, CSS, Tailwind, componentes en `src/components/`, layouts o páginas), consulta
  @doc/frontend-style.md y @doc/frontend-aesthetics-guidelines.md y aplica sus reglas.
- Si hay conflicto, prioridad:
  1) AGENTS.md
  2) @doc/frontend-style.md
  3) @doc/frontend-aesthetics-guidelines.md