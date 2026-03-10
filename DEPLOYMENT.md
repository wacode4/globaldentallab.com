# Global Dental Lab Deployment Notes

## Purpose

This document records the deployment workflow, cache behavior, and verification steps for this project so future updates can be shipped and checked quickly.

## Stack Summary

- Static HTML site
- Shared CSS: `css/shared-styles.css`
- Shared JS: `js/header-hero.js`
- Contact/subscription backend in `functions/api/`
- Git-based deployment flow

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

### 4. The test server does not execute `functions/api/`

What happened:

- static pages deployed correctly to `https://tt.globaldentallab.com/`
- direct requests to `/api/contact` and `/api/admin/*` returned `404 Not Found`

Root cause:

- the current SSH deploy target is a static web root
- it is useful for validating HTML, CSS, JS, and asset references
- it does not run the Cloudflare Pages Functions in `functions/api/`

Implication:

- form and admin UI code can be shipped with the static site
- runtime backend behavior still requires a separate Functions-capable environment to verify

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
- `curl -L https://tt.globaldentallab.com/ | sed -n '1,120p'`
- confirm the expected title, copy, and asset version strings

Browser verification:

- use an incognito window or hard refresh
- test at least:
  - `/`
  - `/services.html`
  - `/send-a-case.html`
  - `/downloads.html`

## Current Known Good Deploy Commits

- `9fed7fd` `Rebuild site structure for migrated lab content`
- `296feb1` `Add cache-busting version to shared assets`

## Maintenance Guidance

- Treat `PROJECT_NOTES.md` as design/content context.
- Treat this file as deployment/operations workflow for the repo.
- Keep future deployment pitfalls added here as they happen.
