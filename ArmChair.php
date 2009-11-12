<?php
/**
 * HTTP_Request2
 * @ignore
 */
require_once 'HTTP/Request2.php';

/**
 * ArmChair - a very simple wrapper for CouchDB!
 *
 * @category Database
 * @package  ArmChair
 * @author   Till Klampaeckel
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  0.1.0
 * @link     http://github.com/till/armchair
 */
class ArmChair extends HTTP_Request2
{
    /**
     * @var string Url, including port and database name.
     */
    protected $server;

    /**
     * __construct
     *
     * @param string $server Url, including port and database name.
     *
     * @return $this
     * @uses   parent::__construct()
     */
    public function __construct($server)
    {
        $this->server = $server;
        parent::__construct($server);

        // force this for all calls
        $this->setHeader('Content-type: application/json; charset=utf-8');
    }

    /**
     * Return a document by ID, or all documents.
     *
     * @param mixed $id Null for all documents, or a string for a specific one.
     *
     * @return array
     */
    public function get($id = null)
    {
        if ($id === null) {
            $this->setUrl($this->server . '/_all_docs');
        } else {
            $id = urlencode($id);
            $this->setUrl($this->server . '/' . $id);
        }
        $this->setMethod(HTTP_Request2::METHOD_GET);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Add a new document to the database.
     *
     * If the $data array contains an _id key, we PUT, otherwise CouchDB will take
     * care of it for us.
     *
     * @param array $data The new document.
     *
     * @return array
     */
    public function addDocument(array $data)
    {
        if (isset($data['_id'])) {
            $id = urlencode($data['_id']);
            unset($data['_id']);
            $this->setUrl($this->server . '/' . $id);
            $this->setMethod(HTTP_Request2::METHOD_PUT);
        } else {
            $this->setUrl($this->server);
            $this->setMethod(HTTP_Request2::METHOD_POST);
        }
        $this->setBody(json_encode($data));

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Delete a document by ID.
     *
     * @param string $id  The document's ID.
     * @param string $rev The document's revision.
     *
     * @return mixed False if no ID was provided, otherwise an array.
     */
    public function deleteDocument($id, $rev)
    {
        $id = trim($id);
        if (empty($id)) {
            return false;
        }
        $id  = urlencode($id);
        $rev = urlencode($rev);
        $this->setUrl($this->server . '/' . $id . '?rev=' . $rev);
        $this->setMethod(HTTP_Request2::METHOD_DELETE);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Update a document with new data.
     *
     * @param string $id   The document's ID.
     * @param array  $data New data to save, must contain a _rev key.
     *
     * @return mixed False if no ID was provided, otherwise an array.
     */
    public function updateDocument($id, $data)
    {
        $id = trim($id);
        if (empty($id)) {
            return false;
        }
        if (!isset($data['_rev'])) {
            return false;
        }
        $id = urlencode($id);

        $this->setUrl($this->server . '/' . $id);
        $this->setMethod(HTTP_Request2::METHOD_PUT);
        $this->setBody(json_encode($data));

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Parse the response. This will do a little more in future iterations.
     *
     * @param HTTP_Request2_Response $response Response object.
     *
     * @return array
     */
    protected function parseResponse(HTTP_Request2_Response $response)
    {
        return json_decode($response->getBody());
    }
}
