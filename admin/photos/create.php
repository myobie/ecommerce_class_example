<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";
$db = new DB();

$photo = new Photo($_POST["photo"]);
$photo->update(array("tmpfile" => $_FILES['file']));
$photo->save();

header('Location: /admin/photos/');

?>