# Mailboxes

Add and remove email accounts on a server. Compatible with WP3.5 multisite.

**Context** Plugin should only load for logged in wordpress users who are currently viewing the custom post type 'toolbox' or are in wp-admin.

## WP-admin Settings

Add settings to NETWORK ADMIN SETTINGS tab in wp-admin. Settings section called 'mailboxes'. Add settings for:

* Server API - currently the only option will be cPanel
* Authentication - Whatever is needed to authenticate with cPanel API

## Compatible server API's

*cPanel

### Add a new account

* Creates new email account in cPanel.
 
* Appends the name of the new account to an array called `tb_settings_mailboxes` using wp function `set_theme_mod`. `tb_settings_mailboxes` will contain an array of all the mailboxes created for a given wp site.

**Method** `mailbox->add` 

**Properties:** 

`account_name` (req'd) Name of the email account to add.

'fowards_to' (optional) If filled in create an email forwarder instead of a standard email account.

Returns: true or error array

### Remove an account

* Delete an email account in cPanel
* Remove account name from `tb_settings_mailboxes`

**Method** `mailbox->delete' 

**Properties:** 

'account_name' (req'd) Name of existing account to delete.

Returns: true or error array

## Resources

An older plugin that tackles this same issue. http://wordpress.org/extend/plugins/cpanel-operations/installation/

A cPanel API plugin. No email account handling, but ex. of cPanel Api integration. http://wordpress.org/extend/plugins/cpanel-manager-from-worpit/

