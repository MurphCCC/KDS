<!DOCTYPE html>
<html lang='en'>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel='stylesheet' type='text/css' href='./css/clock.css'>
    <link href='https://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'>
<!--     <link rel='stylesheet' type='text/css' href='./css/bootstrap.min.css'>
 -->    
    <link rel='stylesheet' type='text/css' href='test.css'>
    <link href='https://fonts.googleapis.com/css?family=Space+Mono' rel='stylesheet'>

<meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body onLoad="document.getElementById('kds').focus();">

<div class="sticky row">
    <div class="col-12">
        <span class='clock'></span>
<!--         <h1 class='title' contenteditable='true'>Saut&eacute; Checks</h1><br>
 -->
        <div id='production'><span></span></div>

        <script src='./js/clock.js'></script>
    </div></div>
            <div class="blank-col"></div> 

<div class='wrapper'>
    <div class='row' id='kds' tabindex='1''>


    <?php
    /* Database credentials*/

    $dbhost = 'host';
    $dbuser = 'user';
    $dbpass = 'password';
    $conn = mysql_connect($dbhost, $dbuser, $dbpass);

    $timeCurrent = date("H:i:s");
    $timePast = date('Y-m-d H:i:s',time() - 60);


    $order_id = $_POST['update'];
    $order_id = filter_var($order_id, FILTER_SANITIZE_NUMBER_INT);

    $db = new PDO('mysql:host=host;dbname=database;charset=utf8mb4', 'user', 'password', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $db->query("UPDATE tickets SET item = CONCAT(item, '-', check_number) WHERE check_number NOT REGEXP '^[0-9]+' AND check_number !=''"); // Append forced modifier to item and se$
        $db->query("UPDATE tickets SET check_number = '' WHERE check_number NOT REGEXP '^[0-9]+'");
    //  Logic to bump tickets, tied to bump button in each tickets div
    if (isset($_POST['update'])) {
            $order_id = $_POST['update'];
    $order_id = filter_var($order_id, FILTER_SANITIZE_NUMBER_INT);
        $db->query("UPDATE tickets SET visible_saute = 0 WHERE order_id = '$order_id'");
    }

    if (isset($_POST['recall'])) {
        $db->query("UPDATE tickets SET visible_saute = 1 WHERE visible_saute = '0' ORDER BY `ticket_time` DESC LIMIT 1");
    }


    //  New Method
    $db = new PDO('mysql:host=localhost;dbname=database;charset=utf8mb4', 'user', 'password', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

/*    $select = 'SELECT GROUP_CONCAT(item_id SEPARATOR ":") AS "id", 
                    GROUP_CONCAT(quantity SEPARATOR ":"), 
                    GROUP_CONCAT(modifier SEPARATOR ":"), 
                    GROUP_CONCAT(category_id SEPARATOR ":") 
                    order_id, check_number, quantity, 
                    GROUP_CONCAT(special SEPARATOR ":"), 
                    ticket_time, GROUP_CONCAT(notes SEPARATOR ":"),
                    id, 
                    GROUP_CONCAT(item SEPARATOR ":") 
                    FROM tickets WHERE visible_saute = 1
                        GROUP BY id 
                            LIMIT 12';*/

    $item_id = $row['item_id'];

    $saute_items = array(
        '1025-25779', //  Salmon Ceaser
        '1025-25781', //  Simple Salmon
        '1025-25790', //  Mac And Cheese
        '1025-25803', //  Ginger Beef
        '1025-25804', //  Kids Mac
        '1025-25814', //  Day Veg Side
        // '2707', //  Salmon Filet
        // '3057', //  Sweet N Sour
        // '2771', //  Ginger Chicken

    );
    //print_r($saute_items);
    $needle = '/(' .implode('|', $saute_items) .')/i';
    $counter = 1;

    if (preg_match($needle, $item_id)) {
        $item = $row['order_id'];
    }

    //$setup_modifier = 'UPDATE tickets SET modifier = check_number WHERE check_number NOT LIKE "%0%"';
    //echo $needle;
$order_id_array = array();

/*
*
*   This foreach loop should be the only thing that we need to modify in order to change our ticket view
*   
*/
foreach(
    $db->query("SELECT order_id FROM tickets INNER JOIN menu_items WHERE menu_items.category = 'saute'
        AND menu_items.id LIKE tickets.item_id GROUP BY order_id") as $order_id) {
    $order_id_array[] = $order_id['order_id'];

}

    // echo '<pre>';
    // print_r($order_id_array);
    // echo '</pre>';
$needle = '/(' .implode('|', $order_id_array) .')/i';
    foreach(

        $db->query
        ('SELECT GROUP_CONCAT(item_id SEPARATOR ":") AS item_id, 
            modifier, category_id, order_id, check_number, ticket_time, 
            GROUP_CONCAT(item SEPARATOR ":") AS item,
            GROUP_CONCAT(notes SEPARATOR ":") AS notes,
            GROUP_CONCAT(quantity SEPARATOR ":") AS quantity
            FROM tickets WHERE print = 1 AND visible_saute = 1 GROUP BY order_id ORDER BY ticket_time DESC') as $row) {
        if (preg_match($needle, $row['order_id'])) {

 //            //  Set up our rows and colums, if we want to use col-md-3 then every 4th iteration should echo out <div class="col-md-3"> after the </div>

 //           echo $counter++;
                        $notes = str_replace(',','<br>',$row['GROUP_CONCAT(notes SEPARATOR ":")']);
                $notes = explode(":", $notes);
            $item = str_replace('Check #:', '', $row['item']);
                    $item = str_replace('Check #', '', $item);

            $item = str_replace(':', '<br>', $item);
                        $item = strtolower($item);
            $item = mb_convert_case($item, MB_CASE_TITLE);


            echo "<div class='col-6'>";
            echo '<div class="ticket_number">';
            echo 'Order ' . $row['order_id'];
            echo '<br>';
            echo 'Check #' .$row['check_number'];
            echo '</div>';

            echo '<div class="ticket_details">';
            echo $item;
            echo '<br><br>';
            echo $row['ticket_time'];

            echo "
            <form method='post' action=''>
            <button class='btn btn-danger' name='update' type='submit'id ='update-".$counter++."' value='".$row['order_id']."'>Bump</button>
            ";
            echo '</div></div>';
          //  $counter++;
            

            if ($counter % 4 == 1) {
                echo "</div><div class='row'>";
            }

        }
    }
    //  Check how many tickets we have.  If we have less than 12 tickets, then reload page every 15 seconds to check for more.
            if ($counter < 12) {
                echo '<meta http-equiv="refresh" content="15" />';
            }

    /*    $select = 'SELECT GROUP_CONCAT(item_id) AS specials, 
                        GROUP_CONCAT(quantity SEPARATOR ":"), 
                        GROUP_CONCAT(modifier SEPARATOR ":"), 
                        GROUP_CONCAT(category_id SEPARATOR ":") 
                        order_id,
                        check_number, 
                        quantity, 
                        GROUP_CONCAT(special SEPARATOR ":"), 
                        ticket_time, 
                        GROUP_CONCAT(notes SEPARATOR ":"), 
                        GROUP_CONCAT(item SEPARATOR ":")
                            FROM tickets WHERE visible_saute = 1
                                    GROUP BY order_id
                                    HAVING "specials" = 2448';
    
    */  //This is our 'recall' button.  It is attached to a hidden submit button that is triggered when the number 0 is pressed on the number pad using keypress.js and numpad.js

    if(isset($_POST['recall'])){
        $recall = "UPDATE tickets SET visible_saute = '1' WHERE visible_saute = '0' ORDER BY `ticket_time` DESC LIMIT 1";
        $db->query($recall);
    }

    //This button is to clear all checks on the screen, their is no undo!
    if(isset($_POST['clear_all'])) {
        $clear_all = "UPDATE tickets SET visible_saute = '0'";
        $db->query($clear_all);
    }
    // /* We want to display the quantity next to each item, however it doesn't make sense to show the quantity next to the check number.  Because the table/order/check number that we assign to the customer
    //    is set up as a menu item with a modifier, we need to exclude this from our loop when we print out the quantities.  One way to do this is simply to set a null quantity on all rows where
    //    the item = 'Check #' or 'FOUNTAIN' */
    // $remove_invalid_quantity = 'UPDATE tickets SET quantity = "" WHERE item = "Check #" OR item = "FOUNTAIN"';

    //  Need to account for the fact that when we assign a customer a # to take to their table this information is entered into POSLavu as an item named 'Check # with modifiers corresponding to the actual number'
    //    This causes problems because we use this same field for actual item modifiers and we need to be able to put the check number into one field and the modifier into another.  One solution is to look for all
    //    the rows where the item does not equal check number, take the information in the check_number field and move it over to the modifier field then select all rows where the item does not equal check # and set
    //    the check_number value to null.  Surely their is a better way to do this, but it is what I have to work with at the moment.  First we will look for all rows where the item does not equal 'Check #', we will
    //    take the check_number field and move it over to the modifier field, then we will run another query to remove the value from the check_number field
    // $setup_modifier = 'UPDATE tickets SET modifier = check_number WHERE check_number NOT LIKE "%0%"';
    // //  $remove_check_number = 'UPDATE tickets SET check_number = "" where item != "Check #"';
    // $db->query($remove_invalid_quantity);
    // $db->query($setup_modifier);
    // //              $db->query($remove_check_number);
    // $results = $db->query($select);

    //echo '<pre>';
    //while ($rows = mysqli_fetch_array($results, MYSQLI_NUM)) {
    //     var_dump($row['GROUP_CONCAT(quantity SEPARATOR ":")']);
    //}
    //echo '</pre>';

    /* Set up some counters to use in our loop. */
    $i=1;
    $ii=1;
    $iii=1;
    $disableTab='tabindex=-1';


    /*        while ($row = $results->fetch_array()) {
    
     //   echo '<pre>';print_r($row); echo '</pre>';
        Take our items and put them into an array.  We want to remove every instance of 'Check #', convert everything to lower case put the list of items on the check into
        an array, take each word and upper case the first letter.  Take our quantity and put it into an array, take the date off of our ticket_time field and use that as the
        the check time.  Take the 'Check #' field, make sure it contains only numbers by filtering out any letters and set that as our $check_number variable
                $search = array('Check #');
                $replace = array('');
                    $item = str_replace($search, $replace, $row['GROUP_CONCAT(item SEPARATOR ":")']);
                        $item = strtolower($item);
                            $item = explode(":", $item);
                                $item = array_map(function($word) { return ucwords($word); }, $item);
    
        $quantity = explode(":", $row['GROUP_CONCAT(quantity SEPARATOR ":")']);
    
            $time = $row['ticket_time'];
            $check_time = substr($time, 11,18);
    
            $modifier = explode(":", $row['GROUP_CONCAT(modifier SEPARATOR ":")']);
    
            $check_number = $row['check_number'];
                $check_number = substr($check_number, 0,3);
                    $check_number = filter_var($check_number, FILTER_SANITIZE_NUMBER_INT);
    
            $notes = str_replace(',','<br>',$row['GROUP_CONCAT(notes SEPARATOR ":")']);
                $notes = explode(":", $notes);
    
            $order_id = $row['order_id'];
    
            $special = $row['GROUP_CONCAT(special SEPARATOR ":")'];
            $special = explode(":", $special);
            $rows[] = $row;
                echo "
    
                    <div class='well' id='ticket".$i."'>
                        <li class='menu_item ".$i."'>
                        <div class='left'>Order#<div class='order_number'>".$order_id."</div></div>
                        <div class='right'>Check#<div class='order_number'>".$check_number."</div></div><br /><br />
                        <div class='menu_item_name'>".$quantity[0]." ".$item[0]."<div class='modifier'>".$modifier[0]." ".$notes[0]." ".$special[0]."</div></div>
                        <div class='menu_item_name'>".$quantity[1]." ".$item[1]."<div class='modifier'>".$modifier[1]." ".$notes[1]." ".$special[1]."</div></div>
                        <div class='menu_item_name'>".$quantity[2]." ".$item[2]."<div class='modifier'>".$modifier[2]." ".$notes[2]." ".$special[2]."</div></div>
                        <div class='menu_item_name'>".$quantity[3]." ".$item[3]."<div class='modifier'>".$modifier[3]." ".$notes[3]." ".$special[3]."</div></div>
                        <div class='menu_item_name'>".$quantity[4]." ".$item[4]."<div class='modifier'>".$modifier[4]." ".$notes[4]." ".$special[4]."</div></div>
                        <div class='menu_item_name'>".$quantity[5]." ".$item[5]."<div class='modifier'>".$modifier[5]." ".$notes[5]." ".$special[5]."</div></div>
                        <div class='menu_item_name'>".$quantity[6]." ".$item[6]."<div class='modifier'>".$modifier[6]." ".$notes[6]." ".$special[6]."</div></div>
                        <div class='menu_item_name'>".$quantity[7]." ".$item[7]."<div class='modifier'>".$modifier[7]." ".$notes[7]." ".$special[7]."</div></div>
                        <div class='menu_item_name'>".$quantity[8]." ".$item[8]."<div class='modifier'>".$modifier[8]." ".$notes[8]." ".$special[8]."</div></div>
                        <div class='menu_item_name'>".$quantity[9]." ".$item[9]."<div class='modifier'>".$modifier[9]." ".$notes[9]." ".$special[9]."</div></div>
                        <div class='menu_item_name'>".$quantity[10]." ".$item[10]."<div class='modifier'>".$modifier[10]." ".$notes[10]." ".$special[10]."</div></div>
                        <div class='menu_item_name'>".$quantity[11]." ".$item[11]."<div class='modifier'>".$modifier[11]." ".$notes[11]." ".$special[11]."</div></div>
                        <div class='menu_item_name'>".$quantity[12]." ".$item[12]."<div class='modifier'>".$modifier[12]." ".$notes[12]." ".$special[12]."</div></div>
                        <div class='menu_item_name'>".$quantity[13]." ".$item[13]."<div class='modifier'>".$modifier[13]." ".$notes[13]." ".$special[13]."</div></div><br />
    
                        <div class='left'>Opened at</div><div class='order_number right'>".$check_time."</div><div id='time'></div>
    
                <form method='post' action=''>
                                <button name='update' type='submit'id ='update-".$i++."' value='".$order_id."'>Bump</button><br /></div></li>
    
                ";
        //End our row and start a new row after we display 4 tickets, just to make sure that we have 4 on each row
                        if ($i % 5 == 0) {
                            echo "</div><div class='row'>";
                        }
            }
        echo "</div></div></div>";
    */


    ?>
</div>
    <br>

    <form action='' method='POST'>
        <input type = 'submit' name='recall' id='recall' value='recall'></form>
    </form>
    <form action='' method='POST'>
        <input type='submit' name='clear_all' id='clear_all' value='clear_all'></form>

    <script type='text/javascript'>

        $('#kds').bind('keydown', function(event) {

            //If you want to check the code from the key
            //console.log(event.keyCode);
            console.log(event.keyCode);

            switch(event.keyCode){

                case 96: case 48:
                document.getElementById("recall").click();
//  document.getElementById("fullScreen").click();
                break;

                case 97: case 49: //1 on the numpad and up top
                console.log("1 from the ticket display div");
                $("#ticket1").fadeOut();
                document.getElementById("update-1").click();
                break;

                case 98: case 50: //2
                console.log("2 from the ticket display div");
                $("#ticket2").fadeOut();
                document.getElementById("update-2").click();
                break;

                case 99: case 51: //3
                console.log("3 from the kds div");
                $("#ticket3").fadeOut();
                document.getElementById("update-3").click();
                break;

                case 100: case 52://4
                console.log("4 from the kds div");
                $("#ticket4").fadeOut();
                document.getElementById("update-4").click();
                break;


                case 101: case 53://5
                console.log("5 from the kds div");
                $("#ticket5").fadeOut();
                document.getElementById("update-5").click();
                break;

                case 102: case 54://6
                console.log("6 from the kds div");
                $("#ticket6").fadeOut();
                document.getElementById("update-6").click();
                break;

                case 103: case 55://7
                console.log("7 from the kds div");
                $("#ticket7").fadeOut();
                document.getElementById("update-7").click();
                break;

                case 104: case 56://8
                console.log("8 from the kds div");
                $("#ticket8").fadeOut();
                document.getElementById("update-8").click();
                break;

                case 57: case 105://#9
//  location.reload();
                console.log("9 from the kds div");
                $("#ticket9").fadeOut();
                document.getElementById("update-9").click();
                break;

                case 189: case 109: //Use - sign to scroll down
                window.scrollBy(0, 100);
                console.log('Scroll Down');
                break;

                case 187: case 107: //Use + sign to scroll up
                window.scrollBy(0, -100);
                break;
            }
        });

    </script>

<!--     Check the current date and time and print a message accordingly.  This is to help us remember which order the production team/worship team orders 
    need to get made.  The schedule is as follows:
        Saturday - Worship Team eats after they sing the third song at the 6:0pm  -->

    <script>
    var d = new Date();
    var currentHour = d.getHours(); //note 0-23
    var currentMinute = d.getMinutes();

    if (currentHour < 6) 
     { 
         $('div').append('Before 6am');
      }
    else if (currentHour == 19 && currentMinute >= 02 || currentHour == 12 && currentMinute <= 15)
    {
       $('div#production').append('<production><div class="blink"><b>FIRE Production!<b></div></production>');
       console.log(currentMinute);
    }
    else if (currentHour == 13 && currentMinute >= 20 && currentMinute < 40)
    {
        $('div#production').append('<production><div class="blink"><b>Get ready for Worship!</b></div></production>');
    }
    else {
      $('div#production').append('<h1>Saut&eacute; Checks</h1>');
      }
 
function blink(selector){
$(selector).fadeOut('fast', function(){
    $(this).fadeIn('slow', function(){
        blink(this);
    });
});
}
    
blink('.blink');


    </script>

    <!--
    //Select tickets containing Saute items (in this case using category_ids
    SELECT GROUP_CONCAT(item), GROUP_CONCAT(category_id) AS category_id, order_id FROM tickets GROUP BY order_id HAVING category_id LIKE '%112%'
    
    //Show me all orders containing Salmon Ceaser
    SELECT GROUP_CONCAT(item), GROUP_CONCAT(item_id) AS item_id, order_id FROM tickets GROUP BY order_id HAVING item_id LIKE '%2448%'
    
    //Simple Salmon 2646
    SELECT GROUP_CONCAT(item), GROUP_CONCAT(item_id) AS item_id, order_id FROM tickets GROUP BY order_id HAVING item_id LIKE '%2448%' OR '%2646%'
    
    FROM tickets WHERE visible = 1 GROUP BY order_id HAVING item_id LIKE "%2448%" OR "%2646%" LIMIT 12
