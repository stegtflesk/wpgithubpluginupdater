<?php


namespace GithubUpdater\Controller;

use GuzzleHttp;
use stdClass;

class GithubPluginUpdateController
{

    private $pluginFile;

    function __construct( $pluginFile, $gitHubUsername, $gitHubProjectNam) {
        $this->pluginFile = $pluginFile;
        $this->getCheckedSlug();

        set_site_transient('update_plugins', null);

        add_filter( "pre_set_site_transient_update_plugins", array( $this, "checkForUpdate" ) );
        add_filter('plugins_api', array($this, 'pluginAPICallback'), 10, 3);
        add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );
    }

    // Perform additional actions to successfully install our plugin
    public function postInstall( $true, $hook_extra, $result ) {
        global $wp_filesystem;

        // Since we are hosted in GitHub, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:
        $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
        $wp_filesystem->move( $result['destination'], $pluginFolder );
        $result['destination'] = $pluginFolder;

        // Re-activate plugin if needed
        if ( $this->pluginActivated )
        {
            $activate = activate_plugin( $this->slug );
        }

        return $result;
    }

    public function pluginAPICallback($false, $action, $response ) {
        // If nothing is found, do nothing
        if ( empty( $response->slug ) || $response->slug != $this->slug ) {
            return false;
        }

        // Add our plugin information
        $response->last_updated = $this->getPluginRelease('published_at');
        $response->slug = $this->getSlug();
        $response->plugin_name  = $this->getPluginData('Name');
        $response->version = $this->getPluginRelease('tag_name');
        $response->author = $this->getPluginData('AuthorName');
        $response->homepage = $this->getPluginData('PluginURI');

        $response->download_link = $this->getPluginRelease('zipball_url');

        return $response;
    }

    public function checkForUpdate($data) {

        if (property_exists($data, 'checked')
            && is_array($data->checked)
            && array_key_exists($this->getCheckedSlug(), $data->checked)) {

            if ($this->hasUpdate($data)) {
                $newRelease = new stdClass();
                $newRelease->slug = $this->getSlug();
                $newRelease->plugin = $this->getCheckedSlug();
                $newRelease->new_version = $this->getPluginRelease('tag_name');
                $newRelease->url = $this->getPluginData('PluginURI');
                $newRelease->package = $this->getPluginRelease('zipball_url');

                $data->response[$this->getCheckedSlug()] = $newRelease;
            }
        }

        return $data;
    }

    private function hasUpdate($data) {
        return (bool)version_compare($data->checked[$this->getCheckedSlug()], $this->getPluginRelease('tag_name'));
    }

    private function getCheckedSlug() {
        return wp_basename($this->pluginFile, '.php') . '/' . wp_basename($this->pluginFile);
    }

    private function getSlug() {
        return wp_basename($this->pluginFile, '.php');
    }

    private function getPluginData($key = '') {

        $pluginData = get_plugin_data($this->pluginFile);

        if (!empty($key) && array_key_exists($key, $pluginData)) {
            return $pluginData[$key];
        }

        return $pluginData;
    }

    private function getPluginRelease($property = '') {

        $client = new GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.github.com/repos/stegtflesk/wpgithubpluginupdater/releases/latest');

        if ($response->getStatusCode() === 200) {
            /** @var stdClass $release */
            $release = json_decode($response->getBody());

            if (!empty($property) && property_exists($release, $property)) {
                return $release->{$property};
            }

            return $release;
        }
    }
}