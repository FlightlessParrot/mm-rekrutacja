<?php

require_once 'MysqlManagerInterface.php';

class MysqlManager implements MysqlManagerInterface
{
   
    /**
     * ATTENTION
     * Refactoring code to version above PHP 5 requires use of mysqli or PDO object. 
     * The legacy code does not include parts responsible for setting the connection. 
     * As a result this change must be implemented together with the code responsible for setting connection.
     *  
     */
    public function __construct(private PDO $db)
    {
       
    }
   
    public function sanitize($value): string
    {
        return htmlspecialchars(strip_tags($value));
    }
    /**
     * Get contracts from database
     * @param int $sort
     * @param array $params
     * params should have format ['key' => ['value',PDO::PARAM_TYPE]]
     * @return array
     */
    public function getContracts(int $sort, array $params): array
    {
        $sql="SELECT * FROM contracts";
        $sql .= $this->addWhere($params);
        $sql = $this->addOrderByToSql($sql, $sort);
        $stmt = $this->db->prepare($sql);
        
        $this->bindValue($params,$stmt);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    private function addWhere(array $params) : string
    {
        if(empty($params)){
            return '';
        }

        $where = ' WHERE';
        foreach ($params as $key => $value) {
            $where .= " $key = :$key AND";
        }
        return rtrim($where, 'AND');
    }
    private function bindValue(array $paramsWithPdoType, PDOStatement $stmt) : void
    {
        foreach ($paramsWithPdoType as $key => $value) {
            $stmt->bindValue(":$key", $value[0], $value[1]);
        }
    }
    public function addOrderByToSql(string $sql, int $sort): string
    {
        $sql_orderby = '';
        switch ($sort) {
            case 1:
                $sql_orderby = "ORDER BY 2, 4 DESC";
                break;
            case 2:
                $sql_orderby = "ORDER BY 10";
                break;
            default:
                $sql_orderby = "ORDER BY id";
        }
        return $sql . ' ' . $sql_orderby;
    }
   
}