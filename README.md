Thai's S3 Extension for Magento 2
=================================

[![Total Downloads](https://poser.pugx.org/thaiphan/magento2-s3/d/total.svg)](https://packagist.org/packages/thaiphan/magento2-s3)
[![MIT License](https://poser.pugx.org/thaiphan/magento2-s3/license.svg)](https://packagist.org/packages/thaiphan/magento2-s3)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/thaiphan)

[Amazon S3](https://aws.amazon.com/s3/) is a popular file storage solution that allows web apps to host images in the cloud. Setting up your web app to use cloud storage removes the need to use the local filesystem to store images. This, in turn, allows companies to spin up more servers to handle more traffic without having to worry also copying over pre-existing images onto these new servers. Cloud storage also provides some other performance benefits, such as shifting a lot of the load of retrieving images for a customer to S3 and the ability to easily set up a CloudFront CDN for your images.

Thai's S3 extension for Magento 2 allows retailers to upload their catalogue and WYSIWYG images straight to Amazon S3.

This is done by overriding the [built-in database file storage mechanism](https://docs.magento.com/m2/ee/user_guide/system/media-storage-database.html) to upload to Amazon S3 bucket instead of MySQL. By handling integration with Amazon S3 in this manner, this extension will work nicely with third-party extensions that properly use Magento's file system mechanisms as well as work with any future Magento functionality.

Account & pricing
-----------------

You will need to create an [Amazon AWS account](https://portal.aws.amazon.com/gp/aws/developer/registration/index.html) to get started. The cost to host your images on S3 varies depending on the AWS region that you are using and on the kind and amount of images that you upload. Click here for more detailed pricing information, https://aws.amazon.com/s3/pricing/

Features
--------

### Easy to use

This extension is easy to use with little configuration! You only need to follow a few simple steps (and one of them is simply to create a copy of your images as a precaution) to get up and running!

### Sync all your media images

The following images are automatically saved to S3:

* Product images
* Generated thumbnails
* WYSIWYG images
* Category images
* CAPTCHA images

### Magento can now scale horizontally

Complex file syncing between multiple servers is now a thing of the past with this extension. All your servers will be able to share the one S3 bucket as the single source of media.

### Easy integration with CloudFront CDN

CloudFront CDN supports using S3 as an origin server so you can significantly reduce load on your servers.

Installation
------------

See the [Installation](https://github.com/thaiphan/magento2-s3/wiki/Installation) page on the wiki.

Support
-------

There's a [Troubleshooting](https://github.com/thaiphan/magento2-s3/wiki/Troubleshooting) page on the wiki that I'll try and keep up to date with any issues that the community might have with the extension.

If you can't find the answer you're looking for, however, feel free to [create a GitHub issue](https://github.com/thaiphan/magento2-s3/issues/new) for support regarding this extension.

FAQs
----

### Does this extension upload my log files?

No, the S3 extension only syncs across the media folder. You will need to find an alternative solution to store your log files.

### Magento is still loading images from the file system! What went wrong?

The S3 extension is built on top of the built-in database file storage, which will re-download files back onto the file system as part of a caching mechanism. Magento will then use this cached version of the image instead of using S3.

If you have enabled S3 integration then you can safely delete the images off your file system **(although please take a backup just in case)**. If you want Magento to not download files to the file system, you can configure your static media URL to point to S3 or CloudFront.


### We did something wrong and all our images are gone! Can you restore it?

I recommend taking a backup of your media files when switching file storage systems. Unfortunately, there's nothing I can do if you somehow accidentally delete them.

Success Stories
---------------

Are you a happy user of my extension? I would love to feature you! [Create a GitHub issue](https://github.com/thaiphan/magento2-s3/issues/new) or [send me an email](mailto:thai@outlook.com) to discuss opportunities for cross promotion!
