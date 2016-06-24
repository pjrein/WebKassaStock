<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>kassa-stock</title>
    </head>
    <body>       
        <form action="scanLijst.php" method="post"  enctype="multipart/form-data">
            Choose your file: <br/>
            <input name="lijst" type="file" id="csv"/>
            <input type="submit" value="lees lijst" /><br><br>
        </form>

        <form name="keuze-form" action="pagina-init.php" method="POST">
            <input type="submit" value="keuze" name="keuze" />
        </form>
    </body>
</html>
