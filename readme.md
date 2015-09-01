# RapidCampaign Magento Extension Technical Documentation

The aim of this Magento extension is to integration with RapidCampaign to show promotions to customers.


## Requirements

The user stories that this extension should achieve and maintain are as follows.

### Configuration

- As an administrator I must be able to configure the extension with my API key from the Magento admin area. 
- As an administrator I must be able to globally enable or disable the display of the promotional blocks from the Magento admin area.
- As an administrator I must be able to enable or disable the encryption of the iframe URL parameters from the Magento admin area
- As an administrator I must be able to configure the level of analytics to send to RapidCampaign from the Magento admin area
- As an administrator I must be able to define the cookie lifetime length from the Magento admin area
- As an administrator I must be able to enable or disable test mode for the extension from the Magento admin area
- As an administrator I must be able to enable or disable extension specific logs from the  Magento admin area

### Widget
- As an administrator I must be able to create a RapidCampaign widget
- As an administrator I must be able to specify to which RapidCampaign promotion this widget applies when creating the widget
- As an administrator I must be able to specify targeting conditions for the widget using the Magento sales rule interface when creating the widget
- As an administrator I must be able to specify addition targeting conditions for the widget by selecting customer groups that are available
- As a customer I should not be able to see a widget if the widget's sales rules are not matched
- As a customer I should not be able to see a widget if the extension is globally disabled
- As an administrator when I edit a widget I should see the sales rules interface populated with what was previously selected

### Automatic Coupon Application

- As a customer with items in my basket, when I click a coupon code link in the RapidCampaign promotion then coupon code should be applied to my cart
- As a customer with no items in my basket and I have clicked a coupon code link, when I go to the basket, I should still receive the discount. 

## Development Setup

This extension provides a modman file for further development.  [Modman](https://github.com/colinmollenhour/modman) is an easy way to install magento extensions via symlink. 

	modman init
    modman clone git@bitbucket.org:meanbee/rapidcampaign.git


## Components

### API Requests

Api requests are handled by `Model/Api/` with two classes. The first is a utility class for sending requests, the other is for the promotions endpoint, this is where much of the work is performed.  The request is attempted a maximum of 3 times if it fails.

There is a `Model/Storage.php` class which handles fetched promotions from the API and storing them for later use in a database table. 

There is a cron task defined in `config.xml` to re-fetch the promotions daily. Similarly, there is a custom button that has been added to the system configuration area which triggers a promotion cache expiry through the `controllers/Adminhtml/PromotionController.php`.

### Widget

The crux of the functionality comes from the widget that has been defined in `etc/widget.xml` and functionality created in `Block/Widget/Promotion.php` which allows a store owner to choose a promotion and targeting rules.

The targeting rules re-uses components from sales conditions and then stores it on the widget by base_64 encoding. There is an observer that is used to support the condition combining and serializing on the post. 

To select a promotion a separate window is needed and provided by `controllers/Adminhtml/ChooserController.php` and `Block/Widget/Grid/Chooser.php`.

Ultimately, this outputs the RapidCampaign div element and script embed tag for rendering the promotion.  There is some customer information included as parameters that are optionally encrypted using `Helper/Encrypter.php`. 


### Coupons

There is a new controller `controllers/CouponsConroller.php` which allows RapidCampaign to redirect customers with coupon code information (`$STORE_BASE_URL + '/rapidcampaign/coupons/apply/coupon/$COUPON_CODE'`)

If there are items in the basket then the coupon code is immediately applied and the sucess message is shown to the customer.  It's important that the same success/error message is used to maintain compatibility with the store's translations.

If there aren't items in the basket, we still send a message to a customer but we also add the coupon code to a `coupon_code` cookie.  

There is then an observer that listens to when a product is added to the basket and fetches the coupon from the cookie and automatically applies it to the basket. 


### Analytics

There are three new Blocks that have been created, one for each piece of JS; one for all pages, one for just the basket and one for the order success page.  There are no templates for this functionality at this time as the HTML is output in the block.

There is some logic around whether checkout success analytics is used for all customers or just those that interacted with a RapidCampaign Promotion.  This is managed through a cookie that is set when a coupon is applied. 


### Enterprise

Compatibility with Magento Enterprise edition and Full Page Cache is provided through hole punching.  This is needed for the promotion itself as well as the analytics.

Functionality that provides this can be found in `Model/FPC` folder and `etc/cache.xml`






