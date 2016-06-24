<?php

function my_conn() {
    // echo 'verbinding maken met kassa : ';
    $dbase = 'chromis2'; // DATABASE NAME
   // $dbase = 'mili_chromis1'; // DATABASE NAME
    $host = 'localhost'; //DATABASE HOST LOCATION/SERVER
    $user = 'root'; //USER NAME
    $pass = 'welkom'; //PASSWORD
    $link = @mysqli_connect($host, $user, $pass, $dbase);

    if (!$link) {
        die("connection failed : " . mysqli_connect_error());
    }
    return $link;
}

function selectie() {
    echo '<br><br>';
    if (isset($_POST['zendform'])) {
        $i = 0;
        foreach ($_POST['locatie'] as $locatie) {
            $loc[] = "locations.NAME = '" . $locatie . "'";
            $i++;
        }
        $stloc = "where ((" . implode(" OR ", $loc) . ") and ";
        $i = 0;
        foreach ($_POST['categorie'] as $categorie) {
            $cat[] = "categories.NAME = '" . $categorie . "'";
            $i++;
        }
        $sel = $stloc . "(" . implode(" OR ", $cat) . "))";
        // echo $sel;
        $_SESSION["sel"] = $sel;
        return $sel;
    }
}

function kassalijst() {
    $sel = selectie();


    $prod = "SELECT products.CODE as ean , products.NAME as product, products.PRICEBUY as inkoop, products.PRICESELL as verkoop, locations.NAME as locatie,
                    categories.NAME as categorie, stockcurrent.UNITS as aantal
                    FROM products
                    join categories on products.CATEGORY = categories.ID
                    join stockcurrent on stockcurrent.PRODUCT = products.ID
                    join locations on stockcurrent.LOCATION = locations.ID  $sel ;";
//    echo 'prod = ' . $prod;
    $con = my_conn();
    $result = mysqli_query($con, $prod);
    if (!$result) {
        echo ("<p>error performing prod query: " . mysql_error . "</p>");
        exit();
    }
    mysqli_close($con);
    ?>
    <!--    <table> 
            <tr> 
                <td><strong>EAN</strong></td> 
                <td><strong>PRODUCT</strong></td> 
                <td><strong>INKOOP</strong></td>
                <td><strong>VERKOOP</strong></td>
                <td><strong>AANTAL</strong></td> 
                <td><strong>LOCATIE</strong></td>
                <td><strong>CATEGORIE</strong></td> 
                <td></td>
            </tr> -->
    <?php
//        echo "<pre>";
//        print_r($result);
//        echo "</pre>";
    $rows = array(); //om te bewerken in array gezet
    while ($row = mysqli_fetch_assoc($result)) {
//            echo("<tr>\n<td>" . $row["ean"] . "</td> ");
//            echo("<td>" . $row["product"] . "</td>");
//            echo("<td>" . $row["inkoop"] . "</td>");
//            echo("<td>" . $row["verkoop"] . "</td>");
//            echo("<td>" . $row["aantal"] . "</td>");
//            echo("<td>" . $row["locatie"] . "</td>");
//            echo("<td>" . $row["categorie"] . "</td>");
        $rows[] = $row;
    }


//          echo "<pre>";
//        print_r($rows);
//        echo "</pre>";
    return $rows;
}

function selectie_form() {
    echo 'functie selectie_form';
    $con = my_conn();
    $sqlloc = "select locations.NAME from locations";
    $result = mysqli_query($con, $sqlloc);
    $sqlcat = "SELECT categories.Name FROM categories";
    $resultcat = mysqli_query($con, $sqlcat);
    ?>
    <div class="container">           
        <form name="index" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">                
            <input type="hidden" value="1" name="zendform" />
            <div class="left">
                selecteer de locatie:<br> <select name="locatie[]" multiple="multiple" size='5' >                                                                                            
                    <?php
                    print " <option selected value=\" \"></option> ";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo ("<option value=\"" . $row["NAME"] . "\">" . $row["NAME"] . "</option>\n");
                    }
                    ?>                                                                 
                </select><br><br>
            </div>
            <div class="mid">
                selecteer categorie: <br><select name="categorie[]" size="14"  multiple="multiple">
                    <?php
                    print " <option selected value=\" \"></option> ";
                    while ($row = mysqli_fetch_assoc($resultcat)) {
                        echo ("<option value=\"" . $row["Name"] . "\">" . $row["Name"] . "</option>\n");
                    }
                    ?>
                </select><br><br>
            </div>
            <div class="bottom">     
                <input type="submit" name="submit" value="selectie"/>
            </div>
        </form>
    </div>
    <form name="backToMainPage" action="index.php">
        <input type="submit" value="Back To Main Page"/>
    </form>
    <?php
    mysqli_close($con);
}

function scan_lijst() {
    if ($_FILES['lijst']['size'] > 0) {
        $file = $_FILES['lijst']['tmp_name'];
        $handle = fopen($file, "r");
        $header = NULL;
        $data = array();
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
    } else {
        echo "geen bestand gekozen of het is niet gevonden!";
    }
    return $data;
}

function vergelijk($kassa_data) {
//        echo "<pre>";
//        print_r($kassa_data);
//        echo "</pre>";
//        echo "<pre>";
//        print_r($_SESSION);
//        echo "<pre>";   

    $merge = $kassa_data;#kassa_data is de produkten die in de kassa staan
    $tel = count($merge);
    // echo '<br>tel = ' . $tel . '<br>';
    for ($i = 0; $i < $tel; $i++) {
        $merge[$i]["scan"] = 0;
        $merge[$i]["scan-kassa"] = 0;#array uitbreiden met drei velden
        $merge[$i]["esc"] = 0;
    }

    $scan_data = $_SESSION["scannen"];
    //  $tel = count($scan_data);
//    echo '<br><br>';
//    for ($i = 0; $i < $tel; $i++) {
//        echo 'EAN = ' . $scan_data[$i]['EAN'] . "<br>";
//        echo 'product = ' . $scan_data[$i]['product'] . "<br>";
//        echo 'units = ' . $scan_data[$i]['units'] . "<br>";
//    }
//    echo "<pre>";
//    print_r($scan_data);
//    echo "</pre>";
    $tel_scan = count($scan_data);
    $tel_kassa = count($kassa_data);
//    echo '<br>tel_scan =' . $tel_scan . '<br>';
//    echo '<br>tel_kassa =' . $tel_kassa . '<br>';

    $in_kassa = array();
    $in_scan = array();
    for ($j = 0; $j < $tel_scan; $j++) {
        for ($i = 0; $i < $tel_kassa; $i++) {
            if ($scan_data[$j]['EAN'] == $kassa_data[$i]['ean']) {
                $merge[$i]["scan"] = $scan_data[$j]['units'];
                $merge[$i]["scan-kassa"] = $scan_data[$j]['units'] - $kassa_data[$i]['aantal'];
                $merge[$i]["esc"] = $merge[$i]["scan-kassa"] * $kassa_data[$i]['verkoop'];
                $in_kassa[] = $i;
                $in_scan[] = $j;
            }
        }
    }
    echo '<br>scanlijst aanvullen met de gegevens uit de kassalijst';
    ?>
    <table> 
        <tr> 
            <td><strong>EAN</strong></td> 
            <td><strong>PRODUCT</strong></td> 
            <td><strong>INKOOP</strong></td>
            <td><strong>VERKOOP</strong></td>
            <td><strong>LOCATIE</strong></td> 
            <td><strong>CATEGORIE</strong></td>
            <td><strong>AANTAL</strong></td> 
            <td><strong>SCAN</strong></td> 
            <td><strong>SCAN-KASSA</strong></td> 
            <td><strong>ESCUDO</strong></td> 
            <td></td>
        </tr> 

        <?php
        $tel = count($merge);
        $update = array();
        for ($i = 0; $i < $tel; $i++) {
            if (in_array($i, $in_kassa)) {
                echo("<tr>\n<td>" . $merge[$i]["ean"] . "</td> ");
                echo("<td>" . $merge[$i]["product"] . "</td>");
                echo("<td>" . $merge[$i]["inkoop"] . "</td>");
                echo("<td>" . $merge[$i]["verkoop"] . "</td>");
                echo("<td>" . $merge[$i]["locatie"] . "</td>");
                echo("<td>" . $merge[$i]["categorie"] . "</td>");
                echo("<td>" . $merge[$i]["aantal"] . "</td>");
                echo("<td>" . $merge[$i]["scan"] . "</td>");
                echo("<td>" . $merge[$i]["scan-kassa"] . "</td>");
                echo("<td>" . $merge[$i]["esc"] . "</td>");
                $update[$i] = $merge[$i];
            }
        }

        $_SESSION["update"] = $update;
        $_SESSION["merge"] = $merge;
        $tijd = time();
        date_default_timezone_set("UTC");
        $datum = strftime("%d/%m/%y", $tijd);
        $_SESSION["datum"] = $datum;
        ?>
    </table>
    <form name="index" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
        <input type="submit" value="merge" name="submit" />
    </form>
    <form name="index" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
        <input type="submit" value="update" name="submit" />
    </form>
    <?php
//    echo "<pre>";
//    print_r($merge);
//    echo "</pre>";
//    $temp = array_keys($merge[0]);
//    echo "<pre>";
//    print_r($temp);
//    echo "<pre>";
    //convert_to_csv($merge, 'merge-report.csv', ',');
    //convert_to_csv();
//    echo '<br><br>welke wel in kassa maar niet gescand';
//    echo "<pre>";
//    print_r($in_kassa);
//    echo "<pre>";
    echo '<br><br>wel in kassa maar niet gescand<br>';
    ?>
    <table> 
        <tr> 
            <td><strong>EAN</strong></td> 
            <td><strong>PRODUCT</strong></td> 
            <td><strong>AANTAL</strong></td>            
            <td></td>
        </tr> 

        <?php
        $nietInScan = array();
        for ($j = 0; $j < $tel_kassa; $j++) {
            if (!in_array($j, $in_kassa)) {//               
                echo("<tr>\n<td>" . $kassa_data[$j]['ean'] . "</td> ");
                echo("<td>" . $kassa_data[$j]['product'] . "</td>");
                echo("<td>" . $kassa_data[$j]['aantal'] . "</td>");
                $nietInScan[$j]['EAN'] = $kassa_data[$j]['ean'];
                $nietInScan[$j]['product'] = $kassa_data[$j]['product'];
                $nietInScan[$j]['aantal'] = $kassa_data[$j]['aantal'];
            }
        }
        $_SESSION["nietInScan"] = $nietInScan;
//    echo "<pre>";
//    print_r($scan_data);
//    echo "<pre>";
//
//      $temp = array_keys($scan_data[0]);
//    echo "<pre>";
//    print_r($temp);
//    echo "<pre>";
        ?>
    </table>

    <form name="index" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
        <input type="submit" value="nietInScan" name="submit" />
    </form>

    <?php
//    echo "<pre>";
//    print_r($nietInScan);
//    echo "<pre>";
//    echo '<br><br>welke wel in scan maar niet in kassa';
//    echo "<pre>";
//    print_r($in_scan);
//    echo "<pre>";
    //  echo '<br> <br>wel in scan maar niet in kassa <br>';
    //  echo 'als bestaat in kassa dan initialiseren';
    ?>
    <table> 
        <tr> 
            <td><strong>EAN</strong></td> 
            <td><strong>PRODUCT</strong></td> 
            <td><strong>AANTAL</strong></td> 
            <td><strong>INIT</strong></td> 
            <td></td>
        </tr> 

        <?php
        $con = my_conn();
        $bekendInKassa = array();
        $nietInKassa = array();
        echo '<br>niet bekend in kassa of nog niet geinitialiseerd<br>';
        echo '<br>pas op alle locaties worden geinitialiseerd (dubbele rijen)<br>';
        for ($j = 0; $j < $tel_scan; $j++) {
            if (!in_array($j, $in_scan)) {
                $inKassa_sql = "SELECT products.code as ean, products.name
                FROM products
                where products.code= '" . $scan_data[$j]['EAN'] . "'";
                //categories.name = 'snoeop' and 
                //echo '<br>inKassa_sql = ' . $inKassa_sql;
                $result = mysqli_query($con, $inKassa_sql);
                if (!$result) {
                    echo ("<p>error performing prod query: " . mysql_error . "</p>");
                    exit();
                }

                echo("<tr>\n<td>" . $scan_data[$j]['EAN'] . "</td> ");
                echo("<td>" . $scan_data[$j]['product'] . "</td>");
                echo("<td>" . $scan_data[$j]['units'] . "</td>");
                $nietInKassa[$j]['EAN'] = $scan_data[$j]['EAN'];
                $nietInKassa[$j]['product'] = $scan_data[$j]['product'];
                $nietInKassa[$j]['units'] = $scan_data[$j]['units'];
                if ($row = mysqli_fetch_assoc($result)) {
                    //echo '<br><br>product in bekend in kassa : init stock = 0 in locaties';
                    $bekendInKassa[] = $row;
                    echo("<td> INITIALISEREN</td>");
                }
            }
        }

        mysqli_close($con);
        $_SESSION["nietInKassa"] = $nietInKassa;
        $_SESSION["init"] = $bekendInKassa;
//        echo "<pre>";
//        print_r($bekendInKassa);
//        echo "<pre>";
        ?>
    </table>
    <form name="index" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
        <input type="submit" value="nietInKassa" name="submit" />
    </form>
    <form name="init" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
        <input type="submit" value="init" name="submit" />
    </form>
    <?php
}
?>

<?php

function convert_to_csv($reeks, $titel, $output_file_name, $delimiter) {
    // $filename = "testing-exports.csv";
    header("Content-type:application/csv");
    header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
    header("Pragma: no-cache");
    header("Expires: 0");

//    echo "<pre>";
//    print_r($_SESSION["niet"]);
//    echo "<pre>";
//    echo "<pre>";
//    print_r($reeks);
//    echo "<pre>";
//    echo "<pre>";
//    print_r($_SESSION);
//    echo "<pre>";
    $output = fopen('php://output', 'w');
//    /** loop through array  */
    fputcsv($output, $titel);
    // $title = array("EAN", "PRODUCT", "AANTAL");
    // fputcsv($output, $title);
    foreach ($reeks as $line) {
//        /** default php csv handler * */
        fputcsv($output, $line, $delimiter);
    }
    exit();
}

function update() {
    //echo 'vanuit update<br>';
    $update = $_SESSION["update"];
    $sel = $_SESSION["sel"];
    $update = array_values($update);
    $tel = count($update);
    for ($i = 0; $i < $tel; $i++) {
        $update_sql = "update products
join categories on products.CATEGORY = categories.ID
join stockcurrent on stockcurrent.PRODUCT = products.ID
join locations on stockcurrent.LOCATION = locations.ID 
 set stockcurrent.units ='" . $update[$i]["scan"] . "' $sel
     and products.CODE = '" . $update[$i]["ean"] . "'";
//and products.name = '" . $update[$i]["product"] . "'"; //product naar ean omzetten
//#where locations.NAME = 'mili' and categories.NAME = 'snoeop'
#'melk chocolaat'";
# "locations.NAME = '" . $locatie . "'"
        // echo '<br>update_sql = ' . $update_sql;

        $con = my_conn();
        $result = mysqli_query($con, $update_sql);

        if (!$result) {
            echo ("<p>error performing prod query: " . mysql_error . "</p>");
            exit();
        }
    }
    mysqli_close($con);
}

function init() {
    // echo 'vanuit init';
    $init = $_SESSION["init"];# $bekendInKassa vanuit vergelijk($kassa_data)
//    echo "<pre>";
//    print_r($init);
//    echo "<pre>";
    $con = my_conn();
    $sqlloc = "select locations.NAME, locations.ID from locations";
    $result = mysqli_query($con, $sqlloc);
    while ($row = mysqli_fetch_assoc($result)) {
//        echo "<br>" . $row["NAME"];
//        echo "  id =  " . $row["ID"];
        $rows[] = $row;
    }
//    echo "<pre>";
//    print_r($rows);
//    echo "<pre>";
    $tel = count($rows);
    $tel_init = count($init);
    //echo 'tel =' . $tel_init;
    for ($i = 0; $i < $tel; $i++) {
        //alle locaties een voor een !! probleem als je een locatie wilt init => andere locatie dubbel init (geen unieke velden in tabel!!!)
        for ($x = 0; $x < $tel_init; $x++) {
            //echo '<br>ean = ' . $init[$x]['ean'] . "<br>";
            $product_id = "select products.CODE, products.NAME, products.ID from products
                where products.CODE= '" . $init[$x]['ean'] . "'";
            $id = mysqli_query($con, $product_id);
            $row1 = mysqli_fetch_assoc($id);
            //echo "<br> ean = " . $init[$x]['ean'] . "   id = " . $row1["ID"] . "<br>";
            $sql_init = "insert into stockcurrent (location, product, ATTRIBUTESETINSTANCE_ID, units )
                values ('" . $rows[$i]["ID"] . "', '" . $row1["ID"] . "' , NULL, 0)";
           // echo '<br> sql_init =' . $sql_init;
            $result = mysqli_query($con, $sql_init);
            if (!$result) {
                echo ("<p>error performing prod query: " . mysql_error . "</p>");
                exit();
            }
        }
    }
    mysqli_close($con);
}
?>