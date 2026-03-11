# Global Dental Lab Deployment Notes

## Purpose

This document records the deployment workflow, cache behavior, and verification steps for this project so future updates can be shipped and checked quickly.

For current CMS migration status and architecture handoff, also read `CMS_HANDOFF.md`.

## Stack Summary

- Legacy static HTML pages still exist in the repo.
- Active test architecture is now PHP + MySQL CMS driven by `site.php`.
- Dynamic public routes use `/en/<slug>` style URLs.
- Shared CSS: `css/shared-styles.css`
- Some legacy front-end behavior still uses `js/cms-runtime.js`.
- Git-based deployment flow with server-side `git pull`

## Runtime Config Notes

- `ADMIN_KEY` should be provided by the Functions-capable runtime instead of relying on the built-in fallback.
- A non-secret example is kept in `.dev.vars.example`.
- Do not commit real runtime secrets into this repo.

## Main Deployment Flow

1. Make local changes.
2. Commit only the files that should go live.
3. Push to `origin main`.
4. SSH to the server and run `git pull` in the test site directory.
5. Verify the live HTML response, not just the browser view.

If `cms/schema.sql` changes, re-apply the schema before verification so new CMS tables are available.

## Current Shared Asset Cache Busting

To reduce Cloudflare and browser cache issues, all shared CSS and JS references now use explicit version query strings:

- `css/shared-styles.css?v=20260309-2`
- `js/header-hero.js?v=20260309-2`

### Rule

Whenever `css/shared-styles.css` or `js/header-hero.js` changes:

1. Update the version string in every HTML file that references them.
2. Commit and deploy that version bump together with the asset change.

### Why this matters

Without versioned asset URLs:

- the HTML may update
- Cloudflare or the browser may still serve stale CSS/JS
- the page can look partially updated or broken

## Pitfalls Already Hit

### 1. Server was updated, but browser still looked old

What happened:

- Git deployment succeeded
- test server pulled the new commit
- browser still appeared to show the old version

Root cause:

- cached CSS/JS or cached browser state made the site look stale

Fix:

- add cache-busting version params to shared CSS/JS
- verify page HTML directly with `curl`

### 2. Deploying to the server is not the same as verifying the public domain

What happened:

- server-side `git pull` succeeded
- uncertainty remained because the public test domain view did not immediately match expectations

Fix:

- verify the public domain with `curl -L`
- check returned HTML for expected new title, copy, or asset version strings

### 4. The old `functions/api/` path is not the active backend on the test server

What happened:

- static pages deployed correctly to `https://tt.globaldentallab.com/`
- direct requests to `/api/contact` and `/api/admin/*` returned `404 Not Found`

Root cause:

- the current SSH deploy target is a static web root
- it is useful for validating HTML, CSS, JS, and asset references
- it does not run the Cloudflare Pages Functions in `functions/api/`

Implication:

- `functions/api/` is no longer the preferred runtime path for current CMS work
- current contact/admin/content management behavior on test now runs through PHP endpoints under `/cms/`
- do not spend time debugging Cloudflare Functions for the current server-managed CMS path unless production requirements change

Rule:

- use `tt.globaldentallab.com` to verify page markup and front-end wiring
- do not treat `404` on `/api/*` there as proof the API code is broken
- do not treat static-site success there as proof the API is deployed

### 3. `git pull` timing can race the push

What happened:

- `git pull` ran before `git push` fully completed
- server temporarily reported `Already up to date`

Fix:

- if push and pull are done back-to-back, rerun `git pull` after push completes

## Recommended Release Checklist

Before deploy:

- confirm `git status --short`
- avoid committing unrelated local notes unless intended
- confirm shared asset version bump if CSS/JS changed

Deploy:

- `git push origin main`
- SSH to server
- `git pull`

Verify:

- `curl -I https://tt.globaldentallab.com/`
- `curl -L https://tt.globaldentallab.com/en/services | sed -n '1,160p'`
- `curl -L https://tt.globaldentallab.com/en/ceramics | sed -n '1,200p'`
- `curl -L https://tt.globaldentallab.com/en/emax | sed -n '1,200p'`
- `curl -L https://tt.globaldentallab.com/en/downloads | sed -n '1,200p'`
- `curl -L https://tt.globaldentallab.com/en/materials | sed -n '1,200p'`
- `curl -L https://tt.globaldentallab.com/en/certificates | sed -n '1,200p'`
- `curl -L https://tt.globaldentallab.com/en/lab-tour | sed -n '1,200p'`
- confirm the expected title, copy, redirects, or menu links

Browser verification:

- use an incognito window or hard refresh
- test at least:
  - `/`
  - `/services.html`
  - `/downloads.html`
  - `/materials.html`
  - `/certificates.html`
  - `/lab-tour.html`
  - `/send-a-case.html`

## Current Known Good Deploy Commits

- `336d378` `Migrate resource and trust pages into CMS`
- `818af62` `Build multilingual page and module architecture`
- `f351529` `Localize dynamic module links by language`
- `c93e598` `Add translatable site settings and seed product pages`
- `7ba8237` `Add CMS menu management and legacy redirects`

### 2026-03-11 verification note

- `336d378` was deployed to `tt.globaldentallab.com`
- schema was re-applied
- seed script was re-run successfully
- verified `200` on:
  - `/en/downloads`
  - `/en/materials`
  - `/en/certificates`
  - `/en/lab-tour`
- verified `302` on:
  - `/downloads.html`
  - `/materials.html`
  - `/certificates.html`
  - `/lab-tour.html`

## Maintenance Guidance

- Treat `PROJECT_NOTES.md` as design/content context.
- Treat this file as deployment/operations workflow for the repo.
- Keep future deployment pitfalls added here as they happen.
