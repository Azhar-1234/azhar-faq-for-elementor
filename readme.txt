=== Azhar FAQ for Elementor ===
Contributors: azhar64100
Tags: elementor, faq, accordion, woocommerce, product-faq
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

FAQ accordion widget for Elementor, with a separate FAQ for every WooCommerce product.

== Description ==

A lightweight FAQ accordion widget for Elementor — and, since 1.1.0, a way to give **every WooCommerce product its own set of questions**.

**A separate FAQ for each product**

Editing a product? A new **FAQ** tab appears right inside the WooCommerce Product data box, next to General and Inventory. Add as many question/answer pairs as you want for that one product, drag them into the order you like, and hit Update. Nothing to configure anywhere else.

On the product page the widget looks up the product being viewed and shows that product's questions. A product with no FAQ of its own keeps showing whatever the widget was already set to, so adding this to an existing site changes nothing until you actually fill in a product.

**Everything else**

* FAQ accordion widget for Elementor, styled through normal Elementor controls — colors, typography, borders.
* Choose where the questions come from: the product being viewed, the widget itself, or product-first with the widget as a fallback.
* `[azhar_product_faq]` shortcode for themes and product templates that are not built with Elementor.
* One click installs and activates Elementor if it is missing.
* The product FAQ tab only needs WooCommerce. It keeps working, and your saved questions stay safe, even if Elementor is deactivated.

== Installation ==

1. Upload the `azhar-faq-for-elementor` folder to `/wp-content/plugins/`, or install it from Plugins > Add New.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. If Elementor is not installed yet, click **Install & Activate Elementor** in the notice the plugin shows.

**Adding an FAQ to a single product**

1. Go to **Products** and edit any product.
2. In the **Product data** box, open the new **FAQ** tab.
3. Click **+ Add FAQ**, then fill in the Question and Answer. Repeat for as many questions as that product needs.
4. Drag the rows by the handle to reorder them, or use the trash icon to delete one.
5. Click **Update**. Those questions now belong to that product only.

**Showing it on the product page**

* Built with Elementor: drop the **Azhar FAQ for Elementor** widget into your single product template. Its FAQ Source is set to product-first out of the box, so it picks up each product's questions automatically.
* Not built with Elementor: put the `[azhar_product_faq]` shortcode wherever you want the FAQ to appear.

== Frequently Asked Questions ==

= Do I have to add an FAQ to every product? =

No. Products you leave empty are untouched — the FAQ section on those pages stays exactly as it was.

= Do I need one widget per product? =

No. One widget in your single product template is enough. It reads the FAQ of whichever product is being viewed.

= Can I still use one fixed FAQ list for the whole site? =

Yes. Set the widget's **FAQ Source** to "Items below only" and it ignores product FAQs completely.

= Does the product FAQ need Elementor? =

Only for the widget. The FAQ tab on the product itself needs WooCommerce, nothing else, and your saved questions survive if Elementor is turned off.

= Where is the FAQ data stored? =

In the product's own post meta (`_azhafafo_product_faqs`). No extra database tables.

= Can I use HTML in an answer? =

Yes, the same HTML WordPress allows in a post. Line breaks are turned into paragraphs automatically.

== Changelog ==

= 1.1.0 =
* New: **FAQ** tab in the WooCommerce Product data box — add, reorder and remove FAQ items per product.
* New: the Elementor widget shows the viewed product's FAQ automatically, falling back to its own items when a product has none.
* New: **FAQ Source** control on the widget — product FAQ, widget items, or product-first.
* New: `[azhar_product_faq]` shortcode for non-Elementor product templates.
* New: one click install and activate for Elementor when it is missing.
* Improvement: the product FAQ tab now loads with WooCommerce alone, independently of Elementor.

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
Adds a per product FAQ tab to WooCommerce. Existing widgets keep working unchanged until you add FAQ items to a product. https://plugins.svn.wordpress.org/azhar-faq-for-elementor/tags/1.1.0/readme.txt
