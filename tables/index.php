<?php
require_once './support/Presenter.php';
require_once './support/PaymentDataHelper.php';

$db = new PDO('mysql:host=db;dbname=database', 'user', 'password');
$presenter = new Presenter($db);
?>
<html>
<head>
    <link rel="stylesheet" href="main.css" /> 
</head>
<body><nav style="height: 2rem; background-color:rgb(41, 39, 39); display:flex; justify-content:center; align-items:center;">
    <p style="font-weight:bold; color:white;">
        Jeśli jesteś tu pierwszy raz, to zacznij od konfiguracji bazy danych. Aplikacja uworzy tabele oraz wypelni je danymi.
    </p>
</nav>
<a href="./set-database" style="display: inline-block; margin: 2rem;">Konfiguruj bazę danych</a>
<div style="background-color: rgb(41, 39, 39); height:1rem; margin:2rem 0rem;" ></div>
<div>
<h1>Aplikacja</h1>
<section>

<h2 style="margin-bottom:0rem">Filtruj oraz sortuj wyniki</h2>
<i style="margin-bottom:2rem; display:inline-block">Przykładowe filtrowanie i opcje sortowania. 
    Faktury po terminie płatności są sortowane wedle użytkownika.</i>

  
       <form>
        <div>
        <label for="client">Klient</label>
        <select name="id" id="client">
            <option value="">Wybierz klienta</option>
            <?php
            $clients = $presenter->getClientsIdsAndNames();
            
            foreach ($clients as $client) {
                echo '<option value="' . $client['id'] . '">' . $client['nazwa_przedsiebiorcy'] . '</option>';
            }
            ?>
        </select>
        </div>
        <div>
        <label for="sort">Sortuj po ID</label>
        <select name="sort" id="sort">
            <option value="ASC">Wzrastająco</option>
            <option value="DESC">Malejąco</option>
           
        </select>
          
    </div>
    <button>Zatwierdź</button>
    </form>
    

</section>
<section>
<h2>Przykładowe dane</h2>
<strong>Kwoty są wpisane jako liczby całkowite (kwota*100) co wynika z problemu z floating point precision php - zob.
    <a href=https://www.php.net/manual/en/language.types.float.php>dokumentację</a>
     </strong>
<?php
echo $presenter->renderClientsTable();
echo $presenter->renderInvoicesTable();
echo $presenter->renderInvoiceItemsTable();
echo $presenter->renderPaymentsTable();


?>
</section>

<section>
<h2>Nadpłaty</h2>
<?php
$payments = new PaymentDataHelper($db, $presenter);

echo '<p><strong>Nadpłaty: </strong> ';
echo $payments->getOverpayments();
echo '</p>';

?>
</section>
<section>
<h2>Niedopłaty</h2>
<?php
echo '<p><strong>Niedopłaty: </strong> ';
echo $payments->getUnderpayments();
echo '</p>';

?>
</section>
<section>
<h2>Faktury po terminie płatności</h2>
<?php
echo $payments->renderUnpaidInvoices();
?>
</section>
</div>


</body>

</html>