<?php
require_once ('config/config.php');
$id = $_POST['id'];
$oddzial = $_POST['oddzial'];
$stanowisko = $_POST['stanowisko'];
$one = 1;
$zapytanie_13 = "SELECT * FROM jednostki_organizacyjne_ewidencja WHERE id LIKE '$oddzial'";
$zapytanie_13_stanowisko = "SELECT * FROM stanowiska_ewidencja WHERE id LIKE '$stanowisko'";


       $zapytanie_stanowiska = "SELECT *  FROM pracownicy_stanowiska WHERE id_pracownika='$id' AND status=1";
        $wynik_stanowiska = $db2_hr->query($zapytanie_stanowiska);
        $ilosc_stanowiska = $wynik_stanowiska->num_rows;
        for ($i = 0; $i < $ilosc_stanowiska; $i++)
        {
            $tablica_stanowiska = $wynik_stanowiska->fetch_assoc();
            $id_ps = $tablica_stanowiska[id];
            $update_stanowiska = "UPDATE pracownicy_stanowiska SET status=0, czy_glowne=0 WHERE id LIKE '$id_ps'";
            $db2_hr->query($update_stanowiska);
        }

        $update_uzytkownicy = "UPDATE uzytkownicy_ewidencja SET id_jednostki_organizacyjnej='$oddzial', id_stanowiska='$stanowisko' WHERE id_pracownika='$id'";
        $db2->query($update_uzytkownicy);

        $zapytanie_powiazanie = "SELECT *  FROM pracownicy_stanowiska WHERE id_pracownika='$id' AND id_jednostki_organizacyjnej='$oddzial' AND id_stanowiska='$stanowisko'";
        $wynik_powiazanie = $db2_hr->query($zapytanie_powiazanie);
        $ilosc_powiazanie = $wynik_powiazanie->num_rows;

        if ($ilosc_powiazanie>0)
        {
            $update_stanowiska_2 = "UPDATE pracownicy_stanowiska SET status=1, czy_glowne=1 WHERE id_pracownika='$id' AND id_jednostki_organizacyjnej='$oddzial' AND id_stanowiska='$stanowisko'";
            $db2_hr->query($update_stanowiska_2);
        }
        else
        {
         $insert_powiazanie = "INSERT INTO `pracownicy_stanowiska` (`id`,`id_pracownika`,`id_jednostki_organizacyjnej`,`id_stanowiska`,`czy_glowne`,`status`) VALUES (NULL,'$id', '$oddzial','$stanowisko','$one','$one')";
         $db2_hr->query($insert_powiazanie);
        }
$wynik_13 = $db13->query($zapytanie_13);
$tablica_13 = $wynik_13->fetch_assoc();
$oddzial_crm1 = $tablica_13['crm1_oddzialy'];
$dzial_crm1 = $tablica_13['crm1_dzialy'];

$wynik_13_stanowisko = $db13->query($zapytanie_13_stanowisko);
$tablica_13_stanowisko = $wynik_13_stanowisko->fetch_assoc();
$stanowisko_crm1 = $tablica_13_stanowisko['crm1_stanowisko'];

$update_capital ="UPDATE cash_users SET id_oddzialu='$oddzial_crm1', id_dzialu='$dzial_crm1', stanowisko='$stanowisko_crm1' WHERE user_id='$id'";
$db2_capital->query($update_capital);


$_SESSION['alert2'] = '<div class="alert alert-success">Użytkownik został pomyślnie przpięty w CRM1 oraz CRM2.</div>';
header('location: main.php');
exit();
