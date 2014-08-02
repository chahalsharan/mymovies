<?php require_once 'mysqliDb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'User.php' ?>
<?php require_once 'globals.php' ?>

<?php
    session_start();
    $db = getMysqlConnection();
    $action=FALSE;
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }else if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    if($action){
        switch ($action) {
            case 'createUser':
                // Sanitize incoming username and password
                $username = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
                $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
                createNewUser($db, $username, $_POST['email'], $password);
                break;
            case 'login':
                // Sanitize incoming username and password
                $username = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
                $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
                login($db, $username, $password);
                break;
            case 'logout':
                logout($db);
                break;
            case 'addToWatchList':
                $movieId = filter_var($_POST['movieId'], FILTER_SANITIZE_STRING);
                addToWatchList($db, $movieId);
                break;
            case 'removeFromWatchList':
                $movieId = filter_var($_POST['movieId'], FILTER_SANITIZE_STRING);
                removeFromWatchList($db, $movieId);
                break;
            default:
                http_response_code(500);
                echo "Invalid request";
        }
    }else{
        http_response_code(500);
        echo "Invalid request";
    }
    closeMysqlConnection($db);

    function logout($db){
        User::unSetSession();
        header("Location:letMeWatchThis.php");
        exit();
    }

    function addToWatchList($db, $movieId){
        $userId = NULL;
        if(isset($_SESSION['userId']) && $movieId){
            $userId = $_SESSION['userId'];
            $user = User::loadFromDb($db, $userId);
            if($user){
                $user->addToWatchList($db, $movieId);
            }else{
                error_log("DEBUG: User not found for id " . $userId);
            }
        }else{
            error_log("DEBUG: User not logged in");
        }
    }

    function removeFromWatchList($db, $movieId){
        $userId = NULL;
        if(isset($_SESSION['userId']) && $movieId){
            $userId = $_SESSION['userId'];
            $user = User::loadFromDb($db, $userId);
            if($user){
                $user->removeFromWatchList($db, $movieId);
            }else{
                error_log("DEBUG: User not found for id " . $userId);
            }
        }else{
            error_log("DEBUG: User not logged in");
        }
    }

    function login($db, $userName, $password){
        error_log("DEBUG: logging in username: " . $userName);
        error_log("DEBUG: logging in password: " . $password);
        $userId = User::existsInDb($db, $userName, $userName);
        $user = new User();
        http_response_code(500);
        if(! isset($userId)){
            error_log("DEBUG: User does not exist: " . $userName);
            echo "User does not exists";
        }else{
            $user = User::loadFromDb($db, $userId);
            if(isset($user) && $user->validate($password)){
                if($user->isActive()){
                    error_log("DEBUG: login successful");
                    http_response_code(200);
                    $user->setSession();
                }else{
                    echo "User not activated yet.";
                }
            }else{
                echo "Login failed. Check username/password.";
            }
        }
    }

    function createNewUser($db, $userName, $email, $password){
        $user = new User();
        if(! User::existsInDb($db, $userName, $password)){
            error_log("DEBUG: creating new user for username: " . $userName);
            $user->userName = $userName;
            $user->email = $email;
            $user->password = $password;
            $user->confirmationCode = User::generateConfirmationCode();
            $user->save($db);
            $user->sendConfirmationEmail();
        }else{
            error_log("DEBUG: user already exists for username: " . $userName);
            http_response_code(500);
            echo "User already exists";
        }
    }
?>