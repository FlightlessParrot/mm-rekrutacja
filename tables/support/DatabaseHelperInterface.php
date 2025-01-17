<?php
/**
 * Interface is responsible for setting up database and inserting sample data
 */
    interface DatabaseHelperInterface
    {
        public function createTables() : string;
        public function insertSampleData() : string;
    }