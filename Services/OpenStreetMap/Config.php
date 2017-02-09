<?php
/**
 * Config.php
 * 08-Nov-2011
 *
 * PHP Version 5
 *
 * @category Services
 * @package  Services_OpenStreetMap
 * @author   Ken Guest <kguest@php.net>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @version  Release: @package_version@
 * @link     Config.php
 */

/**
 * Services_OpenStreetMap_Config
 *
 * @category Services
 * @package  Services_OpenStreetMap
 * @author   Ken Guest <kguest@php.net>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @link     Config.php
 */
class Services_OpenStreetMap_Config
{

    /**
     * Minimum version of the OSM API that is supported.
     *
     * @var float
     *
     * @internal
     */
    protected $minVersion = null;

    /**
     * Maximum version of the OSM API that is supported.
     *
     * @var float
     *
     * @internal
     */
    protected $maxVersion = null;

    /**
     * Timeout, in seconds.
     *
     * @var integer
     *
     * @internal
     */
    protected $timeout = null;

    /**
     * Number of elements allowed per changeset
     *
     * @var integer
     *
     * @internal
     */
    protected $changesetMaximumElements = null;

    /**
     * Maximum number of nodes per way.
     *
     * @var integer
     *
     * @internal
     */
    protected $waynodesMaximum = null;

    /**
     * Number of tracepoints per way.
     *
     * @var integer
     *
     * @internal
     */
    protected $tracepointsPerPage = null;

    /**
     * Max size of area that can be downloaded in one request.
     *
     * @var float
     *
     * @internal
     */
    protected $areaMaximum = null;

    /**
     * Status of Database server: online, readonly or offline.
     *
     * @var string
     *
     * @internal
     */
    protected $databaseStatus = null;

    /**
     * Status of API server: online, readonly or offline.
     *
     * @var string
     *
     * @internal
     */
    protected $apiStatus = null;

    /**
     * Status of GPX server: online, readonly or offline.
     *
     * @var string
     *
     * @internal
     */
    protected $gpxStatus = null;

    /**
     * Default config settings
     *
     * @var array
     * @see Services_OpenStreetMap::getConfig
     * @see Services_OpenStreetMap::setConfig
     */
    protected $config = array(
        'accept-language' => 'en',
        'adapter'         => 'HTTP_Request2_Adapter_Socket',
        'api_version'     => '0.6',
        'password'        => null,
        'passwordfile'    => null,
        'server'          => 'http://api.openstreetmap.org/',
        'User-Agent'      => 'Services_OpenStreetMap',
        'user'            => null,
        'verbose'         => false,
    );

    /**
     * Version of the [OSM] API which communications will be over.
     *
     * @var string
     *
     * @internal
     */
    protected $api_version = '0.6';

    /**
     * Server to connect to.
     *
     * @var string
     *
     * @internal
     */
    protected $server = 'http://api.openstreetmap.org/';

    /**
     * Capabilities XML generated by...
     *
     * @var string
     *
     * @internal
     */
    protected $generator = 'Generator';

    /**
     * Get the value of a configuration setting - if none is set all are
     * returned.
     *
     * Use like:
     * <code>
     * $config = $osm->getConfig();
     * </code>
     *
     * @param string $name name. optional.
     *
     * @return mixed  value of $name parameter, array of all configuration
     *                parameters if $name is not given
     * @throws Services_OpenStreetMap_InvalidArgumentException If the parameter
     *                                                         is unknown
     */
    public function getValue($name = null)
    {
        if (is_null($name)) {
            return $this->config;
        } elseif (!array_key_exists($name, $this->config)) {
            throw new Services_OpenStreetMap_InvalidArgumentException(
                "Unknown config parameter '$name'"
            );
        }
        return $this->config[$name];
    }

    /**
     * Set at least one configuration variable.
     *
     * Use like:
     * <code>
     * $osm->setConfig('user', 'fred@example.com');
     * $osm->setConfig(array('user' => 'fred@example.com', 'password' => 'Simples'));
     * $osm->setConfig('user' => 'f@example.com')->setConfig('password' => 'Sis');
     * </code>
     *
     * The following parameters are available:
     * <ul>
     *  <li> 'accept-language' - language to use for queries with Nominatim</li>
     *  <li> 'adapter'         - adapter to use (string)</li>
     *  <li> 'api_version'     - Version of API to communicate via (string)</li>
     *  <li> 'password'        - password (string, optional)</li>
     *  <li> 'passwordfile'    - passwordfile (string, optional)</li>
     *  <li> 'server'          - server to connect to (string)</li>
     *  <li> 'User-Agent'      - User-Agent (string)</li>
     *  <li> 'user'            - user (string, optional)</li>
     *  <li> 'verbose'         - verbose (boolean, optional)</li>
     * </ul>
     *
     * @param mixed $config array containing config settings
     * @param mixed $value  config value if $config is not an array
     *
     * @throws Services_OpenStreetMap_InvalidArgumentException If the parameter
     *                                                         is unknown
     *
     * @return Services_OpenStreetMap_Config
     */
    public function setValue($config, $value = null)
    {
        if (is_array($config)) {
            if (isset($config['adapter'])) {
                $this->config['adapter'] = $config['adapter'];
            }
            foreach ($config as $key=>$value) {
                if (!array_key_exists($key, $this->config)) {
                    throw new Services_OpenStreetMap_InvalidArgumentException(
                        "Unknown config parameter '$key'"
                    );
                }
                switch($key) {
                case 'server':
                    $this->setServer($value);
                    break;
                case 'passwordfile':
                    $this->setPasswordfile($value);
                    break;
                case 'api_version':
                    $this->config[$key] = $value;
                    $api = "Services_OpenStreetMap_API_V" . str_replace(
                        '.',
                        '',
                        $value
                    );
                    $this->api = new $api;
                    break;
                case 'accept_language':
                    $this->setAcceptLanguage($value);
                    break;
                default:
                    $this->config[$key] = $value;
                }
            }
        } else {
            if (!array_key_exists($config, $this->config)) {
                throw new Services_OpenStreetMap_InvalidArgumentException(
                    "Unknown config parameter '$config'"
                );
            }
            $this->config[$config] = $value;
            if ($config == 'server') {
                $this->setServer($this->server);
            } elseif ($config == 'passwordfile') {
                $this->setPasswordfile($value);
            }
        }
        return $this;
    }

    /**
     * Set the 'Accept' language.
     *
     * @param string $language Accept Language
     *
     * @return Services_OpenStreetMap_Config
     */
    public function setAcceptLanguage($language)
    {
        $this->_validateLanguage($language);
        $this->config['accept-language'] = $language;
        return $this;
    }

    /**
     * Validate specified language.
     *
     * @param string $language ISO representation of language to validate
     *
     * @return void
     * @throws Exception If language invalid
     */
    private function _validateLanguage($language)
    {
        $langs = explode(",", $language);
        foreach ($langs as $lang) {
            if (strpos($lang, '-') !== false) {
                $subparts = explode("-", $lang);
                foreach ($subparts as $subpart) {
                    if ($this->_validateLanguageRegex($subpart) === false) {
                        throw new Exception("Language Invalid: $language");
                    }
                }
            } else {
                if ($this->_validateLanguageRegex($lang) === false) {
                    throw new Exception("Language Invalid: $language");
                }
            }
        }
    }

    /**
     * Validate a language via simple regex.
     *
     * Return true/false depending on outcome (alphabetic 1-8 chars long)
     *
     * @param string $language Language to validate.
     *
     * @return bool
     */
    private function _validateLanguageRegex($language)
    {
        $valid === filter_var(
            $language,
            FILTER_VALIDATE_REGEXP,
            array('options' => array('regexp' => '/^[a-z]{1,8}$/i'))
        );
        if ($valid !== false) {
            return true;
        }
        return false;
    }

    /**
     * Connect to specified server.
     *
     * @param string $server base server details, e.g. http://api.openstreetmap.org
     *
     * @return Services_OpenStreetMap
     * @throws Services_OpenStreetMap_Exception If valid response isn't received.
     */
    public function setServer($server)
    {
        try {
            $c = $this->getTransport()->getResponse($server . '/api/capabilities');
        } catch (Exception $ex) {
            throw new Services_OpenStreetMap_Exception(
                'Could not get a valid response from server',
                $ex->getCode(),
                $ex
            );
        }
        $this->server = $server;
        $capabilities = $c->getBody();
        if (!$this->_checkCapabilities($capabilities)) {
            throw new Services_OpenStreetMap_Exception(
                'Problem checking server capabilities'
            );
        }
        $this->config['server'] = $server;

        return $this;
    }

    /**
     * Set and parse a password file, setting username and password as specified
     * in the file.
     *
     * A password file is a ASCII text file, with username and passwords pairs
     * on each line, separated [delimited] by a semicolon.
     * Lines starting with a hash [#] are comments.
     * If only one non-commented line is present in the file, that username and
     * password will be used for authentication.
     * If more than one set of usernames and passwords are present, the
     * username must be specified, and the matching password from the file will
     * be used.
     *
     * <pre>
     * # Example password file.
     * fredfs@example.com:Wilma4evah
     * barney@example.net:B3ttyRawks
     * </pre>
     *
     * @param string $file file containing credentials
     *
     * @return Services_OpenStreetMap
     */
    public function setPasswordfile($file)
    {
        if (is_null($file)) {
            return $this;
        }
        $lines = @file($file);
        if ($lines === false) {
            throw new Services_OpenStreetMap_Exception(
                'Could not read password file'
            );
        }
        $this->config['passwordfile'] =  $file;
        array_walk($lines, create_function('&$val', '$val = trim($val);'));
        if (sizeof($lines) == 1) {
            if (strpos($lines[0], '#') !== 0) {
                list($this->config['user'], $this->config['password'])
                    = explode(':', $lines[0]);
            }
        } elseif (sizeof($lines) == 2) {
            if (strpos($lines[0], '#') === 0) {
                if (strpos($lines[1], '#') !== 0) {
                    list($this->config['user'], $this->config['password'])
                        = explode(':', $lines[1]);
                }
            }
        } else {
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue;
                }
                list($user, $pwd) = explode(':', $line);
                if ($user == $this->config['user']) {
                    $this->config['password'] = $pwd;
                }
            }
        }
        return $this;
    }

    /**
     * Set the Transport instance.
     *
     * @param Services_OpenStreetMap_Transport $transport Transport instance.
     *
     * @return Services_OpenStreetMap_Config
     */
    public function setTransport(Services_OpenStreetMap_Transport $transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * Retrieve the current Transport instance.
     *
     * @return Services_OpenStreetMap_Transport.
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Return all config settings in an array.
     *
     * @return array
     */
    public function asArray()
    {
        return $this->config;
    }

    /**
     * Set various properties to describe the capabilities that the connected
     * server supports.
     *
     * @param mixed $capabilities XML describing the capabilities of the server
     *
     * @see maxVersion
     * @see minVersion
     * @see timeout
     *
     * @return boolean
     *
     * @internal
     * @throws   Services_OpenStreetMap_Exception If the API Version is not
     *                                            supported.
     */
    private function _checkCapabilities($capabilities)
    {
        $xml = simplexml_load_string($capabilities);
        if ($xml === false) {
            return false;
        }

        $this->minVersion = (float) $this->getXmlValue($xml, 'version', 'minimum');
        $this->maxVersion = (float) $this->getXmlValue($xml, 'version', 'maximum');
        if (($this->minVersion > $this->api_version
            || $this->api_version > $this->maxVersion)
        ) {
            throw new Services_OpenStreetMap_Exception(
                'Specified API Version ' . $this->api_version .' not supported.'
            );
        }
        $this->timeout = (int) $this->getXmlValue($xml, 'timeout', 'seconds');

        //changesets
        $this->changesetMaximumElements = (int) $this->getXmlValue(
            $xml,
            'changesets',
            'maximum_elements'
        );

        // Maximum number of nodes per way.
        $this->waynodesMaximum = (int) $this->getXmlValue(
            $xml,
            'waynodes',
            'maximum'
        );

        // Number of tracepoints per way.
        $this->tracepointsPerPage = (int) $this->getXmlValue(
            $xml,
            'tracepoints',
            'per_page'
        );

        // Max size of area that can be downloaded in one request.
        $this->areaMaximum = (float) $this->getXmlValue(
            $xml,
            'area',
            'maximum'
        );

        $this->databaseStatus = $this->getXmlValue(
            $xml,
            'status',
            'database'
        );

        $this->apiStatus = $this->getXmlValue(
            $xml,
            'status',
            'api'
        );

        $this->gpxStatus = $this->getXmlValue(
            $xml,
            'status',
            'gpx'
        );

        // What generated the XML.
        $this->generator = '' . $this->getXmlValue(
            $xml,
            'osm',
            'generator',
            'OpenStreetMap server'
        );

        return true;
    }

    /**
     * Max size of area that can be downloaded in one request.
     *
     * Use like:
     * <code>
     * $osm = new Services_OpenStreetMap();
     * $area_allowed = $osm->getMaxArea();
     * </code>
     *
     * @return float
     */
    public function getMaxArea()
    {
        return $this->areaMaximum;
    }

    /**
     * Minimum API version supported by connected server.
     *
     * Use like:
     * <code>
     * $config = array('user' => 'fred@example.net', 'password' => 'wilma4eva');
     * $osm = new Services_OpenStreetMap($config);
     * $min = $osm->getMinVersion();
     * </code>
     *
     * @return float
     */
    public function getMinVersion()
    {
        return $this->minVersion;
    }

    /**
     * Maximum API version supported by connected server.
     *
     * Use like:
     * <code>
     * $config = array('user' => 'fred@example.net', 'password' => 'wilma4eva');
     * $osm = new Services_OpenStreetMap($config);
     * $max = $osm->getMaxVersion();
     * </code>
     *
     * @return float
     */
    public function getMaxVersion()
    {
        return $this->maxVersion;
    }

    /**
     * Return the number of seconds that must elapse before a connection is
     * considered to have timed-out.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Maximum number of tracepoints per page.
     *
     * Use like:
     * <code>
     * $osm = new Services_OpenStreetMap();
     * $tracepoints = $osm->getTracepointsPerPage();
     * </code>
     *
     * @return float
     */
    public function getTracepointsPerPage()
    {
        return $this->tracepointsPerPage;
    }

    /**
     * Maximum number of nodes per way.
     *
     * Anymore than that and the way must be split.
     *
     * <code>
     * $osm = new Services_OpenStreetMap();
     * $max = $osm->getMaxNodes();
     * </code>
     *
     * @return float
     */
    public function getMaxNodes()
    {
        return $this->waynodesMaximum;
    }

    /**
     * Maximum number of elements allowed per changeset.
     *
     * Use like:
     * <code>
     * $osm = new Services_OpenStreetMap();
     * $max = $osm->getMaxElements();
     * </code>
     *
     * @return float
     */
    public function getMaxElements()
    {
        return $this->changesetMaximumElements;
    }

    /**
     * Status of the OSM database (offline/readonly/online).
     *
     * @return null|string
     */
    public function getDatabaseStatus()
    {
        return $this->databaseStatus;
    }

    /**
     * Status of the main OSM API (offline/readonly/online).
     *
     * @return null|string
     */
    public function getApiStatus()
    {
        return $this->apiStatus;
    }

    /**
     * Status of the OSM GPX API (offline/readonly/online).
     *
     * @return null|string
     */
    public function getGpxStatus()
    {
        return $this->gpxStatus;
    }

    /**
     * Name of what generated the Capabilities XML.
     *
     * @return string
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Given SimpleXMLElement, retrieve tag value.
     *
     * @param SimpleXMLElement $xml       Object
     * @param string           $tag       name of tag
     * @param string           $attribute name of attribute
     * @param mixed            $default   default value, optional
     *
     * @return string
     */
    public function getXmlValue(
        SimpleXMLElement $xml,
        $tag,
        $attribute,
        $default = null
    ) {
        $obj = $xml->xpath('//' . $tag);
        if (empty($obj)) {
            return $default;
        }
        return $obj[0]->attributes()->$attribute;
    }
}

?>
