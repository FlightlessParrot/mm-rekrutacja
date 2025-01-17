<?php

require_once 'PresenterInterface.php';
require_once __DIR__.'/../traits/Filter.php';
require_once __DIR__.'/../traits/Sort.php';



class Presenter implements PresenterInterface
{
    use Filter, Sort;
    /**
     * @var string $sort
     * SORT BY id ASC or DESC command
     */
    private string $sort;
    public function __construct(private PDO $db)
    {
        $this->sort=$this->addSortById($_GET['sort'] ?? false);
    }
    private function getIdAsKlientIdWhereClouse():string
    {
        $sanitize = $this->getSanitizedParams(['id']);
        $changeKey = $this->replaceKeyWithString($sanitize, 'id', 'klient_id');
        $where = $this->generateWhereClause($changeKey);
        return $where;
    }
    public function getClients():array
    {
        $sanitize = $this->getSanitizedParams(['id']);
        $where = $this->generateWhereClause($sanitize);
        
        $stmt = $this->db->query("SELECT * FROM klienci $where $this->sort");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoices():array
    {
        $where = $this->getIdAsKlientIdWhereClouse();
        $stmt = $this->db->query("SELECT * FROM faktury $where $this->sort");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceItems():array
    {
        $sanitize = $this->getSanitizedParams(['id']);
        $changeKey = $this->replaceKeyWithString($sanitize, 'id', 'faktury.klient_id');
        $where = $this->generateWhereClause($changeKey);
        $stmt = $this->db->query("SELECT pozycje_faktury.*, faktury.numer as numer_faktury 
        FROM pozycje_faktury LEFT JOIN faktury ON pozycje_faktury.faktura_id = faktury.id 
        $where $this->sort");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPayments():array
    {
        $where = $this->getIdAsKlientIdWhereClouse();
        $stmt = $this->db->query("SELECT * FROM platnosci $where $this->sort");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getClientsIdsAndNames():array
    {
        $stmt = $this->db->query("SELECT id,nazwa_przedsiebiorcy FROM klienci $this->sort");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function renderTable($data, $headers) : string
    {
        $html = '<table border="1"><thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
    

    public function renderClientsTable() : string
    {
        $clients = $this->getClients();
        $headers = array_keys($clients[0]);
        return $this->renderTable($clients, $headers);
    }

    public function renderInvoicesTable() : string
    {
        $invoices = $this->getInvoices();
        $headers = array_keys($invoices[0]);
        return $this->renderTable($invoices, $headers);
    }

    public function renderInvoiceItemsTable() : string
    {
        $invoiceItems = $this->getInvoiceItems();
        $headers = array_keys($invoiceItems[0]);
        return $this->renderTable($invoiceItems, $headers);
    }

    public function renderPaymentsTable() : string
    {
        $payments = $this->getPayments();
        $headers = array_keys($payments[0]);
        return $this->renderTable($payments, $headers);
    }

}
