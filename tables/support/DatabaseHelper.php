<?php

require_once 'DatabaseHelperInterface.php';
/**
 * Class is responsible for setting up database and inserting sample data
 * 
 * @param string $servername
 * @param string $username
 * @param string $password
 * @param string $dbname
 */
class DatabaseHelper implements DatabaseHelperInterface
{
   
    private $conn;

    public function __construct(private $servername, private $username,private $password,private $dbname)
    {
            // Create connection

            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
        
    }
  
    /**
     * Create tables
     * 
     * @return string
     * return message for user as a string
     */
    public function createTables() : string
    {
        try {
            $sql = "
    CREATE TABLE IF NOT EXISTS klienci (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nazwa_przedsiebiorcy VARCHAR(255) NOT NULL,
        numer_konta_bankowego VARCHAR(255) NOT NULL,
        nip VARCHAR(20) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS faktury (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numer VARCHAR(50) NOT NULL,
        data_wystawienia DATE NOT NULL,
        termin_platnosci DATE NOT NULL,
        suma_brutto INT NOT NULL,
        klient_id INT,
        FOREIGN KEY (klient_id) REFERENCES klienci(id)
    );

    CREATE TABLE IF NOT EXISTS pozycje_faktury (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nazwa_produktu VARCHAR(255) NOT NULL,
        ilosc INT NOT NULL,
        cena INT NOT NULL,
        faktura_id INT,
        FOREIGN KEY (faktura_id) REFERENCES faktury(id)
    );

    CREATE TABLE IF NOT EXISTS platnosci (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tytul_platnosci VARCHAR(255) NOT NULL,
        kwota INT NOT NULL,
        data_wplaty DATE NOT NULL,
        numer_konta_bankowego_wplaty VARCHAR(255) NOT NULL,
        klient_id INT,
        FOREIGN KEY (klient_id) REFERENCES klienci(id)
        );
        ";

            $this->conn->exec($sql);
            return "Tables created successfully";
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        }

        /**
         * Insert sample data
         * 
         * @return string
         * return message for user as a string
         */
        public function insertSampleData() : string
        {
        // Insert sample data
        $sql = "
        INSERT INTO klienci (nazwa_przedsiebiorcy, numer_konta_bankowego, nip) VALUES
        ('Firma A', '12345678901234567890123456', '1234567890'),
        ('Firma B', '23456789012345678901234567', '0987654321');

        INSERT INTO faktury (numer, data_wystawienia, termin_platnosci, suma_brutto, klient_id) VALUES
        ('F001', '2024-01-01', '2024-01-15', 100000, 1),
        ('F002', '2024-01-05', '2024-01-20', 150000, 2),
        ('F003', '2024-01-07', '2024-01-22', 100000, 1),
        ('F004', '2024-01-10', '2024-01-25', 150000, 2);

        INSERT INTO pozycje_faktury (nazwa_produktu, ilosc, cena, faktura_id) VALUES
        ('Produkt A', 10, 5000, 1),
        ('Produkt A', 20, 2500, 1),
        ('Produkt B', 5, 10000, 2),
        ('Produkt C', 10, 10000, 2),
        ('Produkt D', 10, 5000, 3),
        ('Produkt D', 20, 2500, 3),
        ('Produkt E', 5, 10000, 4),
        ('Produkt F', 10, 10000, 4);

        INSERT INTO platnosci (tytul_platnosci, kwota, data_wplaty, numer_konta_bankowego_wplaty, klient_id) VALUES
        ('Platnosc za F001', 100000, '2024-01-10', '12345678901234567890123456', 1),
        ('Platnosc za F002', 150000, '2024-01-15', '23456789012345678901234567', 2),
        ('Platnosc za F003', 110000, '2024-01-10', '12345678901234567890123456', 1),
        ('Platnosc za F004', 140000, '2024-01-15', '23456789012345678901234567', 2);

    ";
        try {
            // Execute query
            $this->conn->exec($sql);
           return 'Sample data inserted successfully';
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
            
        }
    }

    public function cleanDatabase() : string
    {
        try {
            $sql = "
            DROP TABLE IF EXISTS nadplaty;
            DROP TABLE IF EXISTS platnosci;
            DROP TABLE IF EXISTS pozycje_faktury;
            DROP TABLE IF EXISTS faktury;
            DROP TABLE IF EXISTS klienci;
            ";
            $this->conn->exec($sql);
            return "Database has been cleaned";
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
// Close connection
// $conn = null;
