<?php
    Class AuthGuards{
        public static function checkSession(){
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || !isset($_SESSION['admin_role'])) {
                error_log('Unauthorized access attempt: ' . print_r($_SESSION, true));
                header("Location: ../index.php");
                exit();
            }
        }
    }

?>