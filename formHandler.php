<?php

class Entry{
    private $_firstname;
    private $_lastname;
    private $_mail;
    private $_phone;
    private $_postcode;
    private $_help;

    public function __construct()
    {
        $this->_firstname = '';
        $this->_lastname = '';
        $this->_mail = '';
        $this->_phone = '';
        $this->_postcode = '';
        $this->_help = false;
    }

    /**
     * @param $post
     * @return $this
     */
    public function initFromPost($post){
        $this->_firstname = $post['firstname'];
        $this->_lastname = $post['lastname'];
        $this->_mail = $post['mail'];
        $this->_phone = $post['telephone'];
        $this->_postcode = $post['postcode'];
        $this->_help = $post['help'] == 'on';
        return $this;
    }

    /**
     * @param $row
     * @return $this
     */
    public function initFromRow($row){
        return $this;
    }

    public function toArray(){
        return [$this->getFirstname(),$this->getLastname()];
    }

    public function getFirstname(){
        return $this->_firstname;
    }

    public function getLastname(){
        return $this->_lastname;
    }

    /**
     * @param $row
     * @return static
     */
    static function fromRow($row){
        $entry = new Entry();
        return $entry->initFromRow($row);
    }

    /**
     * @param $post
     * @return bool
     */
    static function checkPost($post){

        foreach (['firstname','lastname','mail','postcode','rgpd'] as $key){
            if (!isset($post[$key]))
                return false;
        }

        if ($post['rgpd']!='on')
            return false;

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email format";
            return false;
        }
        return true;
    }
}

if (isset($_POST['name'])&&strlen($_POST['name'])){
    return '';
}else{
    $filename = "data/data.csv";
    $data = [];
    if (file_exists($filename)){
        //Open the file.
        $fileHandle = fopen($filename, "r");
        //Loop through the CSV rows.
        while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
            //Dump out the row for the sake of clarity.
            $data[] = Entry::fromRow($row);
        }
        fclose($fileHandle);
        var_dump($data);
        die();
    }

    if (Entry::checkPost($_POST)){
        $entry = new Entry();
        $entry->initFromPost($_POST);
        $data[] = $entry;
    }

    // open csv file for writing
    $fileHandle = fopen($filename, 'w');
    if ($fileHandle === false) {
        die('Error opening the file ' . $filename);
    }
    // write each row at a time to a file
    foreach ($data as $entry) {
        fputcsv($fileHandle, $entry->toArray());
    }
    // close the file
    fclose($fileHandle);
}