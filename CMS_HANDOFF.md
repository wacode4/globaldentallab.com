# Global Dental Lab CMS Handoff

## Purpose

This file records the current PHP + MySQL CMS architecture, what has already been migrated, and what remains so future work can continue without re-discovery.

## Current Live Test Environment

- Public test domain: `https://tt.globaldentallab.com/`
- Test server checkout: `/var/www/html/globaldentallab.com/test`
- Production has not been switched to this CMS yet.

## Current Architecture

- PHP front controller: `site.php`
- Language-aware public routes:
  - `/en/`
  - `/en/<slug>`
  - `/fr/<slug>`
  - `/de/<slug>`
- URL rewrites and legacy redirects: `.htaccess`
- CMS bootstrap: `cms/bootstrap.php`
- Core content/query layer: `cms/core/content.php`
- Shared helpers and settings: `cms/core/helpers.php`
- Public layout: `cms/views/layouts/site.php`
- Reusable modules live in `cms/views/modules/`

## Database Model In Use

The test database is `gdls`.

Main CMS tables already in use:

- `languages`
- `pages`
- `page_translations`
- `modules`
- `module_translations`
- `page_modules`
- `menus`
- `menu_items`
- `cms_settings`
- `site_setting_translations`
- `inquiries`
- `admin_users`

Schema source of truth:

- `cms/schema.sql`

Seed source of truth:

- `cms/scripts/seed_dynamic_pages.php`

## What Is Already Migrated

Dynamic public pages currently seeded and live:

- `home`
- `contact`
- `about`
- `technology`
- `services`
- `ceramics`
- `zirconia-ultra`
- `emax`
- `layered-zirconia`
- `monolithic-zirconia`
- `veneers`
- `inlays-onlays`

Current module library includes:

- `hero`
- `rich_text`
- `stats_grid`
- `card_grid`
- `feature_list`
- `media_split`
- `contact_panel`
- `cta_banner`

CMS editing screens already available:

- `cms/pages.php`
- `cms/page-edit.php`
- `cms/modules.php`
- `cms/module-edit.php`
- `cms/menus.php`
- `cms/menu-edit.php`
- `cms/settings.php`
- `cms/inquiries.php`

## Menu System Status

Menu management is now database-backed.

Seeded menus:

- `primary`
- `footer`

Public header/footer navigation now reads from menu tables instead of directly deriving from `pages.show_in_nav`.

## Settings System Status

Global site settings are stored in `cms_settings`.

Language overrides are stored in `site_setting_translations`.

Settings currently used by the dynamic layout include:

- site name
- footer brand blurb
- phone
- email
- WhatsApp
- Hong Kong address
- Shenzhen address

The contact panel module can fall back to these site settings so repeated contact details do not need to be maintained in multiple places.

## Legacy URL Redirects Already Added

These old static URLs now redirect to dynamic routes:

- `about.html`
- `technology.html`
- `services.html`
- `contact.html`
- `category-ceramics.html`
- `product-zirconia-ultra.html`
- `product-emax.html`
- `product-layered.html`
- `product-monolithic.html`
- `product-veneers.html`
- `product-inlays.html`

Current redirect target language is `/en/...`.

## Deployment Workflow

Normal test deployment flow:

1. Commit only intended files.
2. `git push origin main`
3. `ssh root@74.207.245.85`
4. `cd /var/www/html/globaldentallab.com/test`
5. `git pull`
6. If schema or seed changed:
   - `mysql -u <user> -p<pass> gdls < cms/schema.sql`
   - `/usr/bin/php cms/scripts/seed_dynamic_pages.php`
7. Verify with `curl`.

Useful verification targets:

- `/en/`
- `/en/services`
- `/en/ceramics`
- `/en/emax`
- `/cms/pages.php`
- `/cms/modules.php`
- `/cms/menus.php`
- `/cms/settings.php`

## Known Constraints

- Production is still separate and has not been cut over.
- Legacy static files still exist in the repo; migrated ones are redirected at the web server layer.
- Menu custom labels are not yet language-specific; page-linked items localize through page translations.
- Static root `/` is still not force-redirected to `/en/`.
- The repo intentionally does not store runtime secrets or admin passwords.
- `PROJECT_NOTES.md` has user-owned edits and should not be modified casually.

## Best Next Steps

Highest-value next tasks:

- Add language-specific custom labels for menu items.
- Migrate remaining category/support pages such as materials, downloads, certificates, and lab-tour into the same page/module system.
- Decide whether `/` should eventually redirect to `/en/` on the test site.
- Plan the production cutover only after enough static routes are covered by dynamic replacements.

## Practical Rule For Future Work

When adding any new public page:

1. Create or seed the `pages` row.
2. Add translations.
3. Create reusable modules instead of hard-coding page HTML.
4. Assign modules through `page_modules`.
5. Add or update menu entries if the page should be navigable.
6. Add a legacy redirect if an old static URL already exists.
