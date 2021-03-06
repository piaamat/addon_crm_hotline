<?php
session_start();
//STRONA MODYFIKACJI STANOWISK PRACOWNIKA
//sprawdzanie zalogowania użytkownika
if(isset($_SESSION['hotline']) && $_SESSION['hotline'] == sha1(lock) && isset($_COOKIE['hotline']))
{
require_once ('config/config.php');
//zmiene zalogowanej osoby
$username = $_SESSION['username'];
$name = $_SESSION['name'];
$surname = $_SESSION['surname'];
$limit = 0;
        //odnowienie ciasteczek podczas odświerzania
        if (($username=='k.szpond')||($username=='p.szymczyk')||($username=='m.pianka')||($username=='p.jakacki'))
        {
            setcookie("hotline", 'online', time() + 9900); //czas życia cookie
        }
        else
        {
            setcookie("hotline", 'online', time() + 1800); //czas życia cookie
        }
//pobieranie danych z tablicy GET
$id = $_GET[id];
//zapytanie pozwalajacy wyświetlic dane , osoby edytowanej
$zapytanie = "SELECT *  FROM uzytkownicy_ewidencja WHERE id='$id'" ;
if ($wynik = $db2->query($zapytanie)) {
$ilosc = $wynik->num_rows;
$tablica = $wynik->fetch_assoc();
$licznik = 1;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Paweł Szymczyk" />
    <title>Panel Hotline</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/bootstrap-select.min.css" rel="stylesheet">
    <script src="js/jquery-3.1.1.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootbox.min.js"></script> <!--plugin js do okien dialogowych (potwierdzenie) -->
    <script src="js/bootstrap-select.min.js"></script> <!--Ładny select -->
</head>
<body>

<div class="navbar navbar-default btn-variants navbar-fixed" role="navigation">
    <div style="text-align: center" class="col-lg-8 col-lg-offset-2 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        <h2 class="font_logo ">PANEL HOTLINE</b></h2>
    </div>

    <div class="col-lg-2 col-lg-offset-0 col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-sm-6 col-sm-offset-3 col-xs-4 col-xs-offset-4" >
        <?php


        //jesli istnieje zmienna imie OR naziwsko
        if(isset($_SESSION['name_back'])||($_SESSION['surname_back']))
        {
        //jesli zmienna imie AND naziwsko jest pusta
        if(($_SESSION['name_back']=="") && ($_SESSION['surname_back']=="")) {
            echo "<a role=\"button\" class=\"btn btn-primary btn-sm \" style=\"margin-top: 15px\" href=\"main.php\">Powrót</a>";

        }
        echo
            "<form action='main.php' method='post' name='". $tablica[id] ."'>
                        <button type='submit' class='btn btn-primary btn-sm' style='margin-top: 15px; float: left;margin-right: 5px'>Powrót</button>

            <input type='hidden' value='".$_SESSION['name_back']."' name='imie'>
            <input type='hidden' value='".$_SESSION['surname_back']."' name='nazwisko'>
            </form>
        ";




        }
        else {
            echo "<a role=\"button\" class=\"btn btn-primary btn-sm \" style=\"margin-top: 15px\" href=\"main.php\">Powrót</a>";
        }


        ?>
       <!-- <a role="button" class="btn btn-primary btn-sm " href="main.php">Powrót</a>-->

        <a data-toggle="tooltip" data-placement="bottom" title="Zalogowany:<?php  echo "    ".$name." ".$surname; ?>" role="button" class="btn btn-default btn-sm " style="margin-top: 15px" href="logout.php">Wyloguj</a>
    </div>


</div>

<div class="container">

<div class="alert alert-info">
    <?php
    //innformacja od osobie modyfikowanej
    echo "<h4>Modyfikujesz stanowiska pracownika: <b>".$tablica[imie]." ".$tablica[nazwisko]."<h4 /></b>";
    ?>
</div>

<?php
echo $_SESSION['alert2'];
unset($_SESSION['alert2']);
?>

<button class="btn btn-default col-lg-4 col-lg-offset-4 myButton2" style="margin-bottom: 15px" data-toggle="modal" data-target="#exampleModal">Dodaj stanowisko</button>
    <table class="table table-striped" cellspacing='0' style='text-align: center'>
    <tr>
        <!-- //nagłówki tabeli -->
        <th style="text-align: center">lp.</th>
        <th style="text-align: center">Jednostka org.</th>
        <th style="text-align: center">Stanowisko</th>
        <th style="text-align: center">Usuń stanowisko</th>
        <th style="text-align: center">Czy główne</th>
    </tr>

        <?php
        //wyświetl wszystkie aktwne stanowiska u pracownika
        $zapytanie_stanowiska = "SELECT *  FROM pracownicy_stanowiska WHERE id_pracownika='$id' AND status=1";
        $wynik_stanowiska = $db2_hr->query($zapytanie_stanowiska);
        $ilosc_stanowiska = $wynik_stanowiska->num_rows;
        for ($i = 0; $i < $ilosc_stanowiska; $i++) {
            $tablica_stanowiska = $wynik_stanowiska->fetch_assoc();

            //wyświetl nazwe jednostki organizacyjnej
            echo "<tr><td>".$licznik.".</td>";
            $licznik++;
            $zapytanie_jednostka = "SELECT * FROM jednostki_organizacyjne_ewidencja WHERE id='$tablica_stanowiska[id_jednostki_organizacyjnej]'";
            $wynik_jednostka = $db2->query($zapytanie_jednostka);
            $ilosc_jednostka = $wynik_jednostka->num_rows;
            $tablica_jednostka = $wynik_jednostka->fetch_assoc();


            echo "<td>".$tablica_jednostka['nazwa']."</td>";

            //wyświetl nazwe stanowiska
            $zapytanie_stanowisko = "SELECT * FROM stanowiska_ewidencja WHERE id='$tablica_stanowiska[id_stanowiska]'";
            $wynik_stanowisko = $db2->query($zapytanie_stanowisko);
            $ilosc_stanowisko = $wynik_stanowisko->num_rows;
            $tablica_stanowisko = $wynik_stanowisko->fetch_assoc();

            echo "<td>".$tablica_stanowisko['nazwa']."</td>";


//jeśli stnowisko jest ustawione jako główne to pogrub je
if ($tablica_stanowiska[czy_glowne]==1)
{
    echo "<td><input disabled type=submit class=\"btn btn-danger disabled\" value='Usuń' \" class=\"myButton2\"></td>
    <td>";
    echo "<img src=\"img/tak.png\" width=\"25px\"> </td>";
}
else
{
    //button Usun oraz Ustaw jako główne
    echo " <td><input type=submit class=\"btn btn-danger\" value='Usuń' onclick=\"bootbox.confirm('Czy chcesz usunąć stanowisko<b> " . $tablica_jednostka['nazwa'] . " | " . $tablica_stanowisko['nazwa'] . "</b>  ?', function(result){ if (result==true) {window.location.href='modyfikuj/usun_stanowisko.php?id=" . $tablica_stanowiska['id'] . "&id_pracownika=".$id."&oddzial=".$tablica_stanowiska[id_jednostki_organizacyjnej]."'}; });\" class=\"myButton2\"></td>
    <td> <input type=submit class=\"btn btn-info\" value='Ustaw' onclick=\"bootbox.confirm('Czy chcesz ustawić stanowisko <b> " . $tablica_jednostka['nazwa'] . " | " . $tablica_stanowisko['nazwa'] . "</b>  jako główne ?', function(result){ if (result==true) {window.location.href='modyfikuj/ustaw_glowne.php?id=" . $tablica_stanowiska['id'] . "&id_pracownika=".$id. "'}; });\" class=\"myButton2\"></td>";
}}
    echo "</tr>";
}
    ?>
    </table>


    <?php
    $_SESSION[pracownik]=$tablica[id];
    echo "<div>".$_SESSION['alert']."</div>";
    unset($_SESSION['alert']);
    }
    else
    {
        //jesli warunki zalogowania nie są spełnione, wyślij do strony logout.php
        header('location: logout.php');
        exit();
    }
    //LOGOWANIE - SPRAWDZENIE - STOP
    ?>
</div>


<?php
// **** okno dialogowe - start ****
    echo "<div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\">
        <div class=\"modal-dialog\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                    <h4 id=\"exampleModalLabel\">Aby dodać, wybierz oddział oraz stanowisko: <br /></h4><h4><b>".$tablica_hr[imie]." ".$tablica_hr[nazwisko]."</b></h4>
                </div>
                <div class=\"modal-body\">";
                    echo "
                    <form action=\"modyfikuj/dodaj_stanowisko.php\" method='post' name='" . $tablica[id] . "'>";
// **** select oddzialy start ****
                    echo "
                    <select name='oddzial' class='form-control selectpicker' data-live-search=\"true\">
                    <option value ='0'>- - wybierz oddzial- -</option>";

                    //select z listą oddziałów, pomijając Doradców PHP oraz inne zbędne
                    $zapytanie_oddzialy = "SELECT * FROM jednostki_organizacyjne_ewidencja WHERE nazwa NOT LIKE '.%' AND status=1 ORDER BY nazwa ";
                    $wynik_oddzialy = $db2->query($zapytanie_oddzialy);
                    $ilosc_oddzialy = $wynik_oddzialy->num_rows;
                    for ($b = 0; $b < $ilosc_oddzialy; $b++)
                    {
                        $tablica_oddzialy = $wynik_oddzialy->fetch_assoc();
                        echo "
                        <option value = '".$tablica_oddzialy[id]."' > ".$tablica_oddzialy[nazwa]." </option > ";
                    }

                    echo " </select > <br /><br />

<!--// **** select oddzialy end **** -->

<!--// // **** select stanowiska start **** -->

                    <select name='stanowisko' class='form-control selectpicker' data-live-search=\"true\">
                    <option value ='0' >- - wybierz stanowisko- -</option>";
                    //select z listą oddziałów
                    $zapytanie_stanowiska = "SELECT * FROM stanowiska_ewidencja WHERE status=1 AND usunieto=0 ORDER BY nazwa ";
                    $wynik_stanowiska = $db2->query($zapytanie_stanowiska);
                    $ilosc_stanowiska = $wynik_stanowiska->num_rows;
                    for ($c = 0; $c < $ilosc_stanowiska; $c++)
                    {
                        $tablica_stanowiska = $wynik_stanowiska->fetch_assoc();
                        echo "
                        <option value = '".$tablica_stanowiska[id]."' > ".$tablica_stanowiska[nazwa]." </option >";
                    }
                    echo "<select />

<!-- // **** select stanowiska end **** -->

                    <input type='hidden' value='".$tablica[id]."' name='id'>
               </div>
               <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Anuluj</button>
                    <button type=\"submit\" class=\"btn btn-success\">Dodaj</button>
                    </form>
               </div>
         </div>
    </div>
</div>";

// **** okno dialogowe end ****
?>

<script src="js/classie.js"></script>

<script>
    //okno dialogowe
    $('#exampleModal').on('show.bs.modal', function (event) {
       var button = $(event.relatedTarget)
       var recipient = button.data('whatever')
       var modal = $(this)
       modal.find('.modal-title').text('New message to ' + recipient)
       modal.find('.modal-body input').val(recipient)
   })


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

</script>
<?php

$db2->close();
$db13->close();
$db2_hr->close();
$db2_capital->close();
?>


</body>
</html>
