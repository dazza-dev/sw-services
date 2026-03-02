=== SW - Services CPT ===
Contributors: seniorswp, dazzadev
Tags: custom-post-type, services, rest-api, graphql
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom Post Type for Services with native custom fields.

== Description ==

Adds a Services Custom Post Type with:

* Native custom fields (icon class, display order)
* Service categories taxonomy
* REST API support (built-in)
* WPGraphQL support (optional)

== Frequently Asked Questions ==

= Do I need WPGraphQL? =

No. The plugin works with WordPress REST API by default. WPGraphQL support is optional.

= How do I access services via API? =

REST API: `/wp-json/wp/v2/service`
GraphQL: `swServices` query (requires WPGraphQL plugin)

= How do I add custom fields? =

Custom fields appear in the "Service Details" meta box when editing a service.

== Changelog ==

= 1.0.0 =
* Initial release
