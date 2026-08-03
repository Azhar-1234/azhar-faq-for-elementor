# Release Notes — 1.1.0

This repository already includes Version: 1.1.0 in the plugin header and changelog in readme.txt. I've extracted the changelog below and added it here for easy reference and for use in a GitHub Release.

## Changelog

= 1.1.0 =
* New: **FAQ** tab in the WooCommerce Product data box — add, reorder and remove FAQ items per product.
* New: the Elementor widget shows the viewed product's FAQ automatically, falling back to its own items when a product has none.
* New: **FAQ Source** control on the widget — product FAQ, widget items, or product-first.
* New: `[azhar_product_faq]` shortcode for non-Elementor product templates.
* New: one click install and activate for Elementor when it is missing.
* Improvement: the product FAQ tab now loads with WooCommerce alone, independently of Elementor.

= 1.0.0 =
* Initial release

## Upgrade Notice

= 1.1.0 =
Adds a per product FAQ tab to WooCommerce. Existing widgets keep working unchanged until you add FAQ items to a product.

---

What I changed in the repository:
- Added `RELEASE_NOTES.md` with the changelog and upgrade notice for 1.1.0 so you can use it as release notes when creating a Git tag/release.

Next steps for you (or I can guide/do if you provide additional permissions):
1) Create and push an annotated tag (recommended) and push it to GitHub. Example commands:

   git checkout main
   git pull origin main
   git tag -a 1.1.0 -m "Release 1.1.0 — per-product FAQ, shortcode, and improvements"
   git push origin 1.1.0

2) Create a GitHub Release (optional but recommended). Example with GitHub CLI:

   gh auth login
   gh release create 1.1.0 --title "1.1.0" --notes-file RELEASE_NOTES.md

3) Add WordPress.org SVN credentials to GitHub Actions secrets so the deploy workflow can push to WordPress.org:
   - Settings → Secrets and variables → Actions → New repository secret
   - Add `SVN_USERNAME` (your WordPress.org username)
   - Add `SVN_PASSWORD` (an application password or your WordPress.org password — app password preferred)

After you push the tag, the existing workflow `.github/workflows/deploy.yml` is configured to trigger on any tag push and will run the 10up deploy action to publish to the plugin SVN. If you want, I can also create a GitHub Release draft for you (I cannot push the tag without credentials), or walk you through any step.
