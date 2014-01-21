**Gravity Forms + First Data Global Gateway e4℠** is a plugin and addon for 
Gravity Forms that will allow you to process products using the 
First Data Global Gateway e4℠ API.

# Install

To install, download a `.zip` file from the Downloads area to use, 
upload to WordPress, and activate the plugin.

## Setup

Once installed you should setup your installation to work with your
First Data Global Gateway e4℠ account by visiting 
**Forms > Settings > First Data Global Gateway**. 

From there you can link your install with your account. There is a 
test mode for running test transactions with a FirstData demo account.

## Setting Up Feeds

In order to tell Gravity Forms + First Data Global Gateway e4℠ to process
a certain form, you first have to setup a *feed*. You can do this
by visiting **Forms > First Data Global Gateway**, and from here you can 
activate individual forms to work with FirstData.

You will need to tell Gravity Forms + First Data Global Gateway e4℠ 
what fields, from the form, will supply FirstData with the information it
needs to complete the purchase.

Once you have done so, test your forms to ensure it is processing payment.

# Troubleshooting

## How to Debug on Form Submit

To get debug information when a form is submitted, just use 
(in `wp-config.php`):

	define('GFFD_DEBUG_FORM_SUBMIT', true);

This will stop Gravity Forms from submitting the entry and express a
`var_dump` and `exit()`.