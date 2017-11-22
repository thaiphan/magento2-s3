Thai's S3 Extension for Magento 2
=================================

[![Total Downloads](https://img.shields.io/packagist/dt/thaiphan/magento2-s3.svg?style=flat)](https://packagist.org/packages/thaiphan/magento2-s3)
[![MIT License](https://img.shields.io/packagist/l/thaiphan/magento2-s3.svg?style=flat)](https://packagist.org/packages/thaiphan/magento2-s3)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/thaiphan)

Thai's S3 extension for Magento 2 allows retailers to upload their catalogue and WYSIWYG images straight to Amazon S3.

Benefits
--------

### Easy to use

This extension is easy to use with little configuration! You only need to follow a few simple steps (and one of them is simply to create a copy of your images as a precaution) to get up and running!

### Sync all your media images

The following images are automatically saved to S3:

* Product images
* Generated thumbnails
* WYSIWYG images
* Category images

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

If you can't find the answer you're looking for, however, feel free to [create a GitHub issue](https://github.com/thaiphan/magento2-s3/issues/new) or [send us an email](mailto:thai@outlook.com) for support regarding this extension.

FAQs
----

### Does this extension upload my log files?

No, the S3 extension only syncs across the media folder. You will need to find an alternative solution to store your log files.

### We did something wrong and all our images are gone! Can you restore it?

I recommend taking a backup of your media files when switching file storage systems. Unfortunately, there's nothing I can do if you somehow accidentally delete them.
