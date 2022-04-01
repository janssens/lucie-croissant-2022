<?php

require __DIR__.'/vendor/autoload.php';

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

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

    public function toHtml(){
        return 'reçu le '.
            $this->getCreatedAt()->format('d M Y').' à '.
            $this->getCreatedAt()->format('H:i:s').'<br />'.
            '<b>Nom / Prenom :</b> '.$this->getFirstname().' / '.$this->getLastname().'<br />'.
            '<b>Email :</b> '.$this->getMail().'<br />'.
            '<b>Tel :</b> '.$this->getPhone().'<br />'.
            '<b>CP :</b> '.$this->getPostcode().'<br />'.
            (($this->getInfo()) ? 'Souhaite des infos' : 'Ne souhaite pas d&rsquo;info').'<br />'.
            (($this->getHelp()) ? 'Souhaite donner de l&rsquo;aider' : 'Ne souhaite pas aider').
            (($this->getExtra() && isset($this->getExtra()['superpower']) && $this->getExtra()['superpower']) ? ('<br />'.'<b>super pouvoir :</b> '.$this->getExtra()['superpower'].'<br />') : '').'<br />'.
            'en csv :'.'<br />'.
            '<small>'.implode(',',$this->getHeader()).'</small>'.'<br />'.
            '<small>'.implode(',',$this->toArray()).'</small>';
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

        foreach (['firstname','lastname','mail','postcode'] as $key){
            if (!isset($post[$key]))
                return false;
        }

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
    $errormsg = "honey pot";
}else{
    $filename = "data/data.csv";
    if (Entry::checkPost($_POST)){
        // create the entry
        $entry = new Entry();
        $entry->initFromPost($_POST);
        //write on csv
        $csv = new EntryCsv($filename,true);
        $csv->addEntry($entry);

        //write csv on webdav
        $settings = array(
            'baseUri' => $_ENV['WEBDAV_URL'],
            'userName' => $_ENV['WEBDAV_USERNAME'],
            'password' => $_ENV['WEBDAV_PASSWORD']
        );
        $client = new Sabre\DAV\Client($settings);
        $data = file_get_contents($filename);
        $upload_result = $client->request('PUT', $_ENV['WEBDAV_DEST_FILENAME'], $data);

        //send mail as backup
        $mail = new PHPMailer(true);
        try {
            // User smtp access to configure PhpMailer for MailSlurp
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = $_ENV['SMTP_SERVER_HOST'];
            $mail->Port       = $_ENV['SMTP_SERVER_PORT'];
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = '';

            // write email from inbox1 to inbox2
            $mail->setFrom($_ENV['SMTP_FROM']);
            foreach (explode(',',$_ENV['SMTP_TO']) as $to)
                $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = "[".$_ENV['SITE_NAME']."] nouvelle signature";
            $mail->Body    = $entry->toHtml();

            // send the email
            $sent = $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            throw new Exception($mail->ErrorInfo);
        }
    }else{
        $errormsg = 'entry not valid';
    }
}

header('x-debug-errormsg: '.$errormsg);

if (isset($_POST['redirect_path']))
    header('Location: http'. (($_SERVER['HTTPS']) ? 's' : '') .'://'. $_SERVER['HTTP_HOST'].$_POST['redirect_path']);
else
    header('Location: /');
exit();