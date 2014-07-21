[Issue Tracker on Trello](https://trello.com/b/iaFkdo91) | [Plugin on
WordPress.org](http://wordpress.org/plugins/gravity-forms-first-data-global-gateway-addon)

_________________________

**Gravity Forms + First Data Global Gateway e4** is a plugin and addon for 
Gravity Forms that will allow you to process products using the 
[First Data Global Gateway e4](https://www.firstdata.com/en_us/products/merchants/ecommerce/online-payment-processing.html) service.

You can use this plugin and get more information from the [WordPress.org](http://wordpress.org/plugins/gravity-forms-first-data-global-gateway-addon).

## Issue Tracker

We use [Trello](https://trello.com/b/iaFkdo91) to track issues, you can sign up and participate at [https://trello.com/b/iaFkdo91](https://trello.com/b/iaFkdo91).

## How to Debug on Form Submit

To get debug information when a form is submitted, just use 
(in `wp-config.php`):

	define('GFFD_DEBUG_FORM_SUBMIT', true);

This will stop Gravity Forms from submitting the entry and express a
`var_dump` and `exit()`.
