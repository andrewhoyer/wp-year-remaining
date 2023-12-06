<?php
/*

	Year Remaining - Outputs the percentage of time remaining in the year.
	Copyright (C) 2023 Andrew Hoyer

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <https://www.gnu.org/licenses/>.

	* Plugin Name:       Year Remaining
	* Plugin URI:        https://andrewhoyer.com/year-remaining
	* Description:       Outputs the percentage of time remaining in the year.
	* Version:           0.1.1
	* Author:            Andrew Hoyer
	* Author URI:        https://andrewhoyer.com
	* License:           GPL-3.0
	* License URI:       https://www.gnu.org/licenses/gpl-3.0.html#license-text
	* Text Domain:       year-remaining
	* Domain Path:       /languages
	* Requires at least: 5.2
	* Requires PHP:      7.0

*/

function yr_year_remaining($atts) {
	return yr_generate();
}

add_shortcode("year_remaining", "yr_year_remaining");

/*
	Dashboard widget
*/
function yr_add_dashboard_widget() {
	wp_add_dashboard_widget(
		'yr_dashboard',
		'Year Remaining',
		'yr_dashboard_widget_content'
	);
}
add_action('wp_dashboard_setup', 'yr_add_dashboard_widget');

function yr_dashboard_widget_content() {
	echo yr_generate();
}

/*
	Year Remaining progress bar
*/
function yr_generate() {
	// Default timezone to UTC
	date_default_timezone_set('UTC'); 

	// Get current day of the year. Increase by 1 because range is 0 - 365.
	$current_date = new DateTime();
	$day_of_year = (int)$current_date->format('z') + 1; 
	//$day_of_year = 1; // For debug purposes. Set value from 1 to 366

	// A flag determining whether the percentage is an even integer.
	$integer_percent = true;

	// For edge cases, set the percentage specifically.
	if ($day_of_year >= 365) {
		$percent_remaining = 0;
	} elseif ($day_of_year == 1) {
		$percent_remaining = 100;
	} else {
		// For all other dates, calculate percent. This value will be a number from 0 - 1
		// Example: 0.24657534246575 which means 24.6%
		$percent_remaining = 1 - ($day_of_year / 365.0);

		// If percent remaining is less than or equal to 0.273 of an integer, round it down and remove decimal.
		// Each day is 0.00273 (or 0.273%) of the year.

		// Pads the percent calculation to ensure enough characters, splits on the decimal point
		// Then gets three digits representing the decimal percentage. Example: 657
		if ((int)substr(explode('.', str_pad($percent_remaining, 8, '0',  STR_PAD_RIGHT))[1], 2, 3) <= 273) {
			$percent_remaining = (int)($percent_remaining * 100);
		} else {
			// For all other days, round to one decimal point.
			$percent_remaining = (int)($percent_remaining * 1000.0) / 10.0;
			$integer_percent = false;
		}
	}

	# Build a string that is the percentage rounded to the nearest 5 to be used to create the progress bar

	$str_array = str_split((string)(int)$percent_remaining);

	if (end($str_array) == '5' || end($str_array) == '0') {
		// No processing needed.
	} elseif (end($str_array) > '5') {
		if (count($str_array) > 1) {
			$str_array[count($str_array) - 1] = '0';
			$str_array[count($str_array) - 2] = (string)((int)$str_array[count($str_array) - 2] + 1);
		} else {
			$str_array[count($str_array) - 1] = '1';
			$str_array[] = '0';
		}
	} elseif (end($str_array) < '5') {
		$str_array[count($str_array) - 1] = '5';
	}

	$display_string = implode('', $str_array);

	// Get the number of progress bar blocks on the left based on a 10-block format.
	$blocks_out_of_ten = (100 - (int)$display_string) / 10;

	// Determine the number of left / right progress bar blocks.
	// This allows for progress bars of varying length. Set bar_width to length of progress bar.
	$bar_width = 10;
	$characters_left = floor($blocks_out_of_ten * $bar_width / 10.00);
	$characters_right = $bar_width - $characters_left;

	// Build the progress bar text
	$progress_bar_array = [];
	for ($i = 0; $i < $characters_left; $i++) {
		$progress_bar_array[] = '░';
	}
	for ($i = 0; $i < $characters_right; $i++) {
		$progress_bar_array[] = '▓';
	}

	$progress_bar_str = implode('', $progress_bar_array) . ' ' . $percent_remaining . '%';

	// Allow developers to filter the progress bar text.
	$progress_bar_str = apply_filters('yr_filter_progress_bar', $progress_bar_str);
	
	// Can be used for debugging purposes
	$debug = [
		'day_of_year' => $day_of_year,
		'percent_remaining' => $percent_remaining,
		'progress_bar_str' => $progress_bar_str,
		'integer_percent' => $integer_percent
	];
	
	return $progress_bar_str;
}

?>
