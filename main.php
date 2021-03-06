<?php
session_start();
//STRONA GŁÓWNA APLIKACJI
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
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Paweł Szymczyk" />
    <title>Panel Hotline</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/set1.css" rel="stylesheet"> <!--plugin do bajeranckich input -->

    <script src="js/jquery-3.1.1.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootbox.min.js"></script> <!--plugin js do okien dialogowych (potwierdzenie) -->
</head>
<body>

<div class="navbar navbar-default btn-variants navbar-fixed" role="navigation">
    <div style="text-align: center" class="col-lg-8 col-lg-offset-2 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        <h1 class="font_logo ">PANEL HOTLINE</h1>
    </div>
  <div class="col-lg-1 col-lg-offset-1 col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-sm-6 col-sm-offset-3 col-xs-4 col-xs-offset-4" ><a data-toggle="tooltip" data-placement="bottom" title="Zalogowany:<?php  echo "    ".$name." ".$surname; ?>" role="button" class="btn btn-default btn-sm " style="margin-top: 15px" href="logout.php">Wyloguj</a></div>
</div>

<div class="container">
    <?php
    //wyświetlenie wczesniej ustawionego alertu
    echo $_SESSION['alert2'];
    //wyczyszczenie zmiennej alert by po odświerzeniu nie pokazała sie ponowanie
    unset($_SESSION['alert2']);
    ?>
    <div>
        <form action='' method="post">
            <!-- Przycisk wyszukiwania - IMIE -->
            <div class="col-lg-4">  <span class="input input--hoshi"><section class="content">
					<input class="input__field input__field--hoshi" type="text" id="input-4" name="imie" autocomplete="off"/>
					<label class="input__label input__label--hoshi input__label--hoshi-color-1" for="input-4">
					<span class="input__label-content input__label-content--hoshi">Imię</span>
					</label>
				    </span>
            </div>
            <!-- Przycisk wyszukiwania - NAZWISKO -->
            <div class="col-lg-4"> <span class="input input--hoshi">
					<input class="input__field input__field--hoshi" type="text" id="input-4" name="nazwisko" autocomplete="off" autofocus />
					<label class="input__label input__label--hoshi input__label--hoshi-color-1" for="input-4">
					<span class="input__label-content input__label-content--hoshi">Nazwisko</span>
					</label>
		    		</span>
                    </div>
            <br />
        <div class="col-lg-4 col-lg-offset-0 col-md-3 col-md-offset-1 col-sm-4 col-xs-10 col-xs-offset-1"">
            <!-- Przycisk Wyszukaj -->
            <button type="submit" class="btn btn-default form-control">Wyszukaj</button>
        </div>
    </form>
</div>

    <table class="table table-striped" cellspacing='0' style='text-align: center'>
    <tr>
        <!-- nagłówki tabeli -->
        <th style="text-align: center">lp.</th>
        <th style="text-align: center">Imię</th>
        <th style="text-align: center">Nazwisko</th>
        <th style="text-align: center">Jednostka Org.</th>
        <th style="text-align: center">Czy aktywny</th>
        <th style="text-align: center">Odblokuj CRM</th>
        <th style="text-align: center">Przepnij</th>
        <th style="text-align: center">Modyfikuj</th>
        <th style="text-align: center">Domena</th>

    </tr>

    <?php
    //jesli istnieje zmienna imie OR naziwsko
    if(isset($_POST['imie'])||($_POST['nazwisko']))
    {
        //jesli zmienna imie AND naziwsko jest pusta
        if(($_POST['imie']=="") && ($_POST['nazwisko']==""))
        {
            //alert informujące, że nic nie zostało wpisane
            $_SESSION['alert'] = '<div class="alert alert-danger" style="margin-top: 30px">Nic nie wpisano.</div>';}
        else
        {
            $_SESSION['name_back'] = $_POST['imie'];
            $_SESSION['surname_back'] = $_POST['nazwisko'];

            //licznik
            $lp = 0;
            //podstawianie zmiennych POST pod krótsze naszwy
            $imie = $_POST['imie'];
            $nazwisko = $_POST['nazwisko'];
            //zapytanie zwracające wyszukiwane wyniki
            $zapytanie = "SELECT *  FROM jednostki_organizacyjne_ewidencja, uzytkownicy_ewidencja WHERE jednostki_organizacyjne_ewidencja.id = uzytkownicy_ewidencja.id_jednostki_organizacyjnej AND nazwisko LIKE '$nazwisko%' AND imie LIKE '$imie%' AND imie IS NOT NULL" ;
            if ($wynik = $db2->query($zapytanie))
            {
                $ilosc = $wynik->num_rows;
                $licznik = $ilosc;

                if ($ilosc<1)
                {
                    //jesli nic nie znaleziono, wyświetl alert informujący
                    $_SESSION['alert'] = '<div class="alert alert-danger" style="margin-top: 30px">Nie znaleziono pasującego użytkownika w systemie.</div>';

                }
                else
                {
                     //wypisz znalezione wyniki za pomocą pętli for
                    for ($p =0; $p < $ilosc; $p++)
                    {
                        $tablica = $wynik->fetch_assoc();
                        $pesel = $tablica['pesel'];
                        $idi = $tablica['id'];
                        //zapyranie sprawdzające użytkownika w bazie HR
                        $zapytanie_hr = "SELECT * FROM pracownicy_ewidencja WHERE id LIKE '$idi'";
                        $wynik_hr = $db2_hr->query($zapytanie_hr);
                        $tablica_hr = $wynik_hr->fetch_assoc();
                        //status czy pracuje w bazie HR
                        $pracuje = $tablica_hr['czy_pracuje'];
                        //usunięcie kropki z loginu w celu użycia w sambie
                        $login = str_replace(".", "", $tablica_hr['login']);
                        $id_user = $tablica_hr['id'];
                        if ($pracuje == 0)
                        {
                            //jesli osoba ma ustawione nie pracuje w HR, zakończ te działanie pętli i zacznik od nowa (nie wyświetli rekordu)
                            continue;
                        }
                        $limit = $limit + 1;
                        //limit wyświetlen na stronie (zbyt duzo wyświetlen --> długie działanie)
                        if ($limit == 26)
                        {
                            $_SESSION['alert'] = '<div class="alert alert-danger">Znaleziono więcej niż 25 wyników, zawęź kryteria wyszukiwania.</div>';
                            //przerwanie działania pętli po wyświetleniu okreslonej liczby wyników
                            break;
                        }
                        $lp = $lp + 1;
                        //wrzuć dane do tabeli
                        echo "
                            <tr>
                            <td>" . $lp . "</td>
                            <td>" . $tablica['imie'] . "</td>
                            <td>" . $tablica['nazwisko'] . "</td>
                            <td>" . $tablica['nazwa'] . "</td>
                            <td>";
                            //wyświetl status czy zablokowany , podstawiając odpowiedni obrazek
                            if ($tablica['status'] == 1)
                            {
                                echo "<img src=\"img/ok.png\" width='25px'>";
                            }
                                else
                            {
                                echo "<img src=\"img/no.png\" width='25px'>";
                            }

                            echo "</td>";
                            //Przycisk odblokuj, wraz z wymaganym potwierdzeniem
                            echo "<td><input type=submit class=\"btn btn-danger\" value='Odblokuj' onclick=\"bootbox.confirm('Czy chcesz odblokować użytkownika<b> " . $tablica['imie'] . " " . $tablica['nazwisko'] . "</b>  i zmienić hasło na PESEL?', function(result){ if (result==true) {window.location.href='odblokuj.php?pesel=" . $tablica['pesel'] . "&id=".$tablica['id']."'}; });\" class=\"myButton2\"></td>";
                            //Przycisk przepnij, wyświetlający okno dialogowe
                            echo "<td><input type=submit class=\"btn btn-warning\" value='Przepnij' data-toggle=\"modal\" class=\"myButton2\" data-target=\"#exampleModal" . $tablica[id] . "\">
                            <td><a class=\"btn btn-success \" href='modyfikuj.php?id=".$tablica[id]."' \">Modyfikuj</a>
                            </td>
                            <td><input type=submit class=\"btn btn-info\" value='Zresetuj hasło' onclick=\"bootbox . confirm('Czy chcesz zmienić hasło domenowe dla użytkownia<b> " . $tablica['imie'] . " " . $tablica['nazwisko'] . "</b>  na Capital1?', function (result){ if (result == true) { window . location . href = 'sambapass.php?login=" . $login ."&id=".$id_user."'};});\" class=\"myButton2\"></td>
                            </td>";

// **** okno dialogowe - start ****
                             echo "<div class=\"modal fade\" id=\"exampleModal" . $tablica[id] . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\">
                                        <div class=\"modal-dialog\" role=\"document\">
                                             <div class=\"modal-content\">
                                                  <div class=\"modal-header\">
                                                     <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                                                     <h4 id=\"exampleModalLabel\">Wybierz oddział docelowy dla użytkownika: <br /></h4><h4><b>".$tablica_hr[imie]." ".$tablica_hr[nazwisko]."</b></h4>Login: ".$tablica_hr[login]."

                                                   </div>
                                                   <div class=\"modal-body\">";

echo "<b><h5>Obecne stanowiska:</h5></b>";



        //wyświetl wszystkie aktwne stanowiska u pracownika
        $zapytanie_stanowiska = "SELECT *  FROM pracownicy_stanowiska WHERE id_pracownika='$tablica[id]' AND status=1";
        $wynik_stanowiska = $db2_hr->query($zapytanie_stanowiska);
        $ilosc_stanowiska = $wynik_stanowiska->num_rows;
        for ($i = 0; $i < $ilosc_stanowiska; $i++) {
            $tablica_stanowiska = $wynik_stanowiska->fetch_assoc();
            echo "<p><span class=\"glyphicon glyphicon-hand-right\" aria-hidden=\"true\"></span>  &nbsp;";
            if ($tablica_stanowiska[czy_glowne]==1){
                echo"<b>";
            }
            //wyświetl nazwe jednostki organizacyjnej
            $zapytanie_jednostka = "SELECT * FROM jednostki_organizacyjne_ewidencja WHERE id='$tablica_stanowiska[id_jednostki_organizacyjnej]'";
            $wynik_jednostka = $db2->query($zapytanie_jednostka);
            $ilosc_jednostka = $wynik_jednostka->num_rows;
            $tablica_jednostka = $wynik_jednostka->fetch_assoc();


            echo $tablica_jednostka['nazwa'] . " | ";
            //wyświetl nazwe stanowiska
            $zapytanie_stanowisko = "SELECT * FROM stanowiska_ewidencja WHERE id='$tablica_stanowiska[id_stanowiska]'";
            $wynik_stanowisko = $db2->query($zapytanie_stanowisko);
            $ilosc_stanowisko = $wynik_stanowisko->num_rows;
            $tablica_stanowisko = $wynik_stanowisko->fetch_assoc();

            //jeśli stnowisko jest ustawione jako główne to pogrub je
            echo $tablica_stanowisko['nazwa']."</p>";
            if ($tablica_stanowiska[czy_glowne]==1){
                echo"</b>";
            }
        }



                                                      echo " <hr /><b>Oddział docelowy:</b>
                                                     <form action=\"przepnij.php\" method='post' name='" . $tablica[id] . "'>";
// **** select oddzialy start ****
                                                     echo "
                                                     <select name='oddzial' class='form-control'>
                                                     <option value ='0'>- - wybierz oddzial- -</option>";
                                                        //wyświetl select z lista oddziałów
                                                        $zapytanie_oddzialy = "SELECT * FROM jednostki_organizacyjne_ewidencja WHERE status=1 AND nazwa NOT LIKE '.%' ORDER BY nazwa ";
                                                        $wynik_oddzialy = $db2->query($zapytanie_oddzialy);
                                                        $ilosc_oddzialy = $wynik_oddzialy->num_rows;
                                                        for ($b = 0; $b < $ilosc_oddzialy; $b++)
                                                        {
                                                            $tablica_oddzialy = $wynik_oddzialy->fetch_assoc();
                                                            $id_oddzialu = $tablica_oddzialy[id];
                                                            $nazwa_oddzialu = $tablica_oddzialy[nazwa];
                                                            echo "
                                                            <option value = '".$id_oddzialu."' > ".$nazwa_oddzialu." </option > ";
                                                        }

                                                        echo " </select > <br />
<!--// **** select oddzialy end **** -->

<!--// // **** select stanowiska start **** -->
                                                        <select name='stanowisko' class='form-control'>
                                                        <option value ='0' >- - wybierz stanowisko- -</option>";
                                                        //wyświetl select z listą stanowisk
                                                        $zapytanie_stanowiska = "SELECT * FROM stanowiska_ewidencja WHERE status=1 AND usunieto=0 ORDER BY nazwa ";
                                                        $wynik_stanowiska = $db2->query($zapytanie_stanowiska);
                                                        $ilosc_stanowiska = $wynik_stanowiska->num_rows;
                                                        for ($c = 0; $c < $ilosc_stanowiska; $c++)
                                                        {
                                                            $tablica_stanowiska = $wynik_stanowiska->fetch_assoc();
                                                            $id_stanowiska = $tablica_stanowiska[id];
                                                            $nazwa_stanowiska = $tablica_stanowiska[nazwa];
                                                            echo "
                                                            <option value = '".$id_stanowiska."' > ".$nazwa_stanowiska." </option >";
                                                        }
                                                        //ukryte podel id które za pomocą post przeniesie do następnej strony iz uzytkownika
                                                        echo "<select />
<!-- // **** select stanowiska end **** -->
                                                        <input type='hidden' value='".$tablica[id]."' name='id'>

                                                       </div>
                                                  <div class=\"modal-footer\">
                                                  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Anuluj</button>
                                                  <button type=\"submit\" class=\"btn btn-warning\">Przepnij</button>
                                                  </form>
                                            </div>
                                      </div>
                                </div>
                             </div>";
// **** okno dialogowe end ****

                        echo "</tr>";
                    }

                    if ($lp==0)
                    {
                        //alert, informujący o nie znalezieniu użytkownika
                        $_SESSION['alert'] = '<div class="alert alert-danger" style="margin-top: 30px">Nie znaleziono pasującego użytkownika w systemie.</div>';
                    }
                }
            }
            else
            echo "error";
        }
    }
    else
    {
        //jesli nic nie jest wyświetlone, wyświetl warning o wyszukaniu pracowników
        echo "<div class=\"alert alert-warning\" style='margin-top: 65px'>Aby wyświetlić liste, wyszukaj pracowników</div>";
    }
    ?>
    </table>

    <?php
    //wyświetl zmienną alert
    echo "<div>".$_SESSION['alert']."</div>";
    unset($_SESSION['alert']);
}
else
{
    //jesli pierwszy warunek nie został spełniony to prześlij to strony wylogowania
    header('location: logout.php');
    exit();
}
//LOGOWANIE - SPRAWDZENIE - STOP
    ?>
</div>
<script src="js/classie.js"></script>
<!-- javascript pozwalajacy wyświetlic okno dialogowe do przepinania -->
<script>
    $('#exampleModal').on('show.bs.modal', function (event) {
       var button = $(event.relatedTarget) // Button that triggered the modal
       var recipient = button.data('whatever')
       var modal = $(this)
       modal.find('.modal-title').text('New message to ' + recipient)
       modal.find('.modal-body input').val(recipient)
   })

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(function() {
        if (!String.prototype.trim) {
            (function() {
                var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                String.prototype.trim = function() {
                    return this.replace(rtrim, '');
                };
            })();
        }
        [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
            // in case the input is already filled..
            if( inputEl.value.trim() !== '' ) {
                classie.add( inputEl.parentNode, 'input--filled' );
            }

            // events:
            inputEl.addEventListener( 'focus', onInputFocus );
            inputEl.addEventListener( 'blur', onInputBlur );
        } );

        function onInputFocus( ev ) {
            classie.add( ev.target.parentNode, 'input--filled' );
        }

        function onInputBlur( ev ) {
            if( ev.target.value.trim() === '' ) {
                classie.remove( ev.target.parentNode, 'input--filled' );
            }
        }
    })();
</script>
<?php
//zamknij połączenie z bazami danych
$db2->close();
$db13->close();
$db2_hr->close();
$db2_capital->close();

?>

</body>
</html>
