<?php 

class ActionExecuter
{
    private int|null $action;
    private int|null $id;
    private int|null $sort;
    public function __construct(private MysqlManager $mysqlManager, array $get)
    {
        $this->action =isset($_GET['action']) ? intval($mysqlManager->sanitize($$_GET['action'])) : null;
        $this->id = isset($get['i']) ? intval($mysqlManager->sanitize($get['i'])) : null;
        $this->sort = isset($get['sort']) ? intval($mysqlManager->sanitize($get['sort'])) : null;
    }

    public function execute()
    {
        $contracts = [];
        if ($this->action === 5) {
            $contracts = $this->mysqlManager->getContracts($this->sort, ['id' => [$this->id,PDO::PARAM_INT], 'amount' => [10,PDO::PARAM_INT]]);
        } 
        else {
            $contracts = $this->mysqlManager->getContracts(0, ['id' => [$this->id,PDO::PARAM_INT]]);
        }
        return $contracts;
    }

    public function renderTable($dg_bgcolor)
    {
        $contracts = $this->execute();
        echo "<html><body bgcolor=$dg_bgcolor>";
        echo "<br>";
        echo "<table width=95%>";

        foreach ($contracts as $contract) {
            echo '<tr>';
            echo '<td>' . $contract[0] . '</td>';
            echo '<td>' . $contract[2];
            if ($contract[10] > 5) {
                echo ' ' . $contract[10];
            }
            echo '</td></tr>';
        }

        echo '</table></body></html>';
    }
}
