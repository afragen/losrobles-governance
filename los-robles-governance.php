<?php
/**
 * Plugin Name:       Los Robles Governance
 * Plugin URI:        https://github.com/afragen/losrobles-governance
 * Description:       This plugin adds registration, custom user meta and other things to the Los Robles HOA website for web-based governance.
 * Version:           1.2.1
 * Author:            Andy Fragen
 * License:           MIT
 * GitHub Plugin URI: https://github.com/afragen/losrobles-governance
 * Requires WP:       5.2
 * Requires PHP:      7.1
 */

namespace Fragen\LosRobles;

// Load Autoloader.
require_once __DIR__ . '/vendor/autoload.php';
( new Bootstrap() )->run();
