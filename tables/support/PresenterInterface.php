<?php

/**
 * Interfece is responsible for retrieving and presenting simple data
 */
interface PresenterInterface
{
    public function getClients();
    public function getInvoices();
    public function getInvoiceItems();
    public function getPayments();
    public function renderTable($data, $headers);
    public function renderClientsTable();
    public function renderInvoicesTable();
    public function renderInvoiceItemsTable();
    public function renderPaymentsTable();
    public function getClientsIdsAndNames();
}