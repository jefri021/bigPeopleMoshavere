<?php
    $array_of_names = array();
    $array_of_messages = array();
    $file_message = fopen("messages.txt", "r");
    for ($i = 0; !feof($file_message); $i++){
        $array_of_messages[$i] = fgets($file_message);
    }
    $input = file_get_contents('people.json');
    $people = json_decode($input);
    $counter = 1;
    foreach ($people as $key => $value) {
        $array_of_names[$counter] = $key;
        $counter++;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $en_name = $_POST['person'];
        foreach($people as $key => $value) {
            if ($key == $en_name) {
                $fa_name = $value;
                break;
            }
        }
        $question = $_POST['question'];
        $start = "/^آیا/iu";
        $end1 = "/\?$/i";
        $end2 = "/؟$/u";
        if (!(preg_match($start, $question) and (preg_match($end1, $question) or preg_match($end2, $question)))) {
            $msg = 'سوال درستی پرسیده نشده';
        }
        else {
        $hashed = hash('adler32', $question." ".$en_name);
        $hashed = hexdec($hashed);
        $randomfinder = ($hashed % 16);
        $msg = $array_of_messages[$randomfinder];
        }
    }
    else {
        $msg = "!سوال خود را بپرس";
        $question = '';
        $random = array_rand($array_of_names);
        $en_name = $array_of_names[$random];
        foreach($people as $key => $value){
            if ($key == $en_name){
                $fa_name = $value;
                break;
            }
        }
    }
    if (empty($question)){
        $flag = '';
        $msg = "سوال خود را بپرس";
    }
    else {
        $flag = 'پرسش:';
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles/default.css">
    <title>مشاوره بزرگان</title>
</head>

<body>
    <p id="copyright">تهیه شده برای درس کارگاه کامپیوتر،دانشکده کامییوتر، دانشگاه صنعتی شریف</p>
    <div id="wrapper">
        <div id="title">
            <span id="label"><?php echo $flag ?></span>
            <span id="question">
                <?php echo $question ?>
            </span>
        </div>
        <div id="container">
            <div id="message">
                <p>
                    <?php echo $msg ?>
                </p>
            </div>
            <div id="person">
                <div id="person">
                    <img src="images/people/<?php echo "$en_name.jpg" ?>"/>
                    <p id="person-name">
                        <?php echo $fa_name ?>
                    </p>
                </div>
            </div>
        </div>
        <div id="new-q">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
                سوال
                <input type="text" name="question" value="<?php echo $question ?>" maxlength="150" placeholder="..." />
                را از
                <select name="person">
                    <?php
                        $input = file_get_contents('people.json');
                        $list = json_decode($input);
                        foreach($list as $key => $value){
                            if ($key == $en_name){
                                echo "<option value=$key selected> $value </option>";
                            }
                            else{
                                echo "<option value=$key> $value </option>";
                            }
                        }
                    ?>
                </select>
                <input type="submit" value="بپرس"/>
            </form>
        </div>
    </div>
</body>

</html>