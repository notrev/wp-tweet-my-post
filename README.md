# Tweet my Post #

- Contributors:
- Donate link: No donations needed, but thanks.
- Tags: post, twitter, publish
- Requires at least: 3.5.1
- Tested up to: 4.4
- Stable tag: master
- License: GPLv2 or later
- License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that publishes your post to a Twitter account.

## Description ##

This plugin publishes posts to a Twitter account.

There is a meta-box in post pages with a checkbox that defines if a post
should be published to Twitter.

In order for posts to be tweeted, some informations are necessary. You need
to setup a Twitter App and set keys in the plugin setup page. More informations
can be found there.

## Installation ##

You will need 4 informations in order for the plugin to be able to publish to
Twitter: API Consumer Key, API Consumer Secret, OAuth Access Token, OAuth Access Token Secret.

To get these informations, you will need to create an app in Twitter. To do so, you
can follow the steps in this link:

http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/

After the app creation, insert the 4 information in their respective input fields, in
the Tweet my Post settings page, in the WordPress admin area (Settings > Tweet my Post).

If everything is correct, when you hit the **Publish** button, in post page, a message will be
tweeted to the specified Twitter account containing you post title and the permalink to the post.

### Using The WordPress Dashboard ###

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Tweet my Post'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

### Uploading in WordPress Dashboard ###

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `tweetmypost.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

### Using FTP ###

1. Download `tweetmypost.zip`
2. Extract the `tweetmypost` directory to your computer
3. Upload the `tweetmypost` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


## Frequently Asked Questions ##

### How can I get API Key, API Key Secret, Access Token and Access Token Secret? ###

Check this website: http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/

## Screenshots ##

To be added.

## Changelog ##

### 1.0 ###
* First working version

## Upgrade Notice ##

### 1.0 ###
First working version.

### 0.1 ###
First draft. Not working yet

## To Do ##

* Clean up repository, removing unnecessary files added by the [WordPress Plugin Boilerplate](http://wppb.io/).
* Generate .POT file for translations.
