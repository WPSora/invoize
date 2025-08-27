=== Invoize ===
Contributors: WPSora, Opensynergic, Dede Nugroho, dhani, rahman ramsi, ghazi pradana, freemius
Tags: finance, invoice, receipt, report, accounting,
Requires at least: 5.3
Tested up to: 6.8
Requires PHP: 7.4
License: GPLv3
Stable tag: 1.12.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Invoize is an intuitive and user-friendly WordPress plugin designed for effortless invoice creation. With a focus on ease of use.

== Description ==
Invoize is an intuitive and user-friendly WordPress plugin designed for effortless invoice creation. With a focus on ease of use, allowing you to create and manage invoices efficiently directly from your WordPress. Ideal for freelancers, small businesses, and online shops, Invoize helps you streamline your billing process with customizable templates and easy integration.

**Open Source**: Invoize is open source software. You can find the complete source code on our [GitHub repository](https://github.com/WPSora/invoize), ensuring transparency and allowing you to review, contribute, or customize the plugin according to your needs.

== Features ==
- **Recurring Invoices**: Create recurring invoices for your clients and automate your billing process.
- **Reminder Emails**: Send automatic reminders to your clients for overdue invoices.
- **Multi-Currency Support**: Create invoices in multiple currencies and accept payments in your preferred currency.
- **Discount and Tax**: Add discounts and taxes to your invoices to reflect the accurate amount.


== Integrations ==
- **WooCommerce Integration**: Seamlessly create invoices for your WooCommerce orders automatically.  
- **PayPal Integration**: Enable clients to pay invoices directly through [Paypal](https://www.paypal.com). For details, see [Privacy Policy](https://www.paypal.com/us/legalhub/paypal/privacy-full) and [User Agreement](https://www.paypal.com/us/legalhub/paypal/useragreement-full).  
- **Xendit Integration**: Accept payments conveniently via [Xendit](https://www.xendit.co/en-id/), a payment gateway popular in Indonesia that supports various payment methods. Learn more by reviewing their [Terms of Service](https://www.xendit.co/en-id/terms-and-conditions/) and [Privacy Policy](https://www.xendit.co/en-id/privacy-policy/).

== Changelog ==

= 1.12.0 | 27 August 2025 =
* Added support for paypal auto-confirmation in payment page and also support for woocommerce
* Fixed hide invoize welcome from menu
* Fixed error on "Don't create invoize if total woocommerce transaction is 0" feature
* Fixed monthly report not running

= 1.11.5 | 21 August 2025 =
* Minor fix

= 1.11.4 | 20 August 2025 =
* Minor fix

= 1.11.3 | 13 July 2025 =
* Fixed paypal direct link in payment page

= 1.11.2 | 09 July 2025 =
* Fixed error on edit recurring

= 1.11.1 | 01 July 2025 =
* Fixed Paypal direct payment link in payment page

= 1.11.0 | 24 June 2025 =
* Added support for wordpress version 6.8

= 1.10.4 | 24 June 2025 =
* Fixed fatal error

= 1.10.3 | 23 June 2025 =
* Adjusted some code to comply wordpress standard

= 1.10.2 | 21 June 2025 =
* Changed logo in payment page to business logo

= 1.10.1 | 21 June 2025 =
* Fixed Xendit webhook output message

= 1.10.0 | 20 June 2025 =
* Added Xendit Webhook token. By using this webhook, after Xendit payment success, it will always able to update invoice status to paid and can't be cancelled.
* Added Xendit redirect url settings on payment success and failed.

= 1.9.3 | 04 June 2025 =
* Fixed payment page sometimes not working properly for Woocommerce transaction

= 1.9.2 | 22 May 2025 =
* Added settings on Woocommerce integration: Option to disable invoice creation if total transaction is 0.

= 1.9.1 | 20 May 2025 =
* Changed Xendit payment external ID to invoice number

= 1.9.0 | 19 May 2025 =
* Added Log feature
* Fixed regenerate invoice
* Fixed error on edit customer in Recurring
* Fixed Recurring payment missing
* Other bugfix and improvements

= 1.8.0 | 29 April 2025 =
* Added Reminder for Admin
* Fixed duplicate Xendit payment

= 1.7.2 | 25 April 2025 =
* Fixed dashboard chart to display correct data. 
* Fixed payment page to automatically update invoices to 'paid' status after payment via Xendit. 

= 1.7.2 | 25 April 2025 =
* Fixed dashboard chart to display correct data. 
* Fixed payment page to automatically update invoices to 'paid' status after payment via Xendit. 

= 1.7.1 | 24 April 2025 =
* ✨ New Feature Quotation
* ✨ New Feature: Added "Payment Page" functionality.
* Fixed invoice email content 
* Fixed error when sending email from quotation
* Fixed invoice email content 

= 1.7.0 | 23 April 2025 =
* ✨ New Feature: Added "Recurring to Invoice" feature.
* Fixed payment link url error in pdf

= 1.5.3 | January 30, 2025 =
* Fix: Blank Receipt

= 1.5.2 | January 15, 2025 =
* Patched: Security vulnerability related to freemius

= 1.5.1 | January 15, 2025 =
* Fixed: Customer not showing in the invoice preview.

= 1.5.0 | January 9, 2025 =
* ✨ New Feature: Added "Bill To" functionality.
* 💡 Improved: Refined the preview UI for a better user experience.  

= 1.4.7 | January 8, 2025 =
* Fixed: PDF appearance now matches the web preview.
* Improved: Code enhancements for better performance and maintainability.

= 1.4.6 | January 6, 2025 =
* Fixed: Missing Assets

= 1.4.5 | January 5, 2025 =
* Fixed: Unable to edit discount on recurring
* Fixed: Unable to edit discount when discount type is "Fixed"

= 1.4.3 | November 13, 2024 =
* Fixed: Invoice Preview Issue
* Fixed: Invoice Regenerate Issue
* Code Improvement

= 1.4.2 | November 8, 2024 =
* Fixed: Client Role Issue

= 1.4.1 | November 2, 2024 =
* Fixed: WooCommerce Integration Issues
* Improved code structure and maintainability

= 1.4.0 | October 27, 2024 =
* Fixed: Xendit Currency in PDF
* Fixed: BG Color issue on Print Window
* Fixed: Reminder Notification Issue
* Fixed: Unable to generate Invoice when it's Guest Order
* Added: Replace Due Date with Paid Date in Paid Tab
* Added: Cancel Invoice on Canceled Order
= 1.3.0 | October 15, 2024 =
* Added Free Version
* Fixed: Currency display issue in Custom Discounts
* Fixed: Currency display issue in Products
* Fixed: Xendit currency display in PDF
* Fixed: Background color not applied in print mode
* Fixed: Issue preventing invoice generation from WooCommerce orders when users are not logged in
* Updated broken link
* Fixed: Payment URL issue

= 1.2.1 | 28 September 2024 =
* Fix: Issue with Small Paper Size

= 1.2.0 | 26 September 2024 =
* Added Monthly Report feature
* Implemented Live and Sandbox modes for PayPal
* Added PayPal Test Connection
* Enabled editing of Custom Client addresses
* Enhanced Widget UI
* Fixed Invoice Preview Date width
* Corrected Date Format in emails
* Fixed Money Format in PDFs

= 1.1.4 | 18 September 2024 =
* Fix: Dashboard UI Improvement
* Code Improvement
* Update Freemius SDK

= 1.1.3 | 7 September 2024 =
* Fix: WooCommerce Integration show when WooCommerce is not installed

= 1.1.2 | 5 September 2024 =
* Fix: E-Mail content are messed up
* Fix: WooCommerce Integration show when WooCommerce is not installed

= 1.1.1 | 28 August 2024 =
* Fix: Read Documentation Link not working
* Fix: Unable to save business information

= 1.1.0 | 26 August 2024 =
* Improve First Setup Wizard
* Add Invoice Column to My Account
* Small Dashboard Improvement
* Fixing Minors Bugs
* Widget Improvement
* Code Improvement
* Fix Minors Bugs

= 1.0.2 | 6 August 2024 =
* Small UI Improvement
* Fix Minors Bugs

= 1.0.0 | 1 August 2024 =
* Initial release