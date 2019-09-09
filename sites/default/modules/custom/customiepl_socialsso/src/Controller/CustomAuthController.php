<?php

namespace Drupal\social_auth_google\Controller;

use Drupal\social_auth\Controller\OAuth2ControllerBase;

/**
 * Returns responses for Social Auth Google module routes.
 */
class CustomAuthController extends OAuth2ControllerBase {
    /**
     * Response for path 'user/login/google/callback'.
     *
     * Google returns the user here after user has authenticated.
     */
    public function custom_callback() {

         echo 'callback';
         exit();
    }
}
