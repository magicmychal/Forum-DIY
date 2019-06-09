<?php


class AdminController extends Controller
{
    public static function get_user()
    {
        if (!isset($_POST['txtSearch'])) {
            echo '{"status":"0","message":"Field cannot be empty"}';
            exit;
        }
        $sUserToFind = $_POST['txtSearch'];
        $query = new Users;
        $searchResults = $query->get_users($sUserToFind);
        $return = new stdClass();
        if (!$searchResults) {
            $return->status = 0;
            $return->message = 'No results, try again';
            echo json_encode($return);
            exit;
        }
        $return->status = 1;
        $return->message = 'All good';
        $return->data = $searchResults;
        echo json_encode($return);

    }

    public static function update_user_basics()
    {
        if (!hash_equals($_SESSION['key'], $_POST['token'])) {
            echo '{"status":"0","message":"Invalid token"}';
            exit;
        }

        $iUserId = $_POST['iUserId'];
        $iActiveStatus = $_POST['iActive'];
        $iRole = $_POST['iRole'];

        $user = new Users();
        $userUpdate = $user->update_user_basics($iUserId, $iActiveStatus, $iRole);

        if($userUpdate === true) {
            echo '{"status":"1", "message":"User updated" }';
        } else {
            echo '{"status":"0","message":"Internal Server Error. Nothing was changed"';
            exit;
        }

    }

}