<?php
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="mili-style.css">               
        <title>kassa-stock</title>
    </head>
    <body>
        <div id="header">
            <img src="milimix.png" alt="milimix" width="50" height="50">
            <br><h2>voorraad controleren cq aanpassen</h2>
        </div>

        <?php
        include 'includes/biblio.php';
        $data = scan_lijst('lijst');
        $tel = count($data);
        $_SESSION["scannen"] = $data;
        echo "<pre>";
        print_r($_FILES);
        echo "<pre>";
        ?>
        <table> 
            <tr> 
                <td><strong>EAN</strong></td> 
                <td><strong>PRODUCT</strong></td> 
                <td><strong>AANTAL</strong></td>            
                <td></td>
            </tr> 
            <?php
            for ($j = 0; $j < $tel; $j++) {
                echo("<tr>\n<td>" . $data[$j]['EAN'] . "</td> ");
                echo("<td>" . $data[$j]['product'] . "</td>");
                echo("<td>" . $data[$j]['units'] . "</td>");
            }
            ?>
        </table>             
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Back To Main Page"/>
        </form>
    </body>
</html>
