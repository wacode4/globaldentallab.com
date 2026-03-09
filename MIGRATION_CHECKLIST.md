# Global Dental Lab Migration Checklist

## Goal

Rebuild the content from `https://www.dental-lab-china.com/` inside this project while keeping:

- the `Global Dental Lab` brand name
- the current visual design direction
- the current static HTML + shared CSS/JS architecture

This is a content and structure migration, not a design clone.

## Source Scope

Primary source sections confirmed from the source site:

- Home
- Products
  - NEW CAD VENEERS
  - ALL-CERAMICS
  - Implant Products
  - Implant Surgical Guide
  - PFM / Snap-On Smile
  - Clear Aligners
  - Removables Denture
  - Orthodontics Products
  - Clinical cases of Prosthodorntics
- Materials
- About Us
- Certificates
- Lab Tour
- Downloads
- Send A Case / Send A Digital Case
- News

## Current Project Constraints

- Static HTML pages, not CMS-driven
- Shared header/hero logic in `js/header-hero.js`
- Shared presentation styles in `css/shared-styles.css`
- Existing core pages:
  - `index.html`
  - `about.html`
  - `services.html`
  - `technology.html`
  - `contact.html`
  - `category-ceramics.html`
  - `product-zirconia-ultra.html`

## Migration Strategy

1. Keep the current design system and page shell.
2. Rewrite navigation and page hierarchy to match the stronger source-site information architecture.
3. Copy approved images, certificates, and downloadable assets from the source site into this project.
4. Rewrite or clean source copy where needed so the final English reads as one coherent brand voice.
5. Launch in English first. Multi-language can be added after the core English content is complete.

## Phase 1: Asset Intake

### 1.1 Copy source assets into the project

- Create `images/source-home/` for homepage banners and section images.
- Create `images/source-products/` for product/category imagery.
- Create `images/source-certificates/` for FDA, CE, ISO and related certificates.
- Create `images/source-lab-tour/` for factory, team, equipment, workflow photos.
- Create `images/source-materials/` for material/vendor logos and charts.
- Create `downloads/` for RX forms, price lists, turnaround guides, catalogs, schedules, and preparation guides.

### 1.2 Normalize filenames

- Rename copied files to descriptive ASCII names.
- Remove spaces and inconsistent punctuation from filenames.
- Keep a simple naming convention such as:
  - `home-banner-01.jpg`
  - `certificate-fda-01.jpg`
  - `lab-tour-production-01.jpg`
  - `rx-form-pfm.pdf`

### 1.3 Build a source asset ledger

- Track original source URL
- New local filename
- Destination page(s)
- Whether asset is ready for production

Suggested format: spreadsheet or markdown table.

## Phase 2: Navigation and Information Architecture

### 2.1 Update main navigation in `js/header-hero.js`

- Replace the current simplified nav with:
  - Home
  - Products
  - Materials
  - About
  - Send a Case
  - Downloads
  - Contact
- Add product submenu items:
  - CAD Veneers
  - All-Ceramics
  - Implant Products
  - Implant Surgical Guide
  - PFM / Snap-On Smile
  - Clear Aligners
  - Removables
  - Orthodontics
- Add About submenu items:
  - About Us
  - Certificates
  - Lab Tour

### 2.2 Footer restructuring

- Add quick links for:
  - Downloads
  - Send a Case
  - Lab Tour
  - Certificates
  - Materials
- Add both Shenzhen and Hong Kong addresses.
- Add shipping information block.
- Add direct contact CTA for WhatsApp / phone / email.

## Phase 3: Page-by-Page Migration

### 3.1 Home page

Target file: `index.html`

Bring in:

- source homepage hero messaging
- product category overview
- outsourcing value proposition
- Why Choose Us section
- testimonial section
- dual-location contact block
- download/send-case CTAs

Execution checklist:

- Replace current hero copy with stronger outsourcing-focused messaging.
- Keep current hero design, but swap in source banners if they fit quality requirements.
- Expand product grid to include at least:
  - CAD Veneers
  - All-Ceramics
  - Implant Products
  - Implant Surgical Guide
  - PFM / Snap-On Smile
  - Clear Aligners
  - Removables
  - Orthodontics
- Add a dedicated "Why Global Dental Lab" section using:
  - certified quality
  - digital workflow
  - customer care
  - cost-effective outsourcing
- Add client testimonials from the source site.
- Add stronger trust stats only after validating the numbers you want to publish.
- Add CTA strip linking to:
  - `send-a-case.html`
  - `downloads.html`
  - `contact.html`

Acceptance criteria:

- Homepage clearly sells outsourced dental lab services.
- Homepage links users into every major service area and the main conversion path.

### 3.2 About page

Target file: `about.html`

Bring in:

- company background
- founding year
- technician/team scale
- fixed + removable capabilities
- mission / vision / service promise
- leadership story if you want to keep Dr. Yang content

Execution checklist:

- Replace current "10+ years / 50k+ / 500+" placeholders with approved business numbers.
- Add structured sections:
  - company overview
  - production capabilities
  - quality commitment
  - customer service promise
  - leadership / management
- Add certificate summary with links to `certificates.html`.
- Add lab tour teaser linking to `lab-tour.html`.

Acceptance criteria:

- About page reads like the company behind the expanded catalog, not a smaller boutique studio only.

### 3.3 Services overview page

Target file: `services.html`

Bring in:

- full product taxonomy from the source site
- short descriptions per category
- clear path to detail pages

Execution checklist:

- Expand sticky service nav to include all source categories.
- Replace current limited sections with:
  - Crown & Bridge / PFM
  - All-Ceramics
  - CAD Veneers
  - Implant Products
  - Implant Surgical Guide
  - Clear Aligners
  - Removables
  - Orthodontics
  - Clinical Cases
- Keep section layout style consistent with the current site.
- Add "send case" CTA inside each category section.

Acceptance criteria:

- One overview page gives a complete scan of the lab's full service line.

### 3.4 Technology page

Target file: `technology.html`

Bring in:

- digital workflow positioning from the source site
- supported digital case platforms
- scanner/platform compatibility
- implant guide workflow visuals
- 3D printing / CAD-CAM / milling / sintering capabilities

Execution checklist:

- Keep the current digital workflow section.
- Add a "Supported Digital Platforms" section with:
  - TRIOS / 3Shape
  - Medit
  - iTero
  - Carestream
  - Dentsply Sirona
  - Shining3D
  - Dropbox / OneDrive / WeTransfer / email fallback
- Add a guided workflow section for digital case submission.
- Add implant surgical guide workflow visuals if the images are strong enough.

Acceptance criteria:

- A dentist can understand both production technology and digital submission options without leaving this page.

### 3.5 Contact page

Target file: `contact.html`

Bring in:

- stronger conversion copy
- shipping addresses
- direct case-submission pathways
- 24-hour hotline style quick actions if desired

Execution checklist:

- Keep the current form and backend integration.
- Add a "Ship a Physical Case" block with full shipping address.
- Add a "Send a Digital Case" block linking to `send-a-case.html`.
- Add downloadable RX forms links.
- Update service dropdown to include the expanded product list.

Acceptance criteria:

- Contact page supports both general inquiries and actual case intake.

## Phase 4: New Pages To Add

### 4.1 Certificates page

New file: `certificates.html`

Content:

- FDA certificate gallery
- CE certificate gallery
- ISO 13485 certificate gallery
- any material/manufacturer certificates you want to surface

Execution checklist:

- Build image grid with modal/lightbox or simple linked full-size images.
- Add short intro explaining compliance and traceability.
- Add CTA to contact or send a case.

### 4.2 Lab tour page

New file: `lab-tour.html`

Content:

- production floor
- CAD design stations
- milling / printing / finishing areas
- QC and packing process
- team / office / shipping areas

Execution checklist:

- Use a gallery or masonry layout.
- Organize images by department or workflow stage.
- Add short captions where useful.

### 4.3 Downloads page

New file: `downloads.html`

Content:

- catalog PDFs
- ceramic restoration chart / preparation guide
- RX forms
- full price list
- turnaround time
- holiday schedule
- shipping label / shipping instructions

Execution checklist:

- Build clean downloadable resource cards.
- Group downloads into:
  - forms
  - catalogs
  - pricing
  - shipping
  - schedules
- Ensure every button points to a local file in `downloads/`.

### 4.4 Send a case page

New file: `send-a-case.html`

Content:

- digital case submission platforms
- platform account identifiers
- instructions by scanner/platform
- file requirements
- fallback send methods
- physical case shipping details

Execution checklist:

- Split page into:
  - digital case submission
  - supported scanners/platforms
  - account details
  - file requirements
  - physical shipping steps
- Include the clear aligner data requirements if aligners stay in scope.
- Add direct CTA buttons for email, WhatsApp, and contact form.

### 4.5 Materials page

New file: `materials.html`

Content:

- material/vendor logos and approved brands
- zirconia / ceramic / implant / attachment materials
- partner compatibility references

Execution checklist:

- Build a logo grid or category-based material list.
- If the source material page is mostly logos, keep this page compact and trust-oriented.

### 4.6 News page

New file: `news.html`

Content:

- only if you want to maintain posts

Execution checklist:

- Do not migrate low-value syndicated or duplicated articles as-is.
- Either:
  - launch with 3 to 5 cleaned articles, or
  - omit from nav until content exists

Recommendation:

- treat News as phase 2, not launch-critical.

## Phase 5: Product / Category Detail Pages

### 5.1 Convert current ceramic category page into a real product-category template

Target file: `category-ceramics.html`

Execution checklist:

- Replace placeholder entries with real local product detail targets.
- Add more complete category intro from source material.
- Add subtypes:
  - all ceramic restorations
  - e.max
  - digital ceramics
  - veneer smile design

### 5.2 Add category detail pages for missing major categories

New files recommended:

- `category-cad-veneers.html`
- `category-implants.html`
- `category-implant-guides.html`
- `category-pfm.html`
- `category-clear-aligners.html`
- `category-removables.html`
- `category-orthodontics.html`

### 5.3 Add product detail pages only where they add sales value

Use product detail pages for:

- ultra translucent zirconia
- e.max
- layered zirconia
- monolithic zirconia
- custom abutments
- implant bars
- overdentures
- snap-on smile

Recommendation:

- Do not create dozens of thin pages with only a title and one image.
- Build fewer, stronger pages first.

## Phase 6: Content Cleanup

### 6.1 Rewrite for one brand voice

- Replace every remaining `Bright Dental Lab` mention with `Global Dental Lab`.
- Standardize tone across all pages.
- Fix grammar and awkward phrasing from source copy.
- Standardize terminology:
  - all-ceramics
  - removables
  - implant guides
  - digital workflow
  - turnaround time

### 6.2 Standardize business data

Decide one canonical set for:

- company description
- founding year
- team size
- certifications
- Shenzhen address
- Hong Kong address
- phone numbers
- email addresses
- WhatsApp number

### 6.3 CTA consistency

Use the same 3 primary conversion actions sitewide:

- Send a Case
- Contact Us
- Download RX Forms

## Phase 7: SEO and Structured Data

### 7.1 Page metadata

For every migrated page:

- unique `<title>`
- unique meta description
- canonical URL
- Open Graph title/description/image

### 7.2 Structured data

Update or add:

- `DentalLaboratory`
- `AboutPage`
- `ContactPage`
- `CollectionPage`
- `Product`

### 7.3 Internal linking

- Link category pages from home and services.
- Link all detail pages back to category and services.
- Link certificates, downloads, and send-a-case from footer and conversion sections.

### 7.4 Sitemap and robots

- update `sitemap.xml`
- verify `robots.txt`

## Phase 8: Functional Checks

### 8.1 Forms and backend

- Keep current contact form endpoint working.
- Test `functions/api/contact.js`.
- Confirm admin inquiries still receive complete service/category data.

### 8.2 Download links

- Verify every PDF/file opens correctly.
- Verify file names are production-safe.

### 8.3 Mobile and navigation

- Confirm expanded nav works on mobile.
- Confirm sticky services navigation still behaves correctly.
- Confirm page sections are not hidden behind the fixed header.

## Phase 9: Launch Readiness

### Must-have before launch

- Homepage updated
- About updated
- Services updated
- Technology updated
- Contact updated
- Certificates page live
- Lab tour page live
- Downloads page live
- Send a case page live
- Main nav and footer updated
- All business details standardized
- Metadata updated
- Broken-link pass complete

### Can wait until phase 2

- News/blog migration
- full materials expansion
- full multi-language rollout
- large library of product-detail pages

## Recommended Build Order

1. Copy source assets and downloads into local folders.
2. Update nav/footer shell in `js/header-hero.js`.
3. Build `downloads.html` and `send-a-case.html` first because they are conversion-critical.
4. Expand `services.html`.
5. Rework `index.html`.
6. Rework `about.html`.
7. Rework `technology.html`.
8. Rework `contact.html`.
9. Add `certificates.html` and `lab-tour.html`.
10. Add category pages for missing service groups.
11. Finish metadata, sitemap, QA, launch pass.

## Suggested Immediate Sprint

If doing this in the next implementation round, start with these exact deliverables:

- navigation update
- homepage rewrite
- services page expansion
- downloads page creation
- send-a-case page creation
- certificates page creation

That set gets the site from "brand brochure" to "usable outsourcing lab website" fastest.
