<?php

/**
 * @file
 * Contains google_kpis.drush.inc.
 */

/**
 * Implements hook_drush_command().
 */
function google_kpis_drush_command() {
  $commands['google_kpis_fetch_and_store'] = [
    'description' => 'Fetch and store google statistics.',
    'drupal dependencies' => ['google_kpis'],
    'aliases' => ['gkfs'],
    'options' => [
      'gsc' => 'trigger command for search console api',
      'ga' => 'trigger command for analytics api',
    ],
    'examples' => [
      'drush gkfs' => 'Fetch and store google statistic',
      'drush gkfs --gsc' => 'fetch and stores search console data only',
      'drush gkfs --ga' => 'fetch and stores analytics data only',
    ],
  ];

  return $commands;
}

/**
 * Callback function for google_kpis drush command.
 *
 * Fetch and store google statistic data.
 *
 * @see Drupal\google_kpis\GoogleAnalyticsFetchAndStore
 */
function drush_google_kpis_fetch_and_store() {
  /** @var \Drupal\google_kpis\GoogleKpisFetchAndStore $service */
  $service = Drupal::service('google_kpis.fetch_and_store');
  $gsc = drush_get_option('gsc');
  $ga = drush_get_option('ga');
  if ($gsc) {
    $search_console_data = $service->fetchGoogleSearchConsoleData();
    $service->prepareQueue($search_console_data);
    drush_print('The search console API data fetched and queued successful.');
    // Run queue.
    drush_invoke('queue-run', 'google_kpis_queue');
    return;
  }
  if ($ga) {
    $analytics_data = $service->fetchGoogleAnalyticsData();
    $service->prepareQueue($analytics_data);
    drush_print('The analytics API data fetched and queued successful.');
    // Run queue.
    drush_invoke('queue-run', 'google_kpis_queue');
    return;
  }

  $analytics_data = $service->fetchGoogleAnalyticsData();
  $search_console_data = $service->fetchGoogleSearchConsoleData();
  $data = $service->combineAnalyticsAndSearchConsoleData($analytics_data, $search_console_data);
  $service->prepareQueue($data);
  drush_print('Your google data was successfully moved to the queue google_kpis_queue.');
  // Run queue.
  drush_invoke('queue-run', 'google_kpis_queue');
}
