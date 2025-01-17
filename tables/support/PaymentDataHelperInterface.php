<?php

interface PaymentDataHelperInterface
{
    public function getUnderpayments(int|false $paidSum = false, int|false $requiredSum = false): int;
    public function getOverpayments() : int;
    public function getUnpaidInvoices(): array;
    public function renderUnpaidInvoices() : string;
}