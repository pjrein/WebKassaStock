<!--
To change this template, choose Tools | Templates
and open the template in the editor.
http://www.sitemasters.be/?pagina=overzicht/overzicht&cat=10&id=215
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // maak 2 arrays aan, dit is een simpele azerty, qwerty vergelijking
        $azerty = array('a', 'z', 'e', 'r', 't', 'y');
        $qwerty = array('q', 'w', 'e', 'r', 't', 'y');

        //deze var zoeken we in de array $azerty
        $letter = "a";

        //controleren of de waarde z in de array azerty zit
        if (array_search($letter, $azerty) !== false) {
        //dan gebruiken we array_search voor te kijken welke key de waarde z heeft,
        //en slagen deze op in $nr
            $nr = array_search($letter, $azerty);
        //dan gaan we kijken in de array van qwerty welke waarde dezelfde key heeft
            echo"$qwerty[$nr]";
        } else {
            echo"De waarde was niet gevonden in de array";
        }
        ?>
    </body>
</html>



<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
* search_array.php - Search a value in the parameter $array and add the found results to $results;
* 
* this function was written for pj_muller00
*
* function: search_array( $array , $search , $use_path = false , $path = '' , $results = array() );
* this function returns an array with results.
*
* @license      http://www.gnu.org/licenses/gpl.html
* @author       Stijn Leenknegt <stijnleenknegt@gmail.com>
* @version      Versie 1.0
*/
 
// full error reporting
error_reporting( E_ALL );
 
 
/**
 * @param array $array
 * @param string $search
 * @param bool $use_path
 * @param string $path
 * @param array $results
 * @return array
 */
function search_array( $array , $search , $use_path = false , $path = '' , $results = array() )
{
 
	foreach( $array as $key => $value) {
 
		if( is_array( $value ) === true ) {
			search_array( $value , $search , $use_path , ( strlen( $path ) == 0) ? $path . $key : $path . ' » ' . $key , &$results );
		} 
 
		if( $value == $search ) {
			$results[] = ( $use_path === false ) ? true : $path;
		}
 
	}
 
	return $results;
 
}
 
#####################################
# Exemples how to use this function #
#####################################
# a 2D array                        #
#####################################
$box = array( );
$box['A'] = array('a1','a2','a3','b1','b2','b3','c1','c2','c3');
$box['B'] = array('a4','a5','a6','b4','b5','b6','c4','c5','c6');
$box['C'] = array('a7','a8','a9','b7','b8','b9','c7','c8','c9');
$box['D'] = array('d1','d2','d3','e1','e2','e3','f1','f2','f3');
$box['E'] = array('d4','d5','d6','e4','e5','e6','f4','f5','f6');
$box['F'] = array('d7','d8','d9','e7','e8','e9','f7','f8','f9');
$box['G'] = array('g1','g2','g3','h1','h2','h3','i1','i2','i3');
$box['H'] = array('g4','g5','g6','h4','h5','h6','i4','i5','i6');
$box['I'] = array('g7','g8','g9','h7','h8','h9','i7','i8','i9');
 
//with the key path
$results_array = search_array( $box , 'f4' , true );
 
foreach( $results_array as $key => $value ) {
	echo $value . '<br>';
}
 
//without the key path
$results_array = search_array( $box , 'g5' , false );
 
foreach( $results_array as $key => $value ) {
	echo $value . '<br>';
}
 
 
#####################################
# a 3D array                        #
#####################################
$my_array = array();
$my_array['A'] = array( 'B' => array( 'F1' , 'F2' ) , 'C' => array( 'F3', 'F4' ) );
$my_array['D'] = array( 'E' => array( 'F5' , 'F6' ) , 'F' => array( 'F7', 'F8' ) );
 
//with the key path
$results_array = search_array( $my_array , 'F4' , true );
 
foreach( $results_array as $key => $value ) {
	echo $value . '<br>';
}
?>

<?php
function resultToArray($result) {
    $rows = array();
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

// Usage
$query = 'SELECT DISTINCT $fields FROM `posts` WHERE `profile_user_id` = $user_id LIMIT 4';
$result = $mysqli->query($query);
$rows = resultToArray($result);
var_dump($rows); // Array of rows
$result->free();