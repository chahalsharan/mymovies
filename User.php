<?php require_once 'globals.php' ?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'password.php' ?>
<?php require_once 'swift/lib/swift_required.php' ?>

<?php
    class User{
        public $id;
        public $userName;
        public $password;
        public $email;
        public $confirmationCode;
        public $status;
        public $createdOn;
        public $updatedOn;
        public $type;

        public function __construct($userId = NULL) {
            $this->id = $userId;
        }

        public function saveOrUpdate($db){
            if (isset($this->id)) {
                $existingUserId = $this->id;
            } else {
                $existingUserId = User::existsInDb($db, $this->userName, $this->email);
            }

            if (isset($existingUserId)) {
                error_log("DEBUG: User exists, going to update user:" . $existingUserId);
                $user = User::loadFromDb($db, $existingUserId);
                $user = $user->update($db, $this);
                return $user;
            }else{
                $this->save($db);
                return $this;
            }
        }

        public function update($db, $updatedUser) {
            $data = array();
            $upd = FALSE;
            if(isset($updatedUser->password)){
                $data["password"] = $updatedUser->password;
                $upd = TRUE;
            }
            if(isset($updatedUser->email)){
                $data["email"] = $updatedUser->email;
                $upd = TRUE;
            }
            if(isset($updatedUser->confirmationCode)){
                $data["confirmation_code"] = $updatedUser->confirmationCode;
                $upd = TRUE;
            }
            if(isset($updatedUser->status)){
                $data["status"] = $updatedUser->status;
                $upd = TRUE;
            }

            if($upd){
                $date = date('Y-m-d H:i:s', time());
                $data["updated_on"] = $date;
                $db ->where('id', $this->id)
                    ->update('users', $data);
            }
            return $updatedUser;
        }

        public function save($db) {
            $dt = date('Y-m-d H:i:s', time());
            $data = Array ("user_name" => urlencode($this->userName),
                           "email" => urlencode($this->email),
                           "password" => password_hash($this->password, PASSWORD_DEFAULT),
                           "confirmation_code" => $this->confirmationCode,
                           "created_on" => $dt,
                           "updated_on" => $dt,
                           "status" => USER_STATUS::PENDING,
                           "type" => "user"
            );
            $this->id = $db->insert('users', $data);
            error_log("DEBUG: user created :" . $this->id);
            return $this;
        }

        public function validate($password){
            return password_verify($password, $this->password);
        }

        public function isActive(){
            if(constant('USER_STATUS::' . $this->status) === USER_STATUS::ACTIVE){
                return TRUE;
            }else{
                return FALSE;
            }
        }

        public function setSession(){
            session_start();
            $_SESSION['userId'] = $this->id;
            if($this->type == "admin"){
                $_SESSION['type'] = "admin";
            }
            $_SESSION['userName'] = $this->userName;
        }

        public static function unSetSession(){
            session_start();
            unset($_SESSION['userId']);
            unset($_SESSION['type']);
            unset($_SESSION['userName']);
        }

        public function sendConfirmationEmail(){
            global $SITE_URL;
            $sendto = $this->email; // this is the email address collected form the form 
            $emailTitle = "Account confirmation"; 
            $confirmationLink = $SITE_URL . "confirmAccount.php?userId=" . $this->id . "&confirmationCode=" . $this->confirmationCode;

            $message = file_get_contents('email_lite2.php');
            $header = "From: noreply-mymovies@gmail.com\r\n"; 
            $header .= "Reply-to: noreply-mymovies@gmail.com\r\n"; 
            mail($sendto, $emailTitle, $message, $header);  

            $emailTitle = "Account confirmation email";
            $vars = array(
              '{$emailTitle}' => "Account confirmation email",
              '{$siteUrl}'    => $SITE_URL,
              '{$confLink}'   => $confirmationLink 
            );
            error_log("DEBUG: confirmation link:" . $confirmationLink);
            $message = file_get_contents('email_lite2.php');
            foreach ($vars as $key => $value) {
                $message = str_replace($key, $value, $message);  
            }

            $header = "From: webspheresolutions@gmail.com\r\n"; 
            $header .= "Reply-to: webspheresolutions@gmail.com\r\n"; 


            $transport = Swift_SmtpTransport::newInstance('ssl://smtp.gmail.com', 465)
              ->setUsername('webspheresolutions@gmail.com')
              ->setPassword('DiscoSingh');

            $mailer = Swift_Mailer::newInstance($transport);

            $message = Swift_Message::newInstance($emailTitle)
              ->setFrom(array('webspheresolutions@gmail.com' => 'MyMovies'))
              ->setTo(array($this->email))
              ->setBody("This is account confirmation email")
              ->addPart($message, 'text/html');

            // Send the message
            if ($mailer->send($message)) {
                error_log("DEBUG: Mail sent successfully.");
            } else {
                error_log("DEBUG: Not able to send email. I am sure, the configuration are not correct.");
            }
        }

        public function confirmCode($db, $code){
            error_log("DEBUG: dbCode:" . $this->confirmationCode . ", userCode:" . $code);
            if($this->confirmationCode === $code){
                $this->status = USER_STATUS::ACTIVE;
                $this->confirmationCode = "";
                $this->saveOrUpdate($db);
                return TRUE;
            }
            return FALSE;
        }

        public function addToWatchList($db, $itemId=FALSE){
            if($itemId){
                error_log("DEBUG: Going add: " . $itemId . " to userId:" . $this->id);
                $data = Array ("user_id" => urlencode($this->id),
                           "movie_id" => urlencode($itemId));
                $db->insert('user_watchlist', $data);
            }
        }

        public function removeFromWatchList($db, $itemId=FALSE){
            if($itemId){
                error_log("DEBUG: Going to delete: " . $itemId . " from userId:" . $this->id);
                $db ->where("user_id", $this->id)
                    ->where("movie_id", $itemId)
                    ->delete('user_watchlist', $data);
            }
        }

        public function getWatchList($db){
            $watchingArr = array();
            $watching = $db ->join("movies m", "m.id = uw.movie_id")
                            ->join("links l", "l.movie_id = m.id", "LEFT")
                            ->where("uw.user_id", $this->id)
                            ->groupBy("m.id, m.name")
                            ->get("user_watchlist uw", null, "m.id, m.name, l.link_id");
            error_log("DEBUG: Query: " . $db->getLastQuery());
            foreach ($watching as $w) {
                $watchingArr[$w['id']] = urldecode($w['name']);
                if(isset($w['link_id'])){
                    $watchingArr[$w['id'] . "_featured"] = urldecode($w['name']);
                }
            }
            return $watchingArr;
        }

        public static function loadFromDb($db, $id) {
            if(isset($id)){
                $userDB = $db ->where("id", $id)
                            ->getOne("users");
                $user = new User();
                $user->id = $userDB['id'];
                $user->userName = $userDB['user_name'];
                $user->email = $userDB['email'];
                $user->confirmationCode = $userDB['confirmation_code'];
                $user->status = $userDB['status'];
                $user->createdOn = $userDB['created_on'];
                $user->updatedOn = $userDB['updated_on'];
                $user->password = $userDB['password'];
                $user->type = $userDB['type'];

                error_log("DEBUG: status: " . $user->status);
                $user->status = USER_STATUS::getConst($user->status);
                error_log("DEBUG: user status " . $user->status);
                return $user;
            }
        }
        public static function existsInDb($db, $userName=NULL, $email=NULL){
            if($userName && $email){
                $dbId = $db ->where("user_name", urlencode($userName))
                            ->orWhere("email", urlencode($email))
                            ->getOne("users", "id");
                if ($db->count  > 0) {
                    return $dbId['id'];
                }
            }if($userName){
                $dbId = $db->where("user_name", urlencode($userName))
                              ->getOne("users", "id");
                if ($db->count  > 0) {
                    return $dbId['id'];
                }
            }else if($email){
                $dbId = $db->where("email", urlencode($email))
                              ->getOne("users", "id");
                if ($db->count  > 0) {
                    return $dbId['id'];
                }
            }
            return;
        }

        public static function generateConfirmationCode(){
            $random_hash = substr(md5(uniqid(rand(), true)), 16, 16);
            error_log("DEBUG: random hash: " . $random_hash);
            return $random_hash;
        }
    }

    abstract class USER_STATUS {
        const ACTIVE = "active";
        const PENDING = "pending";
        const BLOCKED = "blocked";

        private static $constCache = NULL;

        private static function getConstants() {
            if (self::$constCache === NULL) {
                $reflect = new ReflectionClass(get_called_class());
                self::$constCache = $reflect->getConstants();
            }
            return self::$constCache;
        }

        public static function getConstValue($byName) {
            $constants = self::getConstants();
            return $constants[$byName];
        }

        public static function getConst($byValue) {
            $constants = self::getConstants();
            $values = array_values(self::getConstants());
            $keys = array_keys(self::getConstants());
            $index = array_search($byValue, $values);
            if($index >= 0){
                return $keys[$index];
            }
        }
    }
?>