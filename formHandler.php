<?php



class Entry{
    /**
     * @var DateTime
     */
    private $_created_at;
    private $_firstname;
    private $_lastname;
    private $_mail;
    private $_phone;
    private $_postcode;
    /**
     * @var boolean
     */
    private $_info;
    /**
     * @var boolean
     */
    private $_help;
    /**
     * @var array
     */
    private $_extra;

    public function __construct()
    {
        $this->_created_at = '';
        $this->_firstname = '';
        $this->_lastname = '';
        $this->_mail = '';
        $this->_phone = '';
        $this->_postcode = '';
        $this->_info = false;
        $this->_help = false;
        $this->_extra = json_encode([]);
    }

    /**
     * @param $post
     * @return $this
     */
    public function initFromPost($post){
        $this->_created_at = new DateTime('now');
        $this->_firstname = $post['firstname'];
        $this->_lastname = $post['lastname'];
        $this->_mail = $post['mail'];
        $this->_phone = $post['telephone'];
        $this->_postcode = $post['postcode'];
        $this->_info = $post['more_info'] == 'on';
        $this->_help = $post['help'] == 'on';
        $extra = [];
        foreach ($post as $key => $val){
            if (!in_array($key,['firstname','lastname','mail','telephone','postcode','help','more_indo','redirect_path'])){
                $extra[$key] = $val;
            }
        }
        $this->_extra = $extra;
        return $this;
    }

    /**
     * @param $row
     * @return $this
     */
    public function initFromRow($row){
        $this->_created_at = DateTime::createFromFormat(DateTime::ATOM,$row[0]);
        $this->_firstname = $row[1];
        $this->_lastname = $row[2];
        $this->_mail = $row[3];
        $this->_phone = $row[4];
        $this->_postcode = $row[5];
        $this->_info = $row[6];
        $this->_help = $row[7];
        $this->_extra = json_decode($row[8]);
        return $this;
    }

    public function toArray(){
        return [
            $this->getCreatedAt()->format(DateTime::ATOM),
            $this->getFirstname(),
            $this->getLastname(),
            $this->getMail(),
            $this->getPhone(),
            $this->getPostcode(),
            $this->getInfo(),
            $this->getHelp(),
            json_encode($this->getExtra())
            ];
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(){
        return $this->_created_at;
    }


    public function getFirstname(){
        return $this->_firstname;
    }

    public function getLastname(){
        return $this->_lastname;
    }

    public function getMail(){
        return $this->_mail;
    }

    public function getPhone(){
        return $this->_phone;
    }

    public function getPostcode(){
        return $this->_postcode;
    }
    
    public function getInfo(){
        return $this->_info;
    }
    
    public function getHelp(){
        return $this->_help;
    }

    public function getExtra(){
        return $this->_extra;
    }

    static function getHeader(){
        return ["created_at","firstname","lastname",'mail','phone','postcode','info','help','extra'];
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

        if (!filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email format";
            return false;
        }
        return true;
    }
}

class EntryCsv{
    private $_filename;
    /**
     * @var Entry[]
     */
    private $_entries;
    private $_with_header;

    public function __construct($_filename,$_with_header)
    {
        $this->_entries = [];
        $this->_filename = $_filename;
        $this->_with_header = $_with_header;
    }

    public function addEntry(Entry $entry){
        if (file_exists($this->_filename)){
            //Open the file.
            $fileHandle = fopen($this->_filename, "r");
            $i = 0;
            //Loop through the CSV rows.
            while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
                if ($i++===0 && $this->_with_header){
                    continue;
                }
                //Dump out the row for the sake of clarity.
                $this->_entries[] = Entry::fromRow($row);
            }
            fclose($fileHandle);
        }
        $this->_entries[] = $entry;
        // open csv file for writing
        $fileHandle = fopen($this->_filename, 'w');
        if ($fileHandle === false) {
            die('Error opening the file ' . $this->_filename);
        }
        if ($this->_with_header){
            fputcsv($fileHandle, Entry::getHeader());
        }
        // write each row at a time to a file
        foreach ($this->_entries as $entry) {
            fputcsv($fileHandle, $entry->toArray());
        }
        // close the file
        fclose($fileHandle);
    }
}

if (isset($_POST['name'])&&strlen($_POST['name'])){
    //honey pot
}else{
    $filename = "data/data.csv";
    if (Entry::checkPost($_POST)){
        $entry = new Entry();
        $entry->initFromPost($_POST);
        $csv = new EntryCsv($filename,true);
        $csv->addEntry($entry);
    }
}

if (isset($_POST['redirect_path']))
    header('Location: '. $_SERVER['HTTP_HOST'].$_POST['redirect_path']);
else
    header('Location: /');
die();