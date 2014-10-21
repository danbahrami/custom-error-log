=== Plugin Name ===
Contributors: danbahrami
Donate link:
Tags: error, errors, custom errors, error log, log, php, php error, php errors, debug, debugging
Requires at least: 3.0.1
Tested up to: 4.0.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A tool for logging and monitoring custom errors, making debugging complex PHP a breeze.

== Description ==

Custom Error Log allows you to create custom errors in your Wordpress theme or plugin with a single function. Great for debugging those long and complex scripts.

Each error can have its own error message making it a lot easier to pin down specific issues in your code. You can also log notices for less serious issues.

All your errors and notices can be viewed conveniently in the Wordpress admin area.

### Plugin Features:
* **Easy Errors:** Create errors with a simple function.
* **Useful Errors:** Define a custom error message for each error.
* **Log notices:** Notices can be logged for less serious issues and viewed seperately in the admin area.
* **Keep Track:** View and moderate all your errors in the Wordpress admin area.

### Getting Started:
Install and activate the plugin then in the admin menu go to *Tools > Error Log*. This will eventually be the page that displays all your latest errors but if you're just getting started it explains in detail how to start logging custom errors.

In your theme use either of the two built in functions to log an error `log_error($message);` or a notice `log_notice($message);`.

When these functions are executed the will log whatever error or notice you want.

### Ongoing Development:
I have only just started with this plugin and plan on bringing great improvements to it. If you have any ideas please suggest them in the support section.

### Translation:
The plugin is translation ready but as of yet contains only an English translation file. If you would like to contribute any translations I would be very grateful.

== Installation ==

1. Upload `Custom Error Log` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the function `log_error($error);` in your theme where you would like to log an error.

Once the plugin is installed and activated you can find more detailed instructions under *Tools > Error Log* in the Wordpress admin menu.

== Frequently Asked Questions ==

= What's the point? =

As they say 'Necessity is the mother of invention'... I was recently developing a Wordpress site which imported data from a CRM in the background and used that data to create users, posts, taxonomies etc.

I started using the standard PHP error log but quickly found that the errors didn't have enough specific information to make them useful. I needed to know exactly what information wasn't being imported properly and why so I created this plugin to allow me to customise the information stored in each error.

There are other ways, like using full debugging tools but I think it's easier to have a simple, Wordpress native tool.

= How do I log an error? =

It's simple, you place the following function in your theme/plugin where you want to log an error...

`log_error($message);`

Replace $message with whatever error message you want to log for example if you're adding a new user you could do this...

`$user_id = wp_create_user( $user_name, $password, $user_email );

if( is_wp_error( $user_id ) ) {

    $error_response = $user_id->get_error_message();
    
    $mesage = "Unable to create user with username: " . $user_name;
    $message .= " password: " . $password;
    $message .= " The following error occurred: " . $error_response;
    
    log_error($message);
    
}`

== Screenshots ==

1. The error log page.

== Changelog ==

= 1.0 =
* Hello world...

== Upgrade Notice ==