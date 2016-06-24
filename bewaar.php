 <?php
 include 'includes/biblio.php';
 session_start();
//        echo "<pre>";
//        print_r ($_REQUEST);
//        echo "<pre>";
//        echo 'nietInKassa';
        //echo "<pre>";
        //print_r($_SESSION);
       // print_r();
      //  echo "<pre>";
      //  $test = $_SESSION["niet"];
       // $tel = count($test);
       // echo '<br>tel = ' . $tel;
        convert_to_csv();
        
//        // remove all session variables
//session_unset(); 
//
//// destroy the session 
//session_destroy(); 
        ?> 
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
<!--        <?php
//        echo "<pre>";
//        print_r ($_REQUEST);
//        echo "<pre>";
//        echo 'nietInKassa';
        //echo "<pre>";
        //print_r($_SESSION);
       // print_r();
      //  echo "<pre>";
//        $test = $_SESSION["niet"];
//        $tel = count($test);
//        echo '<br>tel = ' . $tel;
        
//        // remove all session variables
//session_unset(); 
//
//// destroy the session 
//session_destroy(); 
        ?> -->
        <form name="backToMainPage" action="pagina-init.php">
            <input type="submit" value="Back To Main Page"/>
        </form>
    </body>
</html>
