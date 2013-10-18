<?php
/**
 * Search
 * @package searches
 * @author Milan Trajkovic <milantrax@gmail.com>
 * @version 1.0
 * @link http://www.nailgunapp.com
 * @copyright Copyright (c) 2013, Milan Trajkovic
 * @access public
 */

class Search extends Database {

    public $query;
    public $data;

    /**
     * Search tasks by term
     * @param string $term search term.
     * @return array Array with task data.
     */
    public function searchAllTasks($term, $account) {
        $query = "SELECT * FROM tasks WHERE title LIKE '%$term%' AND status > 0 AND account=".$account." ORDER BY expire ASC";
        //$query = "SELECT * FROM tasks WHERE title LIKE '%$term%' OR description LIKE '%$term%' AND status > 0 ORDER BY expire ASC";
        $data = $this->select($query);
        return $data;
    }

    /**
     * Count search tasks
     * @return array Array with tasks data.
     */
    public function countSearchAllTasks($term) {
        $query = "SELECT id FROM tasks WHERE title LIKE '%$term%' AND status > 0";
        $data = $this->select($query);
        return $this->getNumRows($data);
    }
	
}