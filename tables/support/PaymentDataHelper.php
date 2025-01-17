<?php
require_once 'PresenterInterface.php';
require_once 'PaymentDataHelperInterface.php';
require_once __DIR__.'/../traits/Filter.php';

/**
 * Class is responsible for getting payment data and calculates it;
 */
class PaymentDataHelper implements PaymentDataHelperInterface
{
    use Filter;
    public int $paidSum;
    public int $requiredSum;
    private string $where;
    public function __construct(private PDO $db,private PresenterInterface $presenter)
    {
        $sanitize = $this->getSanitizedParams(['id']);
        $changeKey = $this->replaceKeyWithString($sanitize, 'id', 'klient_id');
        $this->where = $this->generateWhereClause($changeKey);
        $this->paidSum = $this->getPaidSum();
        $this->requiredSum = $this->getRequiredSum();  
    }
    /**
     * Get paid sum from payments.
     */
    private function getPaidSum(string $additionalWhereClasue=''):int
    {
        $where=$this->where.' '.trim($additionalWhereClasue);
        $sql="SELECT SUM(kwota) FROM platnosci $where";
        return $this->db->query($sql)->fetchColumn();
    }
    /**
     * Get required sum from invoices.
     */
    private function getRequiredSum(string $additionalWhereClasue='') :int
    {
        $where=$this->where.' '.trim($additionalWhereClasue);
        $sql="SELECT SUM(suma_brutto) FROM faktury $where";
        return $this->db->query($sql)->fetchColumn();
    }


    public function getUnderpayments(int|false $paidSum=false, int|false $requiredSum=false) :int
    {
        $payments = $paidSum!==false ? $paidSum : $this->paidSum;
        $obligations = $requiredSum!==false ? $requiredSum : $this->requiredSum;
        $underpayments=$obligations-$payments;
        return $underpayments > 0 ? $underpayments : 0;

    }
    private function getUnderpaymentsForClient(int $clientID): int{

        $clause="klient_id=$clientID";
        $clause = $this->where === ''? 'WHERE '.$clause : 'AND '.$clause;
        $additionalWhereClause= !$this->checkIfClauseAlreadyExists($clause,$this->where) ? $clause:'';

        $reqClause='termin_platnosci <= NOW()';
        $reqClause=$this->where === '' && $additionalWhereClause==='' ? 'WHERE '.$reqClause : 'AND '.$reqClause;
        $reqSumAdditionalWhereClause =!$this->checkIfClauseAlreadyExists($reqClause,$this->where) ? $reqClause :'';

        $requiredSum = $this->getRequiredSum($additionalWhereClause.' '.$reqSumAdditionalWhereClause);
        $paidSum = $this->getPaidSum($additionalWhereClause);
        $underpayments = $this->getUnderpayments($paidSum,$requiredSum);
        return $underpayments;
    }

    public function getOverpayments() : int
    {
        $payments = $this->paidSum;
        $obligations = $this->requiredSum;
        $overpayments=$payments-$obligations;
        return $overpayments > 0 ? $overpayments : 0;
    }

    public function getUnpaidInvoices() : array
    {
        $invoices = $this->presenter->getInvoices();
        $invoices = array_filter($invoices, function($invoice){
            $endDate = new DateTime($invoice['termin_platnosci']);
            $today = new DateTime();
            return $endDate < $today;
        });
        $users=array_map(function($invoice){
            return $invoice['klient_id'];
        },$invoices);
        $users=array_unique($users);
        
        foreach($users as $user){

            $underpayments = $this->getUnderpaymentsForClient($user);
            
            $invoicesWithPayment= [];
            if($underpayments>0){
                $userInvoices = array_filter($invoices, function($invoice) use ($user){
                    return $invoice['klient_id']==$user;
                });

                array_multisort(array_column($userInvoices,'termin_platnosci'),SORT_DESC,$userInvoices);

                $debt =  $underpayments;
                foreach($userInvoices as $userInvoice)
                {
                    $userInvoice['do_zaplaty'] = 0;
                    if($debt >0)
                    {
                        $debt-=$userInvoice['suma_brutto'];
                        $userInvoice['do_zaplaty'] = $debt > 0 ? $userInvoice['suma_brutto'] : $underpayments;
                        array_push($invoicesWithPayment,$userInvoice);
                    }
                }
            }
        }
        return $invoicesWithPayment;
    }

    public function renderUnpaidInvoices() : string
    {
        $invoices = $this->getUnpaidInvoices();
     
        $headers = array_keys($invoices[0]);
        return $this->presenter->renderTable($invoices, $headers);
    }
}