<?php

trait Sort
{
    /**
     * Add sorting by id to the SQL query.
     *
     * @param string $order 'ASC' for ascending, 'DESC' for descending
     * @return string
     */
    public function addSortById(string|false $order=false):string
    {
        
        if($order === false) {
            return '';
        }
        
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        return " ORDER BY id $order";
    }
}