#WHMCS OpenSRS Hosted Email Provisioning Module

This provides the ability to allow customers to purchase mailboxes and forwards and manage them as well as aliases.  By entering your administrator settings for OpenSRS's hosted email you can provide access to your customers for all necessary management functions for creating and managing email accounts.

The following functions are supported in the admin area:

- Create Domain
- Suspend Domain
- Delete Domain

The following functions are supported in the customer area:

- View Mailboxes/Forwards/Aliases
- Add Mailbox
- Delete Mailbox
- Change Mailbox Settings
- Add Forwarding Mailbox
- Delete Forwarding Mailbox
- Add Mailbox Alias
- Delete Mailbox Alias
- View Workgroups
- Add Workgroup
- Delete Workgroup

##Installation

1. Move the folder into the corresponding folder within your WHMCS installation (/modules/servers)
2. Create a new product and choose Opensrsemail as the module name.
3. Set your main amin username, domain and password for your OpenSRS email service and choose the cluster that has been assigned to you by OpenSRS.
4. Create a new configurable options group.
5. Add a new option called Mailboxes of the type quantity and create an option called Mailboxes and set your pricing per mailbox.
6. Add a new option called Forwards of the type quantity and create an option called Forwards and set your pricing per forward.
7. Assign that configurable option group to the product created in step 2.

At this point your customers will be able to purchase the service and choose the number of mailboxes and forwards they would like to have.  They will also be able to adjust those quantities from the product management page.  They will be able to manage their mailboxes based on the quantities they have selected and only add mailboxes or forwards when they have avialility to otherwise they will be required to delete them.

##Common Problems

- Communication error, please contact the administrator.
  - Can be caused by port 51000 not being open.  Make sure that port is in your firewall in order to connect to OpenSRS
