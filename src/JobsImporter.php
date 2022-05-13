<?php

class JobsImporter
{
    private $db;

    private $file;

    public function __construct($host, $username, $password, $databaseName, $file)
    {
        $this->file = $file;
        
        /* connect to DB */
        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName, $username, $password);
        } catch (Exception $e) {
            die('DB error: ' . $e->getMessage() . "\n");
        }
    }

    public function importJobs()
    {
        /* remove existing items */
        $this->db->exec('DELETE FROM job');

        /* parse XML file */
        $xml = simplexml_load_file($this->file);

        /* import each item */
        $count = 0;
        foreach ($xml->item as $item) {
            $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                . '\'' . addslashes($item->ref) . '\', '
                . '\'' . addslashes($item->title) . '\', '
                . '\'' . addslashes($item->description) . '\', '
                . '\'' . addslashes($item->url) . '\', '
                . '\'' . addslashes($item->company) . '\', '
                . '\'' . addslashes($item->pubDate) . '\')'
            );
            $count++;
        }
        /* parse json file */
        $xml = simplejson_load_file($this->file);

        /* import each item */
        $count = 0;
        foreach ($json->item as $item) {
            $this->db->exec('INSERT INTO job (link, title, description, reference, publishedDate, companyname) VALUES ('
                . '\'' . addslashes($item->link) . '\', '
                . '\'' . addslashes($item->title) . '\', '
                . '\'' . addslashes($item->description) . '\', '
                . '\'' . addslashes($item->reference) . '\', '
                . '\'' . addslashes($item->publishedDate) . '\', '
                . '\'' . addslashes($item->companyname) . '\')'
            );
            $count++;
        }
        return $count;
    }
}
