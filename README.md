<h1 align="center">ClickPay - Magento</h1>
<p align="center"><i>The Official Magento2 plugin for ClickPay</i></p>
<div align="center">
   <h2 align="center">Plugin features</h2>
<h4>Embeded Form</h4>
<h4>Direct Apple Pay</h4>
<h4>Real Refund from Magento (credit Memo)</h4>
<h4>Automatic Invoice</h4>
<h4>On Payment Method selection place order</h4>
</div>

- - -

## Installation

### Install using FTP method

*Note: Delete any previous ClickPay plugin.*

1. Download the latest release of the plugin
2. Upload the content of the folder to magento2 installation directory: `app/code/ClickPay/PayPage`
3. Run the following Magento commands:
   1. `php bin/magento setup:upgrade`
   2. `php bin/magento setup:static-content:deploy`
   3. `php bin/magento cache:clean`

- - -
### Install using `Composer`

1. `composer require clickpay/magento_v4`
2. `php bin/magento setup:upgrade`
3. `php bin/magento setup:static-content:deploy`
4. `php bin/magento cache:clean`

---

## Activating the Plugin

By default and after installing the module, it will be activated.
To Disable/Enable the module:

### Enable

`php bin/magento module:enable ClickPay_PayPage`

### Disable

`php bin/magento module:disable ClickPay_PayPage`

- - -

## Configure the Plugin for normal redirect and iframe

1. Navigate to `"Magento admin panel" >> Stores >> Configuration`
2. Open `"Sales >> Payment Methods`
3. Select the preferred payment method from the available list of ClickPay payment methods
4. Enable the `Payment Gateway`
5. Enable hosted or iframe method
6. Enable automatic invoice
7. Enter the primary credentials:
   - **Profile ID**: Enter the Profile ID of your ClickPay account
   - **Server Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
   - **Client Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
8. Click `Save Config`


- - -

## Configure the Plugin for Managed form

1. Navigate to `"Magento admin panel" >> Stores >> Configuration`
2. Open `"Sales >> Payment Methods`
3. Enable the `Payment Gateway`
4. Enable Managed form in the frame option
5. Enable automatic invoice
6. Enter the primary credentials:
   - **Profile ID**: Enter the Profile ID of your ClickPay account
   - **Server Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
   - **Client Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
7. Click `Save Config`
8. Contact Clickpay Technical team for whitlisting you server for having this feature.
- - -

## Configure the Plugin for Direct Apple Pay on your website

1. Navigate to `"Magento admin panel" >> Stores >> Configuration`
2. Open `"Sales >> Payment Methods`
3. Select the Apple Pay method from the available list of ClickPay payment methods
4. Please find the setup section below for the apple pay certificate creation
5. Once certificates created upload the certificates in admin panel
6. Add the Merchnat identifier name

   <img width="809" alt="Screenshot 2024-02-28 at 11 02 44 AM" src="https://github.com/clickpaysa/clickpay-magento2.x/assets/135695828/75893cf4-5159-47c6-a5bf-7b7e3a200c62">

8. Enter the primary credentials:
   - **Profile ID**: Enter the Profile ID of your ClickPay account
   - **Server Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
   - **Client Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
9. Click `Save Config`


## Setup

1. You must have a Apple Developer Account To use this Feature
2. The Domain URL which you want to display the ApplePay Button. It should be verfied under same Apple Developer Account and under same merchant ID which you will create Certificates

![image](https://github.com/clickpaysa/Direct_ApplePay_on_Web/assets/135695828/2b6c16ba-58b3-44ed-a690-dfeb7762b9cb)
 
3. Create two certificates (Apple Pay Payment Processing Certificate & Apple Pay Merchant Identity Certificate)
4. Create Apple Pay Payment Processing Certificate using below Link

       https://support.clickpay.com.sa/en/support/solutions/articles/73000593115-how-to-configure-apple-pay-certificate-in-my-clickpay-dashboard-
5. Create Apple Pay Merchant Identity Certificate using below Steps

       openssl req -sha256 -nodes -newkey rsa:2048 -keyout merchant-cert.key -out merchant-cert.csr   (Create the CSR and Key File)
   
       Upload the CSR in Apple Developer portal to create merchant identifier
       Once Created Download the Certificate and Convert the downloaded cer to crt using below command
   
       openssl x509 -inform der -in merchant_id.cer -out merchant-cert.crt


## Log Access

### ClickPay custome log

1. Access `debug_Clickpay.log` file found at: `/var/log/debug_Clickpay.log`

- - -

Done
