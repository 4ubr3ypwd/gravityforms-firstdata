=== Gravity Forms + First Data Global Gateway e4 ===
Contributors: aubreypwd, excion
Donate link: http://excion.co?ref=wp_org_donate_gffd
Tags: gravity forms, addon, first data, global gateway, global gateway e4, payments, credit card
Requires at least: 3.7
Tested up to: 3.9
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gravity Forms Addon for the First Data Global Gateway e4

== Description ==

**Gravity Forms + First Data Global Gateway e4** is a plugin and addon for
Gravity Forms that will allow you to process products using the
[First Data Global Gateway e4](https://www.firstdata.com/en_us/products/merchants/ecommerce/online-payment-processing.html) service.

= Settings =

Once installed you should setup your installation to work with your
First Data Global Gateway e4 account by visiting
**Forms > Settings > First Data Global Gateway**.

From there you can link your install with your account. There is a
test mode for running test transactions with a FirstData demo account.

= Feeds Setup =

In order to tell Gravity Forms + First Data Global Gateway e4 to process
a certain form, you first have to setup a *feed*. You can do this
by visiting **Forms > First Data Global Gateway**, and from here you can
activate individual forms to work with FirstData.

You will need to tell Gravity Forms + First Data Global Gateway e4
what fields, from the form, will supply FirstData with the information it
needs to complete the purchase.

Once you have done so, test your forms to ensure it is processing payment.

= Development =

Development for this plugins happens over at [Github](https://github.com/excion/gravityforms-firstdata)
and our Issue Tracker is on [Trello](https://trello.com/b/iaFkdo91).

== Installation ==

Install by uploading the .zip, or by searching for this plugin,
from WordPress Dashboard > Plugins > Add New.

== Screenshots ==

1. Settings
2. Feed Setup

== Changelog ==

= 1.1 =

- Fixed issues where various PHP Warning notices were being thrown
- Various bug fixes
- Reference and Customer Reference Numbers are now recorded for cross-referencing

= 1.0.2 =

- Fixes to possible __FILE__ conflict with other plugins
- Release to WP.org
