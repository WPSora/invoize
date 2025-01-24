## June 11, 2024 
### v0.0.1
- Initial development release version.
- Fix to support php v7.4.
- Add checklist option to send email on create recurring page.
- Add more jsdocs do some svelte component.
- Fix hide Paypal & Xendit payment link on downloaded file.
- Paypal & Xendit payment for recurring now created correctly.
- Fix Xendit missing primary currency for payment.
- Improve print invoice.
- Fix reminder & recurring cron not running initially.
- Remove unused migration file.
- UI fix and improvement in some component.
- Fix unable to edit and update bank payment.
- Fix business logo not uploading if beyond 2MB limit size.
- Fix product search UI
- Fix email styling
- Fix due date format
- Receipt is now displaying for one-time and recurring invoice.
- Invoice & Recurring input now get reset after leaving page.
- Update dashboard content positioning.

## June 15, 2024
### v0.0.2
- Remove ability to edit invoice on Cancelled or Trashed invoice
- Fix invoice list not updated correctly after invoice status update
- Fix Business settings for not updating data and logo correctly
- Remove resetInputHelper.js and place it in the component instead
- Compiler now remove previous build files
- Fix unable to add custom address to customer from search result in Invoice & Recurring Page

## June 19, 2024
### v0.0.3
- Add notification in create invoice and recurring if other action is fail or success
- Add Regenerate invoice feature
- Add paypal key check and update error handler
- Add error_log for sendMail invoice if fail and create invoice if fail in Recurring
- Add  Paypal & Xendit payment check on Recurring whether will be valid or not
- Improve API & Update model
- Add retry mechanism for payment Paypal & Xendit if failed
- Bugfix new customer get saved without id
- Update InvoiceMigrationModel
- Add Payments model and use it in InvoiceAPI
- Improve invoice content model
- Now will send email after Paypal & Xendit payment success
- Fix can't create Xendit payment if no phone number or email exist
- Fix send email for Reminder
- Expired and Cancelled invoice now can send email
- Fix and update email send for recurring
- Bugfix email sent automatically when invoice set to paid on non woocommerce invoice
- Refactor Assets.php
- Update invoice content model
- Bugfix can't add customer address on client search result

## June 21, 2024
### v0.0.4
- Hotfix database connection error
- Add MultinelineText component on invoice for text that need line break
- Remove log and prepare to production release

## June 25, 2024
### v0.0.5
- Update and fix payment error handler
- Add unregister hook after plugin deactivation
- Fix Woocommerce hook for not showing create invoice in order page because of older version Woocommerce
- Add note to product, so product will show note instead of description
- Add support for Woocommerce bank payment

## June 29, 2024
### v0.0.6
- Fix many bugs
- Fix to support woocommerce Bank and Paypal payment

## July 6, 2024
### v0.0.7
- Change to only have 1 business only
- Fix empty chart in Dashboard
- Move Invoize admin menu position in Wordress to higher position
- Update to support Wordpress 5.3
- Failed Paypal & Xendit payment now will display the error to user
- Bugfix unable to update invoice status if has no receipt
- Add support for Woocommerce product variation
- Bugfix default payment get selected during edit mode
- Bugfix paypal direct payment link missing after edit invoice
- Bugfix summary data not updated properly on Archived invoice
- Bugfix calendar icon position in Custom Due Date
- Update to show Paypal & Xendit payment link in non public, print, and download.

## July 9, 2024
### v0.0.8
- Remove unused upload API
- Bugfix dashboard chart not showing
- Bugfix sequence number in list page not updated
- Update permission
- Improve print & download styling

## July 11, 2024
### v0.0.9
- Update Widget style
- Add Woocommerce hook on trashed, restore, and delete order
- Add expired invoice state action
- Add suffix to invoice length in tab  (K, M, B)
- Update to use # as prefix invoice
- Fix duplicate for expired invoice
- Fix text selection that has no color indicator
- Fix Woocommerce third party payment
- Fix permission
- Fix add edge case for can't update Paid invoice if already Paid
- List page data now always get fetched on every visit

## July 19, 2024
### v0.1.0
- Update Customer and Business form with long address form
- Update to create Invoize customer if invoice/recurring using Woocommerce customer
- Add authentication on invoice preview
- Add send email option on Mark as Paid in preview page
- Add option to delete WP User if deleting Customer
- Add "Grant Preview Access" option on customer creation and add "Invoize Customer" role
- Update design for email template
- Add required email field for Customer
- Fix register cron on plugin activation
- Fix can't create recurring with woocommerce client
- Bugfix invoice number not updated after duplicating invoice
- Bugfix can't add custom address on edited invoice
- Fix discount & tax only will show based on selected currency in Create Invoice/Recurring page
- Fix overflow on long customer name in Expired section in Dashboard
- Bugfix can create fixed discount & tax without currency
- Adjust business & customer name size in preview
- Improve and refactor code for recurring preview dan dashboard

## Aug 2, 2024
### v0.2.0
- Add invoice/receipt pdf file as email attachment 
- Add currency form in product
- Update download invoice and download receipt feature
- Update downloaded invoice and receipt pdf styling
- Update business and customer form input
- Improve styling on input with currency value
- Many bug fixes and improvement