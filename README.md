# wp-year-remaining
A WordPress plugin that outputs the percentage of time remaining in the year.


## Installation

### WordPress Plugin directory

You can now find the Year Remaining plugin right in the WordPress Plugin directory: [Plugin Page](https://wordpress.org/plugins/year-remaining/)

Open the Plugins area of your WordPress admin, click "Add New Plugin", then search for "year remaining". When the plugin listing loads, press "Install Now" followed by "Activate".

### Manual upload

Use this option if you want to use the code from this repo which may be more recent than what is available in the plugin directory.

* Upload the year-remaining folder from the repo to the /wp-content/plugins/ folder of your WordPress site.
* Activate the "Year Remaining" plugin in the Plugins section of the WordPress admin area.

### Upload plugin

* Compress the year-remaining folder into a .zip file.
* In the WordPress admin Plugins area, use the "Add New Plugin" option and upload the .zip file.
* Activate the "Year Remaining" plugin.

## Usage

### Shortcode

The progress bar and percent remaining in the year can be output by using the following shortcode:

```[year_remaining]```

Sample output:

░░░░░░░▓▓▓ 23.8%

### Dashboard

The plugin adds a widget to the Dashboard that displays the progress bar and percent remaining in the current year.

![Dashboard Widget](/images/dashboard.png)

## Developers

### Modifying the progress bar

A filter can be used to modify the progress bar and percentage text before it is displayed on the screen.

The name of the filter hook is ```year_remaining_filter_progress_bar```.

Example code:

```
add_filter('year_remaining_filter_progress_bar', 'modify_progress_bar', 10);

function modify_progress_bar($bar) {
    $bar = str_replace("░","🙁", $bar);
    $bar = str_replace("▓","😃", $bar);

    return $bar;
}
```